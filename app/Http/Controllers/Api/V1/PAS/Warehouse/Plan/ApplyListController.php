<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Plan;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\PAS\Purchase\WarehousingApply;
use App\Models\PAS\SaleReturnInWarehouse;
use App\Models\PAS\Warehouse\AllotCart;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ApplyListController extends ApiController
{


    public function run(Request $request)
    {
        $status = $request->get('status');
        //status 3个数据的status 值不一样可能需要处理
        $list = [];

        $query = WarehousingApply::query();
        if($status){
            $query->where('status', '=', $status);
        }
        //'applyGoods.sku'
        $inList = $query->with('applyGoods','applyGoods.warehouse')->get()->toArray();
        foreach ($inList as &$value){
            $value['source_type'] = WarehouseInCard::TYPE_BUY;
            $hArr = [];
            foreach ($value['apply_goods'] as $item){
                if(!isset($item['warehouse']) || empty($item['warehouse'])) continue;
                $hArr[] = $item['warehouse']['title'];
            }
            $value['house'] = implode(',', $hArr);
        }
        $list = array_merge($inList, $list);

        $query = SaleReturnInWarehouse::query();
        if($status){
            $query->where('status', '=', $status);
        }
        //'applyGoods.sku'
        $backList = $query->with(['in_goods','in_goods.warehouse',])->get()->toArray();
        foreach ($backList as &$value){
            $value['source_type'] = WarehouseInCard::TYPE_BACK;
            $hArr = [];
            foreach ($value['in_goods'] as $item){
                if(!isset($item['warehouse']) || empty($item['warehouse'])) continue;
                $hArr[] = $item['warehouse']['title'];
            }
            $value['house'] = implode(',', $hArr);
        }
        $list = array_merge($backList, $list);

        $query = AllotCart::query();
        if($status){
            $query->where('status', '=', $status);
        }
        //['id', 'created_at'] 'cardGoods.sku' 'cardGoods.goodsInfo'
        $allotList = $query->with(['cardGoods','cardGoods.warehouse'])->get()->toArray();
        foreach ($allotList as &$value){
            $value['source_type'] = WarehouseInCard::TYPE_ALLOT;
            $hArr = [];
            foreach ($value['card_goods'] as $item){
                if(!isset($item['warehouse']) || empty($item['warehouse'])) continue;
                $hArr[] = $item['warehouse']['title'];
            }
            $value['house'] = implode(',', $hArr);
        }
        $list = array_merge($allotList, $list);

        //根据字段last_name对数组$data进行降序排列
        $last_names = array_column($list,'created_at');
        array_multisort($last_names,SORT_DESC,$list);
        return $list;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\OutCard;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\PAS\Purchase\WarehousingApply;
use App\Models\PAS\SaleOutWarehouse;
use App\Models\PAS\Warehouse\AllotCart;
use Illuminate\Http\Request;


class ApplyListController extends ApiController
{


    public function run(Request $request)
    {
        $status = $request->get('status'); //状态 0带出库1、部分出库2、已出库3、草稿
        $list = [];

        $query = SaleOutWarehouse::query();
        if($status){
            $query->where('status', '=', $status);
        }
        $outList = $query->with(['out_goods','out_goods.orderGoods','out_goods.goods','out_goods.sku'])->get();
        $arr = [];
        foreach ($outList as $key =>  $value){
            $obj['custom'] = '';
            $obj['out_sn'] = 'PK' . getCode();
            $obj['relation_sn'] = $value->out_sn;
            $obj['warehouse'] = empty($value->warehouse) ? '' : $value->warehouse->title;
            $obj['status'] = $value->status;
            $obj['status_str'] = $value->status;
            $obj['created_at'] = $value->created_at;
            $arr[] = $obj;
        }
        $list = array_merge($arr, $list);
        $query = ReturnOrder::query();
        if($status){
            $query->where('status', '=', $status);
        }
        $backList = $query->with('applyGoods', 'applyGoods.sku', 'supplier')->get();
        $backArr = [];
        foreach ($backList as $key => $value){
            $arr = [];
            $arr['supplier_name'] = $value->supplier_name;
            $arr['out_sn'] = 'PK'.getCode();
            $arr['relation_sn'] = $value->code;
            $arr['warehouse'] = '';
            $arr['status'] = $value->status;
            $arr['status_str'] = $value->status;
            $arr['created_at'] = $value->created_at;
            $backArr[] = $arr;
        }
        $list = array_merge($backArr, $list);
        $query = AllotCart::query();
        if($status){
            $query->where('status', '=', $status);
        }
        //['id', 'created_at']
        $allotCards = $query->with(['cardGoods', 'cardGoods.sku', 'cardGoods.goodsInfo', 'warehouseOut', 'warehouseEnter'])
            ->get();
        $allotArr = [];
        foreach ($allotCards  as $key => $allotCard){
            $arr = [];
            $arr['out_warehouse'] = $allotCard->warehouseOut->title;
            $arr['out_sn'] = '----';
            $arr['allot_sn'] = $allotCard->code;
            $arr['in_warehouse'] = $allotCard->warehouseEnter->title;
            $arr['status_str'] = AllotCart::$_status[$allotCard->status];
            $arr['created_at'] = $allotCard->created_at;
            $allotArr[] = $arr;
        }
        $list = array_merge($allotArr, $list);

        //根据字段last_name对数组$data进行降序排列
        $last_names = array_column($list,'created_at');
        array_multisort($last_names,SORT_DESC,$list);
        return $list;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Allot;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\AllotCart;
use App\Models\PAS\Warehouse\AllotCartGoods;
use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use Illuminate\Http\Request;

/**
 * 调拨单详情接口
 */
class DetailController extends ApiController
{
    public function run(Request $request)
    {
        $id = $request->get('id');
        if(empty($id)){
            $message='参数错误';
            throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
        }
        $obj = AllotCart::query()->with('WarehouseOut','WarehousEnter')->find($id);
        if($obj){
            $obj=$obj->toArray();
            $list = AllotCartGoods::query()
                ->leftJoin('pas_goods as b','pas_allot_card_goods.goods_id','=','b.goods_id')
                ->leftJoin('pas_goods_specific_prices as c','pas_allot_card_goods.sku_id','=','c.id')
                ->leftJoin('pas_goods_allocation as d','pas_allot_card_goods.warehouse_id','=','d.id')
                ->where('pas_allot_card_goods.allot_id',$id)
                ->selectRaw('pas_allot_card_goods.id,pas_allot_card_goods.sku_id,pas_allot_card_goods.number,b.goods_id,b.goods_sn,b.goods_name,c.sku_name,d.no')
                ->get();
            $obj['goods_list']=[];
            if($list){
                $list=$list->toArray();
                $listArr = array_field_as_keys($list, 'sku_id');
                //dd($listArr);
                $lists = AllotCartGoods::query()->where('allot_id',$id)->groupBy('sku_id')->selectRaw('sum(number) as number,sku_id')->get()->toArray();
                foreach ($lists as $key=>$val){
                    $listArr[$val['sku_id']]['number']=$val['number'];
                }
                $obj['goods_list']=$listArr;
            }
        }
        return $obj;
    }
}

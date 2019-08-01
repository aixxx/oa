<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\OutCard;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Purchase\WarehousingApply;
use App\Models\PAS\Purchase\WarehousingApplyGoods;
use App\Models\PAS\SaleOutWarehouse;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use Carbon\Carbon;
use Illuminate\Http\Request;


class CreateController extends ApiController
{


    public function run(Request $request)
    {
        $apply_id = $request->get('apply_id');  //申请单ID
        $type = $request->get('type', 1);

        $user = \Auth::user();
        $res = [];
        $res['out_sn'] =  'OUT_'. getCode() ;
        $res['out_time'] = Carbon::now();



        switch ($type){
            case WarehouseOutCard::TYPE_ALLOT:
            case WarehouseOutCard::TYPE_BACK:
            case WarehouseOutCard::TYPE_SALE:
            default:
            //销售申请单
            $obj = SaleOutWarehouse::query()
                ->with(['out_goods','out_goods.orderGoods','out_goods.goods','out_goods.sku','delivery'])
                ->find($apply_id);
            $res['relation_sn'] = $obj->out_sn;
            $res['apply_id'] = $obj->id;
//                $res['from_warehouse_id'] = $obj->id;
            $res['creator'] = ['chinese_name' => $user->chinese_name, 'id' => $user->id];
            $res['service_date'] = Carbon::now()->toDateString();
            $res['delivery_type'] = $obj->shipping_id;
            $res['delivery'] = $obj->delivery;
            $goodies = $obj->out_goods;
                switch ($obj->status){
                    case SaleOutWarehouse::STATUS_OK_OUT:
                    case SaleOutWarehouse::STATUS_HALF_OUT:
                    case SaleOutWarehouse::STATUS_STAY_OUT:
                    default:
                        $res['goods'] = $this->getGoods($goodies);
                        break;
                }

                break;
        }

        return $res;
    }

    /**
     * @param $goodies
     * @return array
     */
    public function getGoods($goodies): array
    {
        $goodsArr = [];
        foreach ($goodies as $key => $goods) {
            $arr = [];
            $arr['goods_name'] = empty($goods->goods) ? '' : $goods->goods->goods_name;
            $arr['goods_sn'] = empty($goods->goods) ? '' : $goods->goods->goods_sn;
            $arr['out_num'] = $goods->out_num;
            $arr['has_out_num'] = $goods->has_out_num;
            $arr['un_out_num'] = $goods->out_num - $goods->has_out_num;
            $arr['sku_name'] = empty($goods->sku) ? '' : $goods->sku->key_name;
            $goodsArr[] = $arr;
        }
        return $goodsArr;
    }
}

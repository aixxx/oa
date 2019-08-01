<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Plan;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Goods;
use App\Models\PAS\GoodsSpecificPrice;
use App\Models\PAS\Purchase\WarehousingApply;
use App\Models\PAS\Purchase\WarehousingApplyGoods;
use App\Models\PAS\Warehouse\GoodsAllocation;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use Carbon\Carbon;
use Illuminate\Http\Request;


class CancelController extends ApiController
{


    public function run(Request $request)
    {
        $id = $request->get('id');
        $obj = WarehouseInCard::query()->with('inCardGoods')->find($id);

        $res = true;
        $type = $obj->type;
        switch ($type){
            case WarehouseInCard::TYPE_BACK:
                $res = $this->backCancel($obj);
                break;
            case WarehouseInCard::TYPE_ALLOT:
                $res = $this->allotCancel($obj);
                break;
            case WarehouseInCard::TYPE_BUY:
            default:
                //采购单
                $applyObj = WarehousingApplyGoods::query()->find($obj->apply_id);
                $res = $this->purchaseCancel($obj, $applyObj);
                break;
        }
        return $res;
    }

    /**
     * @param WarehouseInCard $obj
     * @param WarehousingApply $applyObj
     * @return bool
     */
    public function purchaseCancel($obj, $applyObj){

        /** @var WarehouseInGoods $inCardGoods */
        $inCardGoods = $obj->inCardGoods();  //采购单操作的商品  sku变动的数据

        $allocationId = $obj->goods_allocation_id;

        $goodsAllocation = GoodsAllocation::find($allocationId);

        //修改关联单 状态
        \DB::beginTransaction();
        $obj->status = WarehouseInCard::STATUS_OK;
        try{
            $goodsAllocation->status = 0;
            $goodsAllocation->save();
            //获取库存里的商品
            /** @var WarehouseInGoods $item */
            foreach($inCardGoods as $item){

                $sku = $item->skuInfo(); // 仓库里实际的数据
                if(empty($sku)){
                    continue;
                }
                $cnt = $sku->store_count - $item->stored_num;
                $data = [
                    'store_count' => $cnt
                ];
                GoodsSpecificPrice::query()->where('id', '=', $item->id)->save($data);

                $gid = $item->goods_id;
                Goods::query()->where('goods_id', '=', $gid)->save(['store_count'=> $cnt]);
            }
            $applyObj->status = 0;
            $applyObj->save();

            $obj->save();
            \DB::commit();
        }catch (\Exception $exception){
            \DB::rollBack();
            throw $exception;
        }
        return true;
    }

    private function backCancel($obj)
    {
        //修改关联单 状态
        return true;
    }

    private function allotCancel($obj)
    {
        //修改关联单 状态
        return true;
    }
}

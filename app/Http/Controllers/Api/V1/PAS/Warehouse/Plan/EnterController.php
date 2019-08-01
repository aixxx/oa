<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Plan;

use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Purchase\WarehousingApply;
use App\Models\PAS\Purchase\WarehousingApplyGoods;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\GoodsFlow;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class EnterController extends ApiController
{


    public function run(Request $request)
    {
        $plan_id = '';
        // TODO: Implement run() method.
        $arr = ['plan_id'];
        $params = $request->only($arr);
        extract($params);

        $allocation_sku_info = $request->get('allocation_sku_info'); //[{allocation_id:1;goods_id:1;sku_id:12;number:23}]

        $rules = [
            'plan_id'=> 'required',
        ];
        $messages = [
            'plan_id.required' => '仓库安排ID不能为空',
        ];
        $this->checkParam($request, $rules, $messages);

        $wareHouseObj = WarehouseInCard::query()->find($plan_id);
        /** @var Collection $inCardGoods */
        $inCardGoods = $wareHouseObj->inCardGoods;

        $skuCardRecords = $inCardGoods->pluck([],'sku_id')->toArray();
//        $warehouse_id = $wareHouseObj->id;

        $in_no = 'enter_'. getCode();
        foreach ($allocation_sku_info as $item){
            list($warehouse_id, $allocation_id, $sku_id, $number, $sku_name) = [0,0,0,0, 0];
            extract($item);
            if(!isset($skuCardRecords[$sku_id])) throw new DiyException('sku_id'.$sku_id.' 不在可操作范围内');

            $storedNum = $skuCardRecords[$sku_id]['in_num'];
            $goods_id = $skuCardRecords[$sku_id]['goods_id'];
            if($number > $storedNum){
                throw new DiyException('sku :'.$sku_name . ', 不能大于安排仓库的数量');
            }
            //更新库存里的商品信息
            $applyId = $wareHouseObj->apply_id;
            $type = $wareHouseObj->type;
            DB::beginTransaction();
            try {
                if ($type == WarehouseInCard::TYPE_BUY) {
                    $applyGoodsApplyObj = WarehousingApplyGoods::query()
                        ->where('id', '=', $skuCardRecords[$sku_id]['apply_goods_id'])
                        ->first();
                    if(empty($applyGoodsApplyObj)) throw new DiyException('数据错误');
                    $applyGoodsApplyObj->increment('r_number', $number);
                    $applyGoodsApplyObj->save();
                    $this->updateApplyCardInfo($applyId, $storedNum, $number);
                }

                $data = [
                    'warehouse_id' => $warehouse_id,
                    'allocation_id' => $allocation_id,
                    'goods_id' => $goods_id,
                    'number' => $number,
                    'sku_id' => $sku_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $allocationGoodsObj = GoodsAllocationGoods::query()->updateOrCreate([
                    'warehouse_id' => $warehouse_id,
                    'allocation_id' => $allocation_id,
                    'sku_id' => $sku_id,
                ], $data);  //操作仓库里的数据
//更改货位状态
                $data = [
                    'sku_name' => $sku_name,
                    'sku_id' => $sku_id,
                    'goods_id' => $goods_id,
                    'card_no' => $in_no,
                    'warehouse_id' => $warehouse_id,
                    'type' => GoodsFlow::TYPE_PURCHASE_IN,  //采购入库
                    'plan_id' => $plan_id,
                    'allocation_id' => $allocation_id,
                    'apply_id' => $applyId,
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ];
                GoodsFlow::query()->create($data);  //sku记录流水
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                throw $exception;
            }
        }

        return true;
    }

    /**
     * @param $applyId
     * @param $storedNum
     * @return bool|\Illuminate\Database\Eloquent\Model|null|object|static
     * @internal param $applyGoodsApplyObj
     * @internal param $number
     */
    public function updateApplyCardInfo($applyId, $storedNum, $number)
    {
        if ($number == $storedNum) {
            WarehousingApply::query()
                ->where('id', '=', $applyId)
                ->update(['status' => WarehousingApply::STATUS_OK]);
        } elseif ($number < $storedNum) {
            WarehousingApply::query()
                ->where('id', '=', $applyId)
                ->update(['status' => WarehousingApply::STATUS_PART]);
        }
        return true;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Plan;

use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\GoodsSpecificPrice;
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


class SaveController extends ApiController
{


    public function run(Request $request)
    {
        // TODO: Implement run() method.
        $type = $in_no = $apply_id = $warehouse_id = $create_user_id =
        $create_user_name = $cargo_user_id= $cargo_user_name = $delivery_type=
        $delivery_type_id= $delivery_name = $status= $percent = $remark = '';

        $arr = ['type', 'in_no', 'apply_id', 'warehouse_id', 'create_user_id',
            'create_user_name', 'cargo_user_id',
            'cargo_user_name', 'delivery_type', 'delivery_type_id', 'delivery_name',
            'status', 'percent', 'remark'];
        $params = $request->only($arr);
        extract($params);

        $rules = [
            'in_no'=> 'required',
            'apply_id'=> 'required',
            'type'=> 'required',
        ];
        $messages = [
            'type.required' => '关联的单号的类型不能为空',
            'in_no.required' => '安排货位单号不能为空',
            'apply_id.required' => '关联的单号不能为空',
        ];
        $this->checkParam($request, $rules, $messages);

        $applyObj = $this->getApplyObj($type, $apply_id);
        $data = [
            'type' => $type,
            'in_no' => $in_no,
            'apply_id' => $apply_id,
//            'goods_allocation_id' => $goods_allocation_id,
            'create_user_id' => $create_user_id,
            'create_user_name' => $create_user_name,
            'cargo_user_id' => $cargo_user_id,
            'cargo_user_name' => $cargo_user_name,
            'delivery_type' => $delivery_type,
            'delivery_type_id' => $delivery_type_id,
            'delivery_name' => $delivery_name,
            'status' => $status,
            'percent' => $percent,
            'remark' => $remark,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        DB::beginTransaction();
        try {
            $obj = WarehouseInCard::query()->create($data);
            /** @var Collection $applyGoodsObj */
            $applyGoodsObj = $applyObj->applyGoods;
            if (empty($applyGoodsObj)) throw new DiyException('数据错误', -1);
            $skuApplyInfo = $applyGoodsObj->pluck([], 'sku_id');
            //获得采购单信息
            $allocation_sku_info = $request->get('allocation_sku_info'); //[{allocation_id:1;goods_id:1;sku_id:12;number:23}]

            foreach ($allocation_sku_info as $item) {
                list($warehouse_id, $goods_id, $sku_id, $number, $sku_name) = [0, 0, 0, 0, 0]; //这里的sku_id 实际是pcc_id
                extract($item);
                if (!isset($skuApplyInfo[$sku_id])) throw new DiyException('数据错误' . __LINE__, -1);;

                $data = [
                    'in_id' => $obj->id,
                    'type' => $type,
                    'apply_id' => $apply_id,
                    'apply_goods_id' => $skuApplyInfo[$sku_id]['id'],
                    'warehouse_id' => $warehouse_id,
                    'goods_id' => $goods_id,
                    'goods_no' => '',
                    'sku_id' => $skuApplyInfo[$sku_id]['sku_id'],
                    'in_num' => $skuApplyInfo[$sku_id]['number'],
                    'stored_num' => $number,
                    'status' => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $houseObj = WarehouseInGoods::query()->insert($data);  //入库单关联的商品的信息

                WarehousingApplyGoods::query()->where('id', '=', $skuApplyInfo[$sku_id]['id'])
                    ->update(['warehouse_id' => $warehouse_id]);  //更新申请单状态
            }
            WarehousingApply::query()->where('id', '=', $apply_id)
                ->update(['status' => WarehousingApply::STATUS_HOUSE_YES]);
            DB::commit();
        }catch (\Exception  $exception){
            DB::rollBack();
            throw $exception;
        }

        return true;
    }

    /**
     * @param $type
     * @param $apply_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     * @throws DiyException
     */
    public function getApplyObj($type, $apply_id)
    {
        switch ($type) {
            case WarehouseInCard::TYPE_BUY:
                $applyObj = WarehousingApply::query()->with(['applyGoods', 'applyGoods.sku'])->find($apply_id);
                if (empty($applyObj)) {
                    throw new DiyException('申请单ID不存在');
                }
                break;
            case WarehouseInCard::TYPE_ALLOT:
            case WarehouseInCard::TYPE_BACK:
            default:
                $applyObj = WarehousingApply::query()->with(['applyGoods', 'applyGoods.sku'])->find($apply_id);
                if (empty($applyObj)) {
                    throw new DiyException('申请单ID不存在');
                }
                break;
        }
        return $applyObj;
    }
}

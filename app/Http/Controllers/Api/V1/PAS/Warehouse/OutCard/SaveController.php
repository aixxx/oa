<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\OutCard;

use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\GoodsSpecificPrice;
use App\Models\PAS\SaleOutWarehouse;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\GoodsFlow;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use App\Models\PAS\Warehouse\WarehouseOutGoods;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class SaveController extends ApiController
{


    public function run(Request $request)
    {
        $out_type = $out_no = $apply_id = $warehouse_id =  $allocation_id = $create_user_id = $number=
        $create_user_name = $cargo_user_id= $cargo_user_name = $delivery_type= $deliver_date= $business_date =
        $delivery_type_id= $delivery_name = $status= $percent = $remark = '';
        // TODO: Implement run() method.
        $arr = ['out_type', 'out_no', 'apply_id', 'warehouse_id', 'allocation_id', 'create_user_id',
            'create_user_name', 'deliver_date',
            'business_date', 'delivery_type', 'number', 'delivery_name',
            'status', 'percent', 'remark'];
        $params = $request->only($arr);
        extract($params);

        $rules = [
            'out_no'=> 'required',
            'apply_id'=> 'required',
            'out_type'=> 'required',
        ];
        $messages = [
            'type.required' => '关联的单号的类型不能为空',
            'out_no.required' => '安排货位单号不能为空',
            'apply_id.required' => '关联的单号不能为空',
        ];
        $this->checkParam($request, $rules, $messages);

        $applyObj = SaleOutWarehouse::query()->where('id', '=', $apply_id)->first();
        //获得出库单信息
        $applyGoods = $applyObj->out_goods;
        /** @var Collection $applyGoods */
        $applyGoods = $applyGoods->pluck([], 'sku_id');
        $data = [
            'out_type' => $out_type,
            'out_no' => $out_no,
            'warehouse_id' => $warehouse_id,
            'allocation_id' => $allocation_id,
            'apply_id' => $apply_id,
            'create_user_id' => $create_user_id,
            'create_user_name' => $create_user_name,
            'delivery_type' => $delivery_type,
            'deliver_date' => $deliver_date,
            'business_date' => $business_date,
            'number' => $number,
            'status' => $status,
            'remark' => $remark,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        DB::transaction(function () use($data, $request, $applyGoods, $warehouse_id, $out_no, $apply_id){
            try {
                //[{allocation_id:1;goods_id:1;sku_id:12;number:23}]
                $allocation_sku = $request->get('allocation_sku_info');

                DB::beginTransaction();
                $obj = WarehouseOutCard::query()->create($data);

                foreach ($allocation_sku as $item) {
                    list($allocation_id, $goods_id, $skuId, $number) = [0, 0, 0, 0];
                    extract($item);
                    if(!isset($applyGoods[$skuId])){
                        throw DiyException::instance('SkuId不在操作范围内');
                    }
                    if($applyGoods[$skuId]['out_num'] - $applyGoods[$skuId]['has_out_num'] < $number){
                        throw DiyException::instance('SkuId:'.$skuId.',数量超出范围');
                    }
                    //当前仓库的数量
                    $warehouseTotalCount = GoodsAllocationGoods::query()
                        ->where('warehouse_id', '=', $warehouse_id)
                        ->where('allocation_id', '=', $allocation_id)
                        ->where('sku_id', '=', $skuId)->sum('number');
                    if($number > $warehouseTotalCount){
                        throw DiyException::instance('货位中SkuId:'.$skuId.',库存数量不够');
                    }
                    $skuInfo = GoodsSpecificPrice::find($skuId);
                    $data = [
                        'in_id' => $obj->id,
                        'goods_id' => $goods_id,
                        'goods_no' => '',
                        'sku_id' => $skuId,
                        'apply_num' => $applyGoods[$skuId]['number'],
                        'outed_num' => $number,
                        'house_num' => $skuInfo->store_count,  //库存数
                        'updated_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                    ];
                    //出库单关联的商品的信息
                    $houseObj = WarehouseOutGoods::query()->create($data);
                    //更新库存里的商品信息
                    $data = [
                        'number' => $warehouseTotalCount - $number,
                    ];
                    GoodsAllocationGoods::query()
                        ->where('warehouse_id', '=', $warehouse_id)
                        ->where('allocation_id', '=', $allocation_id)
                        ->where('sku_id', '=', $skuId)
                        ->update($data);

                    //商品流水表
                    $this->createSkuFlow($skuInfo, $warehouse_id, $obj, $allocation_id);
                }
                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
            }
        });

        return true;
    }

    /**
     * @param GoodsSpecificPrice $skuInfo
     * @param $warehouse_id
     * @param Model|WarehouseOutCard $obj
     * @param $allocation_id
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createSkuFlow($skuInfo, $warehouse_id, $obj, $allocation_id)
    {
        $data = [
            'sku_name' => $skuInfo->sku_name,
            'sku_id' => $skuInfo->id,
            'goods_id' => $skuInfo->goods_id,
            'card_no' => $obj->out_no,
            'warehouse_id' => $warehouse_id,
            'type' => GoodsFlow::TYPE_SALE_OUT,  //销售出库
            'plan_id' => $obj->id,
            'allocation_id' => $allocation_id,
            'apply_id' => $obj->apply_id,
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ];
        //sku记录流水
        return GoodsFlow::query()->create($data);
    }

}

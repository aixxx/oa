<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Plan;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use Illuminate\Http\Request;

/**
 * 货位跟商品关联数量列表
 */
class AllocationListController extends ApiController
{
    public function run(Request $request)
    {
        $warehouse_id = $request->get('warehouse_id');
        $goods_id= $request->get('goods_id');
        $sku_id = $request->get('sku_id');
        $query = GoodsAllocationGoods::query();
        if(empty($warehouse_id)){
            $message='仓库id不能为空';
            throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
        }else{
            $query->where('warehouse_id', '=', $warehouse_id);
        }
        if(empty($goods_id)){
            $message='商品id不能为空';
            throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
        }else{
            $query->where('goods_id', '=', $goods_id);
        }
        if(empty($sku_id)){
            $message='商品sku数据id不能为空';
            throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
        }else{
            $query->where('sku_id', '=', $sku_id);
        }
        $list = $query->with('goods_allocation_no')->get(['goods_id','number','allocation_id']);
        return $list;
    }
}

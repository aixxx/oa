<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Goods;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ListController extends ApiController
{


    public function run(Request $request)
    {
        $warehouse_id = $request->get('warehouse_id');
        $allocation_id = $request->get('allocation_id');
        $sku_id = $request->get('sku_id');
        $goods_id = $request->get('goods_id');
        $brand_id = $request->get('brand_id');
        $category_id = $request->get('category_id');
        $keyword = $request->get('keyword');
        $size = $request->get('size', 10);
        $page = $request->get('page', 1);
        $is_empty_num_show = $request->get('is_empty_num', false);

        $query =  GoodsAllocationGoods::query()->with(['sku', 'warehouse', 'goods', 'goods_allocation']);

        if($warehouse_id){
            $houseIdsArr = explode(',', $warehouse_id);
            $query->whereIn('warehouse_id', $houseIdsArr);
        }
        if($allocation_id){
            $allocationIdsArr = explode(',', $allocation_id);
            $query->whereIn('allocation_id', $allocationIdsArr);
        }
        if($sku_id){
            $query->where('sku_id', '=', $sku_id);
        }
        if($goods_id){
            $query->where('goods_id', '=', $goods_id);
        }
        if($keyword){
            $query->where('goods_name', 'like', '%'.$keyword.'%');
        }
        if($brand_id){
            $query->where('brand_id', '=', $brand_id);
        }
        if($category_id){
            $query->where('category_id', '=', $category_id);
        }
        if(!$is_empty_num_show){
            $query->where('number', '>', 0);
        }
        $res = $query->paginate($size,['*'], 'page', $page);
        return $res;
    }
}

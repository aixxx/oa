<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Goods;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use Illuminate\Http\Request;


class AnalyzeController extends ApiController
{


    public function run(Request $request)
    {
        $warehouse_id = $request->get('warehouse_ids');
        $goods_ids = $request->get('goods_ids');

        $query =  GoodsAllocationGoods::query();  //'goods_allocation', 'warehouse'

        if($warehouse_id){
            $query->where('warehouse_id', '=', $warehouse_id);
        }

        if($goods_ids){
            if(!is_array($goods_ids)){
                $goods_ids = explode(',', $goods_ids);
            }
            $query->whereIn('goods_id', $goods_ids);
        }
        $query->groupBy('goods_id');
        $obj = $query->selectRaw("sum(number) as number,goods_name")->get();
        return $obj;
    }

}

<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Goods;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\GoodsFlow;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;


class FlowController extends ApiController
{


    public function run(Request $request)
    {
        $goods_id = $request->get('goods_id');

        $query =  GoodsFlow::query();  //'goods_allocation', 'warehouse'

        $query->with([
            'sku',
            'warehouse',
            'goods',
            'plan',
            'allocation',
        ]);
        if($goods_id){
            $query->where('goods_id', '=', $goods_id);
        }

        $obj = $query->get();
        return $obj;
    }
}

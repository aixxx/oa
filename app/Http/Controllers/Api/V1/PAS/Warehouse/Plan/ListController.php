<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Plan;

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
        $status = $request->get('status');
        $query = WarehouseInCard::query();

        if($status){
            $query->where('status', '=', $status);
        }
        $list = $query->with('inCardGoods', 'inCardGoods.goodsInfo', 'inCardGoods.skuInfo')->get();
        return $list;
    }
}

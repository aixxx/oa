<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\OutCard;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ListController extends ApiController
{


    public function run(Request $request)
    {
        $status = $request->get('status', 1);
        $obj = WarehouseOutCard::query()->where('status', '=', $status)->get();
        return $obj;
    }
}

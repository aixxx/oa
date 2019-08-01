<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\DeliveryType;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\Logistics;
use App\Models\PAS\Warehouse\WarehouseDeliveryType;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use App\Models\PAS\Warehouse\WarehouseOutGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;


class LogisticsListController extends ApiController
{


    public function run(Request $request)
    {

       $obj = Logistics::query()->where('status', '=', Logistics::STATUS_ON)->get();

        return $obj;
    }
}

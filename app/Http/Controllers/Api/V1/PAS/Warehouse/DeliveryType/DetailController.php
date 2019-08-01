<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\DeliveryType;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseDeliveryType;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DetailController extends ApiController
{


    public function run(Request $request)
    {
        $id = $request->get('id');
        $obj = WarehouseDeliveryType::find($id);
        return $obj;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\DeliveryType;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\LogisticsPoint;
use Illuminate\Http\Request;


class PointListController extends ApiController
{


    public function run(Request $request)
    {
        $logistics_id = $request->get('logistics_id', 0);
        $obj = LogisticsPoint::query()
           ->where('status', '=', LogisticsPoint::STATUS_ON)
           ->where('logistics_id', '=', $logistics_id)
           ->get();

        return $obj;
    }
}

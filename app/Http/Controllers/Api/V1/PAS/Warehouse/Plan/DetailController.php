<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Plan;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Purchase\WarehousingApply;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DetailController extends ApiController
{


    public function run(Request $request)
    {
        $applyId = $request->get('apply_id');
        $type = $request->get('type');
        $applyObj = WarehousingApply::query()
            ->with(['applyGoods', 'applyGoods.sku'])
            ->find($applyId);
        return $applyObj;
    }
}

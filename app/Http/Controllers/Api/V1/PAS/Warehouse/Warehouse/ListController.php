<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Warehouse;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocation;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\Warehouse;
use Illuminate\Http\Request;


class ListController extends ApiController
{


    public function run(Request $request)
    {
        // TODO: Implement run() method.
        $status = $request->input('status', 1);
        $list = Warehouse::query()->withCount(['allowAllocation', 'skus'])
            ->where('status', '=', $status)->get();

        return $list;
    }
}

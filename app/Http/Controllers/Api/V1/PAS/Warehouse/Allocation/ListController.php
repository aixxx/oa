<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Allocation;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocation;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use Illuminate\Http\Request;


class ListController extends ApiController
{


    public function run(Request $request)
    {
        // TODO: Implement run() method.
        $id = $request->get('id');
        $sku_id = $request->get('sku_id');
        $status = $request->get('status', 0);
        $idsArr = explode(',', $id);
        $query = GoodsAllocation::query();
        if($sku_id){
            $allocationIds = GoodsAllocationGoods::query()->where('sku_id', '=', $sku_id)
                ->whereIn('warehouse_id', $idsArr)->select(['allocation_id'])->pluck('allocation_id');
            $allocationIds =  array_unique($allocationIds->toArray());
            $query->whereIn('id', $allocationIds);
        }
        $obj = $query
            ->whereIn('warehouse_id', $idsArr)
            ->where('status', '=', $status)->select(['no', 'warehouse_id',
                'is_private', 'status', 'capacity', 'row_num', 'id'])
            ->get();
        return $obj;
    }
}

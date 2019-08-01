<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\StockCheck;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\StockCheck;
use Illuminate\Http\Request;


class DetailController extends ApiController
{


    public function run(Request $request)
    {
        $id = $request->get('id');
        $obj = StockCheck::query()
            ->with(['warehouse','check_user', 'goods', 'goods.goods', 'goods.sku'])
            ->find($id);
        dd($obj->toArray());
        return $obj;
    }
}

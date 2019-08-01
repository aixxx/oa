<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\OutCard;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use Carbon\Carbon;
use Illuminate\Http\Request;


class CancelController extends ApiController
{


    public function run(Request $request)
    {
        $id = $request->get('id');
        $obj = WarehouseOutCard::find($id);

        //修改关联单 状态


        //修改库存
        $type = $obj->out_type;


        //修改商品库存

        return true;
    }
}

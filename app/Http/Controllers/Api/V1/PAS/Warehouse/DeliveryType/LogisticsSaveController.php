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


class LogisticsSaveController extends ApiController
{


    public function run(Request $request)
    {
        $title =  '';
        // TODO: Implement run() method.
        $arr = ['title'];
        $params = $request->only($arr);
        extract($params);

        $rules = [
            'title'=> 'required',
        ];
        $messages = [
            'title.required' => '请输入物流名称',
        ];
        $this->checkParam($request, $rules, $messages);
        $data = [
            'title' => $title,
            'status' => Logistics::STATUS_ON,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $obj = Logistics::query()->create($data);

        //获得采购单信息

        return $obj;
    }
}

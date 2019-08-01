<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\DeliveryType;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\Logistics;
use App\Models\PAS\Warehouse\LogisticsPoint;
use App\Models\PAS\Warehouse\WarehouseDeliveryType;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use App\Models\PAS\Warehouse\WarehouseOutGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;


class PointSaveController extends ApiController
{


    public function run(Request $request)
    {
        $point_name = $contact_tel = $logistics_id =   '';
        // TODO: Implement run() method.
        $arr = ['point_name', 'contact_tel', 'logistics_id'];
        $params = $request->only($arr);
        extract($params);

        $rules = [
            'point_name'=> 'required',
            'contact_tel'=> 'required',
            'logistics_id'=> 'required',
        ];
        $messages = [
            'point_name.required' => '请输入网点名称',
            'contact_tel.required' => '请输入联系电话',
            'logistics_id.required' => '请关联物流',
        ];
        $this->checkParam($request, $rules, $messages);
        $data = [
            'point' => $point_name,
            'tel' => $contact_tel,
            'logistics_id' => $logistics_id,
            'status' => Logistics::STATUS_ON,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $obj = LogisticsPoint::query()->create($data);

        //获得采购单信息

        return $obj;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\DeliveryType;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\WarehouseDeliveryType;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Models\PAS\Warehouse\WarehouseOutCard;
use App\Models\PAS\Warehouse\WarehouseOutGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;


class UpdateController extends ApiController
{


    public function run(Request $request)
    {
        $delivery_type = $logistics_id = $point = $point_tel =  $delivery_no = $receiver = $customer_info_id=
        $address = $contact_tel = $freight_desc = $receiver =  '';
        // TODO: Implement run() method.
        $arr = ['delivery_type', 'logistics_id', 'point', 'point_tel', 'delivery_no', 'receiver',
            'customer_info_id', 'address', 'contact_tel', 'freight_desc'];
        $params = $request->only($arr);
        extract($params);
        $id = intval($request['id']);
        $rules = [
            'delivery_type'=> 'required',
            'logistics_id'=> 'required',
            'point'=> 'required',
        ];
        $messages = [
            'delivery_type.required' => '关联的单号的类型不能为空',
            'logistics_id.required' => '安排货位单号不能为空',
            'point.required' => '关联的单号不能为空',
        ];
        $this->checkParam($request, $rules, $messages);
        $data = [
            'delivery_type' => $delivery_type,
            'logistics_id' => $logistics_id,
            'point' => $point,
            'point_tel' => $point_tel,
            'delivery_no' => $delivery_no,
            'receiver' => $receiver,
            'customer_info_id' => $customer_info_id,
            'address' => $address,
            'contact_tel' => $contact_tel,
            'freight_desc' => $freight_desc,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $obj = WarehouseDeliveryType::query()->where('id',$id)->update($data);
        //获得采购单信息

        return $obj;
    }
}

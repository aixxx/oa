<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Allot;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\AllotCart;
use App\Models\PAS\Warehouse\AllotCartGoods;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use DB;
/**
 * 调拨单添加接口
 */

class SaveController extends ApiController
{
    public function run(Request $request)
    {
        $warehouse_from_id = $warehouse_allocation_from_id = $warehouse_to_id = $warehouse_allocation_to_id =
            $business_date = $remark = $number= $create_user_id = $create_user_name = $cargo_user_id =
            $cargo_user_name =$delivery_type = $status =  '';
        // TODO: Implement run() method.
        $arr = ['warehouse_from_id', 'warehouse_allocation_from_id', 'warehouse_to_id', 'warehouse_allocation_to_id',
            'business_date', 'remark', 'number', 'create_user_id', 'create_user_name', 'cargo_user_id',
            'cargo_user_name', 'delivery_type', 'status'];
        $params = $request->only($arr);
        extract($params);

        $rules = [
            'warehouse_from_id'=> 'required',
            'warehouse_allocation_from_id'=> 'required',
            'warehouse_to_id'=> 'required',
        ];
        $messages = [
            'warehouse_from_id.required' => '调出仓库不能为空',
            'warehouse_allocation_from_id.required' => '调出货位不能为空',
            'warehouse_to_id.required' => '掉入仓库不能为空'
        ];
        $this->checkParam($request, $rules, $messages);

        $user = Auth::user();
        $uid = $user->id;
        $data = [
            'code' => getCode('DBD'),
            'warehouse_from_id' => $warehouse_from_id,
            'warehouse_allocation_from_id' => $warehouse_allocation_from_id,
            'warehouse_to_id' => $warehouse_to_id,
            'warehouse_allocation_to_id' => $warehouse_allocation_to_id,
            'business_date' => $business_date,
            'user_id'=>$uid,
            'remark' => $remark,
            'number' => $number,
            'create_user_id' => $create_user_id,
            'create_user_name' => $create_user_name,
            'cargo_user_id' => $cargo_user_id,
            'cargo_user_name' => $cargo_user_name,
            'delivery_type' => $delivery_type,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        DB::beginTransaction();
        $obj = AllotCart::query()->create($data);
        if($obj){
            $skuInfo = $request->get('allocation_sku_info'); //[{allocation_id:1;goods_id:1;sku_id:12;number:23}]
        }
        if(!empty($skuInfo) && isset($skuInfo)){
            foreach ($skuInfo as $item) {
                list($allocation_id, $goods_id, $sku_id, $number) = [0, 0, 0, 0];
                extract($item);
                $data = [
                    'allot_id' => $obj->id,
                    'warehouse_id'=>$allocation_id,
                    'goods_id' => $goods_id,
                    'sku_id' => $sku_id,
                    'number' => $number,
                    'status' => 1,
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ];
//                if($status==AllotCart::STATUS_ONE){//申请调拨
//                    //修改仓库货位跟商品关联数量表的数据
//                    GoodsAllocationGoods::where('allocation_id',$allocation_id)
//                        ->where('goods_id',$goods_id)
//                        ->where('sku_id',$sku_id)
//                        ->increment('number',$number);
//                }
                $dataArr[]=$data;
            }
            $houseObj = AllotCartGoods::query()->insert($data);  //入库单关联的商品的信息
        }
        if($houseObj){
            DB::commit();
            return $obj;
        }

        DB::rollBack();
        $message='添加失败';
        throw  new DiyException($message, ConstFile::API_PARAM_ERROR);

    }
}

<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Allot;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\AllotCart;
use App\Models\PAS\Warehouse\AllotCartGoods;
use App\Exceptions\DiyException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
/**
 * 调拨单修改接口
 */
class UpdateController extends ApiController
{
    public function run(Request $request)
    {
        $id=$warehouse_from_id = $warehouse_allocation_from_id = $warehouse_to_id = $warehouse_allocation_to_id =
            $business_date = $remark = $number= $create_user_id = $create_user_name = $cargo_user_id =
            $cargo_user_name =$delivery_type = $status =  '';
        // TODO: Implement run() method.
        $arr = ['id','warehouse_from_id', 'warehouse_allocation_from_id', 'warehouse_to_id', 'warehouse_allocation_to_id',
            'business_date', 'remark', 'number', 'create_user_id', 'create_user_name', 'cargo_user_id',
            'cargo_user_name', 'delivery_type', 'status'];
        $params = $request->only($arr);
        extract($params);

        $rules = [
            'id'=> 'required',
            'warehouse_from_id'=> 'required',
            'warehouse_allocation_from_id'=> 'required',
            'warehouse_to_id'=> 'required',
            'warehouse_allocation_to_id'=> 'required',
        ];
        $messages = [
            'id.required' => '调拨单不为空',
            'warehouse_from_id.required' => '调出仓库不能为空',
            'warehouse_allocation_from_id.required' => '调出货位不能为空',
            'warehouse_to_id.required' => '掉入仓库不能为空',
            'warehouse_allocation_to_id.required' => '掉入货位不能为空',
        ];
        $this->checkParam($request, $rules, $messages);

        $count = AllotCart::query()->where('id',$id)->count('id');
        if(!$count){
            throw  new DiyException('调拨单不存在', ConstFile::API_PARAM_ERROR);
        }
        $user = Auth::user();
        $uid = $user->id;
        $data = [
            'warehouse_from_id' => $warehouse_from_id,
            'warehouse_allocation_from_id' => $warehouse_allocation_from_id,
            'warehouse_to_id' => $warehouse_to_id,
            'warehouse_allocation_to_id' => $warehouse_allocation_to_id,
            'business_date' => $business_date,
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
        $obj = AllotCart::query()->where('id',$id)->where('user_id',$uid)->update($data);
        $skuInfo='';
        $houseObj='';
        if($obj){
            $skuInfo = $request->get('allocation_sku_info');
        }
        if(!empty($skuInfo) && isset($skuInfo)){
            foreach ($skuInfo as $item) {
                list($allocation_id, $goods_id, $sku_id, $number) = [0, 0, 0, 0];
                extract($item);
                if (!empty($item['id']) && isset($item['id'])) {
                    $id = intval($item['id']);
                    $data = [
                        'warehouse_id' => $allocation_id,
                        'goods_id' => $goods_id,
                        'sku_id' => $sku_id,
                        'number' => $number,
                        'updated_at' =>  Carbon::now(),
                    ];
                    $houseObj = AllotCartGoods::query()->where('id', $id)->update($data);//修改调拨商品
                } else {
                    $data = [
                        'allot_id' => $obj->id,
                        'warehouse_id' => $allocation_id,
                        'goods_id' => $goods_id,
                        'sku_id' => $sku_id,
                        'number' => $number,
                        'status' => 1,
                        'updated_at' =>  Carbon::now(),
                        'created_at' => Carbon::now(),
                    ];
                    $houseObj = AllotCartGoods::query()->create($data);//新增调拨商品
                }
//                if($status==AllotCart::STATUS_ONE){//申请调拨
//                    //修改仓库货位跟商品关联数量表的数据
//                    GoodsAllocationGoods::where('allocation_id',$allocation_id)
//                        ->where('goods_id',$goods_id)
//                        ->where('sku_id',$sku_id)
//                        ->increment('number',$number);
//                }
            }
        }
        if($houseObj){
            DB::commit();
            return $obj;
        }
        DB::rollBack();
        $message='修改失败';
        throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
    }
}

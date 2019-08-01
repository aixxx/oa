<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Warehouse;

use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocation;
use App\Models\PAS\Warehouse\Warehouse;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SaveController extends ApiController
{


    public function run(Request $request)
    {
        // TODO: Implement run() method.
        $id = $request->input('id');
        $name = $request->input('title');
        $mnemonicCode = $request->input('alias');
        $principalUser = $request->input('charge_id');
        $status = $request->input('status', 0);
        $squire = $request->input('warehouse_area');
        $address = $request->input('address');
        $allocationNum = $request->input('stwarehouse');
        $allocationRowNum = $request->input('row_number');
        $contactTel = $request->input('telephone');
        $rules = ['title' => 'required'];
        $messages = ['title.required'=> '请输入仓库名称'];
        $this->checkParam($request, $rules, $messages);

        $chargeUser = User::find($principalUser);
        $obj = Warehouse::query()->find($id);
        if(!$obj){
            $obj = new Warehouse();
            $obj->user_id = \Auth::id();
            $obj->title = $name;
            $obj->alias = $mnemonicCode;
            $obj->charge_id = $principalUser;
            $obj->charge_name = $chargeUser->chinese_name;
            $obj->warehouse_area = $squire;
            $obj->address = $address;
            $obj->stwarehouse = $allocationNum;
            $obj->row_number = $allocationRowNum;
            $obj->telephone = $contactTel;
            $obj->status = $status;
            $obj->created_at = date('Y-m-d H:i:s');
            $obj->updated_at = date('Y-m-d H:i:s');
            try {
                DB::beginTransaction();
                $res = Warehouse::query()->create($obj->toArray());
                $id = $res->id;

                //初始化货位
                $this->initAllocation($id, $allocationNum, $allocationRowNum, ($squire * 0.2) / $allocationNum);
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                throw  $exception;
            }
        }else{
            $obj->title = $name;
            $obj->alias = $mnemonicCode;
            $obj->charge_id = $principalUser;
            $obj->charge_name = $chargeUser->chinese_name;
            $obj->warehouse_area = $squire;
            $obj->address = $address;
            $obj->stwarehouse = $allocationNum;
            $obj->row_number = $allocationRowNum;
            $obj->telephone = $contactTel;
            $obj->status = $status;
            $obj->created_at = date('Y-m-d H:i:s');
            $obj->updated_at = date('Y-m-d H:i:s');
            $res =  $obj->save();
        }
        return true;
    }

    public function initAllocation($warehouseId, $allocationNum, $allocationRowNum, $areaPer){
        $perNum = floor($allocationNum/$allocationRowNum); //一排多少个货位
        for ($i=1; $i<= $allocationNum; $i++){
            $rowNum = ceil($i/$perNum);
            $data= [
                'warehouse_id' => $warehouseId,
                'no' => 'HQ' . $i,
                'row_num' => $rowNum,
                'capacity' => $areaPer
            ] ;
            GoodsAllocation::query()->insert($data);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Allot;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\AllotCart;
use App\Exceptions\DiyException;
use App\Constant\ConstFile;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * 调拨单列表接口
 */
class ListController extends ApiController
{
    public function run(Request $request)
    {
        $limint=10;
        $where=['id','>',0];
        if(empty($request['type'])){
            $message='参数错误';
            throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
        }
        $type=intval($request['type']);
        if($type==1){

        }
        if(!empty($request['status']) && isset($request['status'])){
            $where['status']=$request->get('status');
        }
        if(!empty($request['limint']) && isset($request['limint'])){
            $limint = intval($request['limint']);
        }
        if(!empty($request['title']) && isset($request['title'])){
            $obj = AllotCart::query()->with('WarehouseOut','WarehousEnter')->where($where)
                ->orWhere('code',trim($request['title']))
                ->select(['id','code','warehouse_from_id','warehouse_to_id','status','created_at'])
                ->paginate($limint);
        }else{
            $obj = AllotCart::query()->with('WarehouseOut','WarehousEnter')
                    ->where($where)
                    ->select(['id','code','warehouse_from_id','warehouse_to_id','status','created_at'])
                    ->paginate($limint);
        }
        if($obj){
            $obj=$obj->toArray();
        }else{
            $obj=[];
        }

        return $obj;
    }
}

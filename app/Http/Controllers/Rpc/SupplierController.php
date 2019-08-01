<?php

namespace App\Http\Controllers\Rpc;

use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\Purchase\WarehousingApply;
use JWTAuth;

class SupplierController extends HproseController
{
    /**
     * @param $uid
     * code   编号
     * money   金额
     * apply_name   经手人名称
     * created_at  采购执行单添加时间
     * status      0草稿 1待入库未安排 2待入库 - 仓库已安排 3部分入库 4全部入库
     */
    public function getPurchase($uid=7, $limit=10, $page=1) {
        $where['supplier_id']=$uid;
        $data = WarehousingApply::where($where)->select(['id','code','money','apply_name','created_at','status'])->paginate($limit,['*'],'page',$page);
        $result=[];
        if($data){
            $data = $data->toArray();
            $result = [
                'total' => $data['total'],
                'page' => $page,
                'limit' => $limit,
                'data' =>  $data['data']
            ];
        }
        return $result;
    }
}

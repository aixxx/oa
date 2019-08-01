<?php

namespace App\Http\Controllers\Rpc;

use App\Models\PAS\SaleOrderGoods;
use App\Models\PAS\Goods;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class GoodsController extends HproseController
{

    /*
     * 供应商的商品
     * */
    public function getSupplierGoods($uid, $limit=10, $page=1) {
        if(empty($uid)){
            return ['status'=>0, '请选择供应商'];
        }

        $data = Goods::where('suppliers_id', $uid)->paginate($limit, array(), 'page', $page)->toArray();
            //->leftJoin('pas_supplier', 'pas_goods.suppliers_id', '=', 'pas_supplier.id')
            //->with('specific_price')

        $result = [
            'total' => $data['total'],
            'page' => $page,
            'limit' => $limit,
            'data' =>  $data['data']
        ];
        return ['status'=>1, 'data'=>$result];
    }


    /*
     * 客户购买的商品
     * */
    public function getCustomerBuyGoods($uid, $limit=10, $page=1){

        if(empty($uid)){
            return ['status'=>0, 'msg'=>'请选择客户'];
        }

        $count = SaleOrderGoods::where('user_id', $uid)->groupBy('goods_id')->get(['goods_id'])->toArray();
        $goods = [];
        if(!empty($count)){
            $goods = Goods::whereIn('goods_id', $count)->paginate($limit, array(), 'page', $page)->toArray();
        }
        $result = [
            'total' => count($count),
            'page' => $page,
            'limit' => $limit,
            'data' => $goods['data']
        ];

        return ['status'=>1, 'data'=>$result];
    }
}

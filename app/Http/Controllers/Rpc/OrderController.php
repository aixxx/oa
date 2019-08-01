<?php

namespace App\Http\Controllers\Rpc;

use App\Models\PAS\SaleOrder;
use JWTAuth;

class OrderController extends HproseController
{
    public $status = [
        '0' => '草稿',
        '1' => '审核中',
        '2' => '已撤回',
        '3' => '已驳回',
        '4' => '待出库',//审核完成
        '5' => '部分出库',
        '6' => '全部出库'
    ];

    /*
     * 客户购买的订单
     * */
    public function getUserOrders($uid, $limit=10, $page=1) {
        if(empty($uid)){
            return ['status'=>0, '请选择客户'];
        }
        $list = SaleOrder::where('buy_user_id', $uid)->paginate($limit, array(), 'page', $page)->toArray();
        
        if(!empty($list['data'])){
            foreach($list['data'] as &$v){
                $v['balance'] = $v['total_money'] - $v['actual_money'];
                $v['is_finish'] = ($v['total_money'] - $v['actual_money']) > 0 ? 0 : 1;
                $v['order_status_name'] = $v['status'] >= 4 ? '已审核' : $this->status[$v['status']];
                $v['out_status_name'] = $v['status'] >= 4 ? $this->status[$v['status']] : '';
                $v['pay_status_name'] = ($v['total_money'] - $v['actual_money']) > 0 ? ($v['total_money'] - $v['actual_money']) : '已结清';
            }
        }

        $result['total'] = $list['total'];
        $result['total_page'] = $list['last_page'];
        $result['data'] = $list['data'];

        return $result;
    }
}

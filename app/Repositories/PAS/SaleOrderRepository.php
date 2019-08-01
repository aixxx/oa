<?php

namespace App\Repositories\PAS;

use App\Models\PAS\Goods;
use App\Models\PAS\GoodsSpecificPrice;
use App\Models\PAS\SaleOrderGoods;
use App\Models\PAS\Purchase\CostInformation;
use App\Models\PAS\SaleInvoice;
use App\Models\PAS\SaleOrder;
use App\Models\PAS\SaleOutWarehouse;
use App\Models\PAS\SaleOutWarehouseGoods;
use App\Models\PAS\SaleReturnInWarehouse;
use App\Models\PAS\SaleReturnInWarehouseGoods;
use App\Models\PAS\SaleReturnOrder;
use App\Models\PAS\SaleReturnOrderGoods;
use App\Models\PAS\Warehouse\WarehouseDeliveryType;
use App\Models\Workflow\Proc;
use App\Repositories\ParentRepository;
use App\Constant\ConstFile;
use DB;
use Exception;
use App\Services\Workflow\FlowCustomize;
use App\Models\Workflow\Entry;
use Hprose\Http\Client;


class SaleOrderRepository extends ParentRepository
{
    private $invoice_field = [
        'shipping_id' => '请选择物流方式',
        'network_id' => '请选择网点',
        'waybill_number' => '请填写运货单号',
        'province' => '请选择收货地址',
        'city' => '请选择收货地址',
        'county' => '请选择收货地址',
        'consignee' => '请填写收件人',
        'mobile' => '请填写联系电话'
    ];

    private $order_field = [
        'order_sn' => '请填写销售单号',
        'buy_user_id' => '请选择客户',
        'actual_money' => '请填写实收金额',
        'expected_pay_time' => '请填写预付款时间',
        'bank_name' => '请填写开户行',
        //'subbranch' => '请填写开户行支行',
        'bank_account' => '请填写银行账户',
        'account_holder' => '请填写开户人',
        'account_period' => '请填写账期',
        'sale_user_id' => '请填写销售员',
        'invoice_id' => '请填写发货方式',
        'business_time' => '请填写业务时间'
    ];

    //销售单状态 0草稿 1审核中 2已撤回 3已退回 4审核完成(待出库) 5部分出库 6出库完成
    public $out_status = [
        '0' => '草稿',
        '1' => '审核中',
        '2' => '已撤回',
        '3' => '已驳回',
        '4' => '待出库',//审核完成
        '5' => '部分出库',
        '6' => '全部出库'
    ];

    //退货单状态
    public $in_status = [
        '0' => '草稿',
        '1' => '审核中',
        '2' => '已撤回',
        '3' => '已驳回',
        '4' => '待入库',//审核完成
        '5' => '部分入库',
        '6' => '全部入库'
    ];

    public function __construct(){

    }


    /*
     * 添加修改销售发货单
     * */
    public function editSaleInvoice($param){
        try{
            $error = $this->checkInvoiceData($param);
            if($error){
                return returnJson($error, ConstFile::API_RESPONSE_FAIL);
            }

            $id = 0;
            if(!empty($param['id'])){
                $res = SaleInvoice::where('id', $param['id'])->update($param['need']);
                if($res){
                    $id = $param['id'];
                }
            }else{
                $res = SaleInvoice::create($param['need']);
                if($res){
                    $id = $res->id;
                }
            }
            if($id){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 获取用户发货地址
     * */
    public function getUserAddress($param){
        try{
            if(empty($param['uid'])){
                return returnJson('请选择客户', ConstFile::API_RESPONSE_FAIL);
            }

            $list = WarehouseDeliveryType::where('user_id', $param['uid'])->get()->toArray();
            if($param['type']){
                $data = $list;
            }else{
                $data = $list ? $list[0] : [];
            }
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加修改销售单
     * */
    public function editSaleOrder($param, $user){
        try{
            $error = $this->checkSaleOrderData($param);
            if($error['status'] == 0){
                return returnJson($error['msg'], ConstFile::API_RESPONSE_FAIL);
            }
            $goods = $error['goods'];
            $cost = $error['cost'];

            $info = [];
            if(!empty($param['id'])){
                $info = SaleOrder::where([['id', $param['id']], ['user_id', $user->id]])->first();
                if(empty($info)){
                    return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
                }
            }

            $id = 0;
            DB::transaction(function () use ($param, $goods, $cost, $user, $info, &$id) {
                $data = $param['need'];
                $data['status'] = $param['status'];

                $cost_money = !empty($cost) ? array_sum(array_column($cost, 'money')) : 0;//费用
                $goods_info = $this->handleSaleOrderGoods($goods);//选择的商品信息数据处理

                if(!empty($goods_info)){
                    $data['user_id'] = $user->id;
                    $data['other_money'] = $cost_money;
                    $data['goods_num'] = array_sum(array_column($goods_info, 'num'));
                    $total_money = array_sum(array_column($goods_info, 'money'));
                    $data['goods_money'] = $total_money;
                    $data['total_money'] = $data['receivable_money'] = (round($total_money*$data['discount']*100)/100/100) - $data['other_money'] - $data['zero_money'];

                    if(!empty($param['id'])){
                        if(!$info['entrise_id'] && $data['status'] == 1){
                            //提交审批流程，添加数据
                            $dataOne['title'] = '销售申请单号为'.trim($data['order_sn']);
                            $entry = FlowCustomize::EntryFlow($dataOne, 'pas_sale_orders');//添加进销存销售单 审核流程
                            $data['entrise_id'] = $entry->id;

                        }
                        $res = $this->handleEditSaleOrder($data, $info, $goods_info, $cost);
                        $id = $res;
                    }else{
                        if($data['status'] == 1){
                            //提交审批流程，添加数据
                            $dataOne['title'] = '销售申请单号为'.trim($data['order_sn']);
                            $entry = FlowCustomize::EntryFlow($dataOne, 'pas_sale_orders');//添加进销存销售单 审核流程
                            $data['entrise_id'] = $entry->id;
                        }
                        $res = $this->handleAddSaleOrder($data, $goods_info, $cost);
                        $id = $res;
                    }
                }
            });

            if($id){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加销售单处理
     * */
    public function handleAddSaleOrder($data, $goods_info, $cost){
        $id = 0;
        $apply = SaleOrder::create($data);
        if($apply){
            $res = 0;
            //商品处理
            foreach($goods_info as &$v){
                $v['order_id'] = $apply->id;
                $v['user_id'] = $data['buy_user_id'];
                $v['created_at'] = date('Y-m-d H:i:s',time());
                $v['updated_at'] = date('Y-m-d H:i:s',time());
            }
            $res = SaleOrderGoods::insert($goods_info);

            //费用处理
            if(!empty($cost)){
                foreach($cost as $key=>&$val){
                    $val['type'] = 3;
                    $val['code_id'] = $apply->id;
                    $val['created_at'] = date('Y-m-d H:i:s',time());
                    $val['updated_at'] = date('Y-m-d H:i:s',time());
                    unset($val['id']);
                }
                $res = CostInformation::insert($cost);//添加费用信息
            }

            //$res1 = SaleInvoice::where('id', $data['invoice_id'])->update(['order_id'=>$res->id]);
            if($res){
                $id = $apply->id;
            }
        }
        return $id;
    }


    /*
     * 修改销售单处理
     * */
    public function handleEditSaleOrder($data, $info, $goods_info, $cost){
        $result = $this->differenceSelectSaleGoods($info, $goods_info, $data); //print_r($result);die;

        $id = 0;
        $res = SaleOrder::where('id', $info['id'])->update($data);
        if($res){
            //商品处理
            if(!empty($result['add_data'])){
                SaleOrderGoods::insert($result['add_data']);
            }
            if(!empty($result['up_data'])){
                $this->updateBatch('pas_sale_order_goods', $result['up_data']);
            }
            if(!empty($result['del_data'])){
                SaleOrderGoods::where('order_id', $info['id'])->whereIn('id', $result['del_data'])->delete();
            }

            //费用处理
            if(!empty($cost)){
                $has_cost = CostInformation::where([['type', 3], ['code_id', $info['id']]])->pluck('id')->toArray();
                $add_cost = $up_cost = [];
                foreach($cost as $key=>$val){
                    if(!empty($val['id'])){
                        $cost_id = $val['id'];
                        unset($val['id']);
                        $up_cost[] = array_merge(['id'=>$cost_id], $val);
                    }else{
                        $val['type'] = 3;
                        $val['code_id'] = $info['id'];
                        $val['created_at'] = date('Y-m-d H:i:s',time());
                        $val['updated_at'] = date('Y-m-d H:i:s',time());
                        unset($val['id']);
                        $add_cost[] = $val;
                    }
                }
                if(!empty($add_cost)){
                    CostInformation::insert($add_cost);//添加费用信息
                }
                if(!empty($up_cost)){
                    $this->updateBatch('pas_cost_information', $up_cost);
                }
                //print_r($has_cost);print_r($up_cost);die;
                if(!empty($up_cost)){
                    $del_cost = array_diff($has_cost, array_column($up_cost, 'id'));
                    if(!empty($del_cost)){
                        CostInformation::whereIn('id', $del_cost)->where('type', 3)->update(['status'=>0]);
                    }
                }
            }
            /*if($info['invoice_id'] !== $data['invoice_id']){
                SaleInvoice::where([['id', $info['invoice_id']], ['order_id', $info['id']]])->delete();
                SaleInvoice::where('id', $data['invoice_id'])->update(['order_id'=>$info['id']]);
            }*/
            $id = $info['id'];
        }
        return $id;
    }


    /*
     * 修改销售单，商品添加删除修改分类处理
     * */
    private function differenceSelectSaleGoods($info, $goods_info, $data){
        $order_goods = SaleOrderGoods::where('order_id', $info['id'])->select()->get()->toArray();

        if(!empty($goods_info) && !empty($order_goods)){
            $key1 = $key2 = $add_data = $up_data = $del_data = [];
            foreach($goods_info as $k => $v){
                $key1[] = $v['goods_id'].'_'.$v['sku_id'];
                $goods_info[$v['goods_id'].'_'.$v['sku_id']] = $v;
                unset($goods_info[$k]);
            }
            foreach($order_goods as $k => $v){
                $key2[] = $v['goods_id'].'_'.$v['sku_id'];
                $order_goods[$v['goods_id'].'_'.$v['sku_id']] = $v;
                unset($order_goods[$k]);
            }
            //print_r($order_goods);print_r($goods_info);die;
            $add_ids = array_diff($key1, $key2);
            $del_ids = array_diff($key2, $key1);
            $up_ids = array_intersect($key1, $key2); //print_r($add_ids);print_r($del_ids);print_r($up_ids);die;

            foreach($add_ids as $v){
                $da = $goods_info[$v];
                $da['order_id'] = $info['id'];
                $da['user_id'] = $data['buy_user_id'];
                $da['created_at'] = date('Y-m-d H:i:s',time());
                $da['updated_at'] = date('Y-m-d H:i:s',time());
                $add_data[] = $da;
            }
            foreach($up_ids as $v){
                $goods_info[$v]['user_id'] = $data['buy_user_id'];
                $goods_info[$v]['updated_at'] = date('Y-m-d H:i:s',time());
                $up_data[] = array_merge(['id'=>$order_goods[$v]['id']], $goods_info[$v]);
            }
            foreach($del_ids as $v){
                $del_data[] = $order_goods[$v]['id'];
            }
        }
        return ['add_data'=>$add_data, 'up_data'=>$up_data, 'del_data'=>$del_data];
    }


    /*
     * 处理选择购买的商品数据
     * */
    private function handleSaleOrderGoods($goods){
        $data = [];
        if(!empty($goods)){
            $goods_ids = $sku = [];
            foreach($goods as $v){
                $goods_ids[] = $v['goods_id'];
                foreach($v['item'] as $vv){
                    $sku[] = $vv['sku_id'];
                }
            }
            $sku = array_unique($sku);

            $goods_info = Goods::whereIn('goods_id', $goods_ids)->whereNull('deleted_at')->where('status', 1)->get(['goods_id','goods_name','thumb_img', 'goods_sn','cost_price', 'price', 'wholesale_price'])->toArray();
            $goods_info = array_field_as_key($goods_info, 'goods_id');

            $sku_info = GoodsSpecificPrice::whereIn('id', $sku)->whereNull('deleted_at')->select('id', 'goods_id', 'key', 'key_name', 'cost_price', 'price', 'wholesale_price', 'store_count','sku', 'sku_name')->get()->toArray();
            $sku_info = array_field_as_key($sku_info, 'id');

            foreach($goods as $v){
                $goods_one = !empty($goods_info[$v['goods_id']]) ? $goods_info[$v['goods_id']] : [];
                if(!empty($goods_one)){
                    foreach($v['item'] as $v1){
                        $sku_one = !empty($sku_info[$v1['sku_id']]) ? $sku_info[$v1['sku_id']] : [];
                        $data[] = [
                            'goods_id' => $v['goods_id'],
                            'sku_id' => $v1['sku_id'],
                            'cost_price' => !empty($sku_one['cost_price']) ? $sku_one['cost_price'] : $goods_one['cost_price'],
                            'sale_price' => !empty($sku_one['price']) ? $sku_one['price'] : $goods_one['price'],
                            'wholesale_price' => !empty($sku_one['wholesale_price']) ? $sku_one['wholesale_price'] : $goods_one['wholesale_price'],
                            'price' => $v1['price'],
                            'discount' => $v1['discount'],
                            'num' => $v1['num'],
                            'money' => !empty($v1['discount']) ? $v1['num'] * $v1['price'] * $v1['discount']/100 : $v1['num'] * $v1['price']
                        ];
                    }
                }
            }
        }
        return $data;
    }


    /*
     * 销售单详情
     * */
    public function saleOrderInfo($param, $user){
        try{
            if(empty($param['id'])){
                return returnJson('请选择销售单', ConstFile::API_RESPONSE_FAIL);
            }
//            $info = SaleOrder::where('id', $param['id'])->with('goods')->with(['invoice'=>function($query){
//                $query->select('id','shipping_id');
//            }])->first()->toArray();

            //$user->id = 1792;
            if(empty($param['type'])){
                $where = ['entrise_id'=>$param['id']];
            }else{
                $where = ['id'=>$param['id']];
            }
            $info = SaleOrder::where($where)->with('goods')->with('invoice')->first()->toArray();
            $data = $this->saleOrderDetail($info);
            $data['is_handle'] = $data['handle_id'] = 0;
            if($info['entrise_id']){
                $entry = Entry::findOrFail($info['entrise_id']);
                $data['process'] = app()->make(ReturnOrderRepository::class)->fetchEntryProcess($entry);
                $entry_info = Proc::findUserProcByEntryId($user->id, $info['entrise_id']);
                $data['is_handle'] = (!empty($entry_info) && $entry_info->status == Proc::STATUS_IN_HAND) ? 1 : 0;
                $data['handle_id'] = (!empty($entry_info) && $entry_info->status == Proc::STATUS_IN_HAND) ? $entry_info->id : 0;
            }
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 销售单详细信息处理
     * $info 销售单信息
     * */
    public function saleOrderDetail($info){//print_r($info);print_r($out);die;
        $user_id = [$info['user_id'], $info['sale_user_id']];
        $user_id = array_unique(array_filter($user_id));
        $user_info = DB::table('users')->whereIn('id', $user_id)->pluck('chinese_name', 'id')->toArray();
        $info['user_name'] = !empty($user_info[$info['user_id']]) ? $user_info[$info['user_id']] : '';
        $info['sale_user_name'] = !empty($user_info[$info['sale_user_id']]) ? $user_info[$info['sale_user_id']] : '';

        $info['buy_user_name'] = '';
        if(!empty($info['buy_user_id'])){
            $customer = app()->make(\App\Repositories\RpcRepository::class)->getCustomerById($info['buy_user_id']);
            $info['buy_user_name'] = empty($customer['cusname']) ? '' : $customer['cusname'];
        }

        $info['status_name'] = $this->out_status[$info['status']];
        $info['goods_num'] = array_sum(array_column($info['goods'], 'num'));
        $info['shipping_name'] = !empty($info['invoice']) ? $info['invoice']['title'] : '';
        unset($info['invoice']);

        $goods_info = [];
        $goods = array_field_as_key($info['goods'], 'goods_id');
        foreach($goods as $v){
            $da = [
                'goods_id' => $v['goods_id'],
                'goods_sn' => $v['goods_sn'],
                'goods_name' => $v['goods_name'],
                'thumb_img' => $v['thumb_img']
            ];
            foreach($info['goods'] as $v1){
                if($v1['goods_id'] == $v['goods_id']){
                    $sku_name = explode('+', $v1['sku_name']);
                    $child = [
                        'id' => $v1['id'],
                        'sku_id' => $v1['sku_id'],
                        'price' => $v1['price'],
                        'num' => $v1['num'],
                        'money' => $v1['money'],
                        'sku_name' => $sku_name,
                        'out_num' => $v1['out_num'],
                        'apply_out_num' => $v1['apply_out_num'],
                        'back_num' => $v1['back_num'],
                        'apply_back_num' => $v1['apply_back_num'],
                        'lave_out_num' => ($v1['num'] - $v1['out_num'] - $v1['back_num'] - $v1['apply_out_num'] - $v1['apply_back_num']),
                        'lave_back_num' => ($v1['num'] - $v1['out_num'] - $v1['back_num'] - $v1['apply_out_num'] - $v1['apply_back_num']),
                    ];
                    $da['child'][] = $child;
                }
            }
            $goods_info[] = $da;
        }
        $info['goods'] = $goods_info;
        return $info;
    }


    /*
     * 销售单列表
     * */
    public function saleOrderList($param){
        try{
            $list = SaleOrder::leftJoin('users', 'users.id', '=', 'pas_sale_orders.buy_user_id')->select('pas_sale_orders.*', 'users.chinese_name');

            if(isset($param['status']) && !in_array(-1, $param['status'])){
                $list->whereIn('pas_sale_orders.status', $param['status']);
            }
            if(!empty($param['buy_user_id'])){
                $list->where('pas_sale_orders.buy_user_id', $param['buy_user_id']);
            }
            if(!empty($param['sale_user_id'])){
                $list->where('pas_sale_orders.sale_user_id', $param['sale_user_id']);
            }
            if(!empty($param['stime'])){
                $list->where('pas_sale_return_orders.updated_at', '>=', $param['stime']);
            }
            if(!empty($param['etime'])){
                $list->where('pas_sale_return_orders.updated_at', '<=', $param['etime']);
            }
            if(!empty($param['min_money'])){
                $list->where('pas_sale_return_orders.refunded_money', '>=', $param['min_money']);
            }
            if(!empty($param['max_money'])){
                $list->where('pas_sale_return_orders.refunded_money', '<=', $param['max_money']);
            }
            $list = $list->paginate($param['limit'])->toArray();

            $data = [];
            if(!empty($list['data'])){
                foreach($list['data'] as $v){
                    $da = [
                        'id' => $v['id'],
                        'user_name' => $v['chinese_name'],
                        'order_sn' => $v['order_sn'],
                        'time' => $v['updated_at'],
                        'num' => $v['goods_num'],
                        'money' => $v['total_money'],
                        'actual_money' => $v['actual_money'],
                        'balance' => $v['total_money'] - $v['actual_money'],
                        'is_finish' => ($v['total_money'] - $v['actual_money']) > 0 ? 0 : 1,
                        'order_status_name' => $v['status'] >= 4 ? '已审核' : $this->out_status[$v['status']],
                        'out_status_name' => $v['status'] >= 4 ? $this->out_status[$v['status']] : '',
                        'pay_status_name' => ($v['total_money'] - $v['actual_money']) > 0 ? ($v['total_money'] - $v['actual_money']) : '已结清'
                    ];
                    $data[] = $da;
                }
            }

            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $data;

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 修改销售单购买的商品
     * */
    public function changeBuyGoods($param){
        try{
            $error = $this->checkAddSaleGoodsData($param);
            if($error){
                return returnJson($error, ConstFile::API_RESPONSE_FAIL);
            }
            $goods = htmlspecialchars_decode($param['goods']);
            $goods = json_decode($goods,true);

            $data = $this->handleBuyGoods($goods);
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 撤销销售单
     * */
    public function cancelSaleOrder($param, $user){
        try{
            if(empty($param['id'])){
                return returnJson('请选择销售单', ConstFile::API_RESPONSE_FAIL);
            }
            $info = SaleOrder::where([['id', $param['id']], ['status', 1], ['user_id', $user->id]])->select('entrise_id','id')->first()->toArray();
            if(empty($info)){
                return returnJson(ConstFile::API_DELETE_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }

            $tag = 0;
            DB::transaction(function() use($user, $param, $info, &$tag) {
                $res = 1;
                if($info['entrise_id']){
                    $res = app()->make(ReturnOrderRepository::class)->cancel($info['entrise_id'], '', $user);//审批流撤销
                    if($res){
                        $data['status']=2;
                        $data['updated_at']=date('Y-m-d H:i:s',time());
                        $res = SaleOrder::where('id', $info['id'])->update($data);
                    }
                }
                if ($res) {
                    $tag = 1;
                }
            });

            if($tag){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 出库单详情
     * */
    public function saleOutWarehouseInfo($param, $user){
        try{
            if(empty($param['id'])){
                return returnJson('请选择出库单', ConstFile::API_RESPONSE_FAIL);
            }
            $info = SaleOutWarehouse::where('id', $param['id'])->with('out_goods')->first()->toArray();
            if(empty($info)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
            $out_goods = array_field_as_key($info['out_goods'], 'sale_order_goods_id');

            $sale = SaleOrder::where([['id', $info['order_id']],['status', 4]])->with('goods')->first()->toArray();
            if(empty($sale)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
            $data = $this->saleOrderDetail($sale);

            $data['is_handle'] = 0;
            if($info['entrise_id']){
                $entry = Entry::findOrFail($info['entrise_id']);
                $data['process'] = app()->make(ReturnOrderRepository::class)->fetchEntryProcess($entry);
                $entry_info = Proc::findUserProcByEntryId($user->id, $info['entrise_id']);
                $data['is_handle'] = (!empty($entry_info) && $entry_info->status == Proc::STATUS_IN_HAND) ? 1 : 0;
                $data['handle_id'] = (!empty($entry_info) && $entry_info->status == Proc::STATUS_IN_HAND) ? $entry_info->id : 0;
            }

            $data['remark'] = $info['remark'];
            $data['out_time'] = $info['out_time'];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加修改销售出库单
     * */
    public function saleOutOfWarehouse($param, $user){
        try{
            $error = $this->checkOutWarehostData($param);
            throw_if(empty($error['status']), new Exception($error['msg']));

            $info = [];
            if(!empty($param['id'])){
                $info = SaleOutWarehouse::where([['id', $param['id']], ['user_id', $user->id]])->with('out_goods')->first();
                throw_if(empty($info), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));
                $out_goods = array_field_as_key($info['out_goods'], 'sale_order_goods_id');
            }

            $sale = SaleOrder::where([['id', $param['order_id']],['status', 4]])->with('goods')->select('id', 'order_sn', 'user_id')->first()->toArray();
            throw_if(empty($sale), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));
            $order_goods = array_field_as_key($sale['goods'], 'id');

            $goods = [];
            $out_num = 0;
            foreach($error['item'] as $v){
                throw_if(empty($order_goods[$v['id']]), new Exception('请选择出库商品'));
                $da = $order_goods[$v['id']];
                if(empty($out_goods)){
                    $numable = $da['num']-$da['out_num']-$da['back_num']-$da['apply_out_num']-$da['apply_back_num'];
                }else{
                    $numable = $da['num']-$da['out_num']-$da['back_num']-$da['apply_out_num']-$da['apply_back_num'] + $out_goods[$v['id']]['out_num'];
                }
                throw_if($v['num'] > $numable, new Exception('请填写合理的出库数量'));

                $da['out_num'] = $v['num'];
                $goods[] = $da;
                $out_num += $v['num'];
            }
            //print_r($order_goods);print_r($goods);die;
            $id = 0;
            DB::transaction(function () use ($param, $goods, $sale, $info, $out_num, $user, &$id) {
                $num = array_sum(array_column($goods, 'num'));//销售单商品总购买量
                $data = [
                    'num' => $num,
                    'out_num' => $out_num,
                    'out_time' => $param['out_time'],
                    'remark' => $param['remark'],
                    'status' => $param['status'],
                ];

                if(empty($param['id'])){
                    $data['user_id'] = $user->id;
                    $data['out_sn'] = $this->getCodes('OW');
                    $data['order_id'] = $sale['id'];
                    $data['order_sn'] = $sale['order_sn'];

                    if($data['status'] == 1){
                        //提交审批流程，添加数据
                        $dataOne['title'] = '销售出库申请单号为'.trim($data['order_sn']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_sale_out_warehouse');//添加进销存销售单 审核流程
                        $data['entrise_id'] = $entry->id;
                    }
                    $id = $this->addSaleOutOfWarehouse($data, $goods);
                }else{
                    $data['id'] = $param['id'];
                    $data['updated_at'] = date('Y-m-d H:i:s', time());

                    if(!$info['entrise_id'] && $data['status'] == 1){
                        //提交审批流程，添加数据
                        $dataOne['title'] = '销售出库申请单号为'.trim($info['order_sn']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_sale_orders');//添加进销存销售单 审核流程
                        $data['entrise_id'] = $entry->id;
                    }
                    $id = $this->editSaleOutOfWarehouse($data, $goods, $info);
                }
            });

            if($id){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加销售出库单
     * */
    private function addSaleOutOfWarehouse($data, $goods){
        $apply = SaleOutWarehouse::create($data);

        $id = 0;
        if($apply){
            foreach($goods as $v){
                $goods_data[] = [
                    'out_id' => $apply->id,
                    'sale_order_goods_id' => $v['id'],
                    'goods_id' => $v['goods_id'],
                    'sku_id' => $v['sku_id'],
                    'out_num' => $v['out_num'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                ];
                if($data['status']){
                    $up_sale_goods[] = [
                        'id' => $v['id'],
                        'apply_out_num' => '`apply_out_num`+' . $v['out_num'],
                    ];
                }
            }

            if(!empty($up_sale_goods)){
                $res = $this->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
            }
            if(!empty($goods_data)){
                $res = SaleOutWarehouseGoods::insert($goods_data);
            }

            if($res){
                $id = $apply->id;
            }
        }
        return $id;
    }


    /*
     * 修改销售出库单
     * */
    private function editSaleOutOfWarehouse($data, $goods, $info){//print_r($data); print_r($goods);die;
        $id = 0;

        $res = SaleOutWarehouse::where('id', $data['id'])->update($data);
        if($res){
            $goods = array_field_as_key($goods, 'id');
            $select_goods = array_keys($goods);
            //$has_goods = SaleOutWarehouseGoods::where('out_id', $data['id'])->pluck('sale_order_goods_id', 'id')->toArray();//print_r($goods);print_r($select_goods);print_r($has_goods);//die;
            $out_goods = SaleOutWarehouseGoods::where('out_id', $data['id'])->get(['out_num', 'sale_order_goods_id', 'id'])->toArray();
            foreach($out_goods as $v){
                $has_goods[$v['id']] = $v['sale_order_goods_id'];
                $has_out_goods[$v['id']] = $v['out_num'];
            }
            //print_r($has_out_goods);print_r($has_goods);die;

            $add_ids = array_diff($select_goods, $has_goods);
            $up_ids = array_intersect($has_goods, $select_goods);
            $del_ids = array_diff($has_goods, $select_goods);//print_r($add_ids);print_r($up_ids);print_r($del_ids);print_r($goods);die;

            $add_data = $up_data = $del_data = $num = $up_sale_goods =  [];
            if(!empty($add_ids)){
                foreach($add_ids as $v){
                    $g_info = $goods[$v];
                    $add_data[] = [
                        'out_id' => $data['id'],
                        'sale_order_goods_id' => $v,
                        'goods_id' => $g_info['goods_id'],
                        'sku_id' => $g_info['sku_id'],
                        'out_num' => $g_info['out_num'],
                        'status' => $data['status'],
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];
                    if($data['status']){
                        $up_sale_goods[] = [
                            'id' => $v,
                            'apply_out_num' => '`apply_out_num`+' . $g_info['out_num'],
                        ];
                        $num[] = $g_info['out_num'];
                    }
                }
                $res = SaleOutWarehouseGoods::insert($add_data);
            }
            if(!empty($up_ids)){
                foreach($up_ids as $k=>$v){
                    $g_info = $goods[$v];
                    $up_data[] = [
                        'id' => $k,
                        'out_num' => $g_info['out_num'],
                        'status' => $data['status'],
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];

                    if($data['status']){
                        if($info['status'] == 1){
                            //上次已提交
                            $up_sale_goods[] = [
                                'id' => $v,
                                'apply_out_num' => '`apply_out_num`+' . ($g_info['out_num']-$has_out_goods[$k]),
                            ];
                            $num[] = $g_info['out_num']-$has_out_goods[$k];
                        }else if($info['status'] == 0){
                            //上次为草稿
                            $up_sale_goods[] = [
                                'id' => $v,
                                'apply_out_num' => '`apply_out_num`+' . $g_info['out_num'],
                            ];
                            $num[] = $g_info['out_num'];
                        }
                    }
                }
                $res = $this->updateBatch('pas_sale_out_warehouse_goods', $up_data);
            }
            if(!empty($del_ids)){
                if($info['status'] == 1){
                    //上次已提交
                    foreach($del_ids as $k=>$v){
                        $up_sale_goods[] = [
                            'id' => $v,
                            'apply_out_num' => '`apply_out_num`-' . $has_out_goods[$k],
                        ];
                        $num[] = $has_out_goods[$k];
                    }
                }
                $del_ids = array_filter(array_unique(array_keys($del_ids)));
                $res = SaleOutWarehouseGoods::whereIn('id', $del_ids)->where('out_id', $data['id'])->delete();
            }

            $num = array_filter($num);
            if(!empty($up_sale_goods) && count($num)){
                $res = $this->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
            }

            if($res){
                $id = $data['id'];
            }
        }
        return $id;
    }


    /*
     * 销售出库单撤回
     * */
    public function cancelSaleOutWarehouse($param, $user){
        try{
            throw_if(empty($param['id']), new Exception('请选择销售出库单'));
            $info = SaleOutWarehouse::where([['id', $param['id']], ['status', 1], ['user_id', $user->id]])->with('out_goods')->select('entrise_id','id', 'status')->first()->toArray();
            throw_if(empty($info), new Exception(ConstFile::API_DELETE_RECORDS_NOT_EXIST_MESSAGE));
            
            $res = 0;
            DB::transaction(function() use($user, $param, $info, &$res) {
                if($info['entrise_id']){
                    $res = app()->make(ReturnOrderRepository::class)->cancel($info['entrise_id'], '', $user);//审批流撤销

                    if($res){
                        $data['status']=2;
                        $data['updated_at']=date('Y-m-d H:i:s',time());
                        $res = SaleOutWarehouse::where('id', $info['id'])->update($data);
                        $res = SaleOutWarehouseGoods::where('out_id', $info['id'])->update(['status'=>0]);

                        $up_sale_goods = [];
                        foreach($info['out_goods'] as $v){
                            $up_sale_goods[] = [
                                'id' => $v['sale_order_goods_id'],
                                'apply_out_num' => '`apply_out_num`-'.$v['out_num']
                            ];
                        }

                        if(!empty($up_sale_goods)){
                            $res = $this->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
                        }
                    }
                }
            });

            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 出库申请列表
     * */
    public function saleOutList($param, $user){
        try{
            $list = SaleOutWarehouse::where('user_id', $user->id);
            if($param['status']== 1){
                //待出库
                $list->whereIn('status', [1,4])->where('out_num', 0);
            }else if($param['status'] == 2){
                //已出库
                $list->where('status', 4)->where('out_num', '=', 'num');
            }else if($param['status'] == 3){
                //部分出库
                $list->where('status', 4)->whereRaw('out_num < num')->where('out_num', '>', 0);
            }else if($param['status'] == 4){
                //草稿
                $list->where('status', 0);
            }else{
                $list->whereIn('status', [2,3]);
            }
            $list = $list->with('sale_order')->with(['out_goods'=>function($query){
                $query->with('goods');
            }])->paginate($param['limit'])->toArray();//print_r($list);die;

            $data = [];
            if(!empty($list['data'])){
                $sale_order = array_column($list['data'], 'sale_order');//print_r($sale_order);die;
                $buy_user_ids = array_unique(array_column($sale_order, 'buy_user_id'));//print_r($buy_user_ids);die;

                $client = new Client(config('app.customer_url'), false);
                $customer = $client->getCustomerByIds($buy_user_ids);
                $customer = array_field_as_key($customer, 'id');

                foreach($list['data'] as $v){
                    $goods = !empty($v['out_goods'][0]) ? $v['out_goods'][0] : [];
                    $data[] = [
                        'id' => $v['id'],
                        'buy_user_name' => !empty($customer[$v['sale_order']['buy_user_id']]) ? $customer[$v['sale_order']['buy_user_id']]['cusname'] : '',
                        'goods_name' => $goods ? $goods['goods']['goods_name'] : '',
                        'sale_order_sn' => $v['sale_order']['order_sn'],
                        'created_at' => $v['created_at'],
                        'num' => $v['num'],
                        'out_num' => $v['out_num'],
                        'lave_num' => $v['num'] - $v['out_num']
                    ];
                }
            }

            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $data;
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加修改退货单
     * */
    public function editReturnOrder($param, $user){
        try{
            $error = $this->checkReturnOrderData($param);
            throw_if(empty($error['status']), new Exception($error['msg']));

            $return_goods = $error['goods'];
            $cost = $error['cost'];

            $info = [];
            if(!empty($param['id'])){
                $info = SaleReturnOrder::where('id', $param['id'])->first()->toArray();
                throw_if(empty($info), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));
            }

            $sale = SaleOrder::where('id', $param['need']['sale_order_id'])->with('goods')->first()->toArray();
            throw_if(empty($sale), new Exception('销售单不存在'));
            $order_goods = array_field_as_key($sale['goods'], 'id');//print_r($sale);print_r($order_goods);die;

            $goods = [];
            foreach($return_goods as $v){
                throw_if(empty($order_goods[$v['id']]), new Exception('请选择退货商品'));
                $da = $order_goods[$v['id']];
                throw_if($v['num'] > ($da['num']-$da['out_num']-$da['apply_out_num']-$da['back_num']-$da['apply_back_num']), new Exception('请填写合理的退货数量'));

                $da['return_num'] = $v['num'];
                $da['return_money'] = round($v['num']*$da['price']*$da['discount']*100)/100/100;
                $goods[] = $da;
            }

            $id = 0;
            DB::transaction(function () use ($param, $goods, $cost, $sale, $info, $user, &$id) {
                $cost_money = !empty($cost) ? array_sum(array_column($cost, 'money')) : 0;//费用
                $money = array_sum(array_column($goods, 'return_money'));
                $money = round($money * $sale['discount']*100)/100/100 - $cost_money;
//echo $money;die;
                $data = $param['need'];
                $data['total_money'] = $money;
                $data['refunded_money'] = $money;
                $data['other_money'] = $cost_money;
                $data['return_num'] = array_sum(array_column($goods, 'return_num'));

                if(empty($param['id'])){
                    //添加
                    $data['user_id'] = $user->id;
                    $data['order_money'] = $sale['total_money'];
                    $data['goods_num'] = $sale['goods_num'];

                    if($data['status'] == 1){
                        //提交审批流程，添加数据
                        $dataOne['title'] = '销售退货申请单号为'.trim($data['order_sn']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_sale_return_orders');//添加销售退货单 审核流程
                        $data['entrise_id'] = $entry->id;
                    }
                    $id = $this->addSaleReturnOrder($data, $goods, $cost);
                }else{
                    //修改
                    $data['id'] = $param['id'];
                    $data['updated_at'] = date('Y-m-d H:i:s', time());

                    if(!$info['entrise_id'] && $data['status'] == 1){
                        //提交审批流程，添加数据
                        $dataOne['title'] = '销售退货申请单号为'.trim($data['order_sn']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_sale_return_orders');//添加销售退货单 审核流程
                        $data['entrise_id'] = $entry->id;
                    }
                    $id = $this->editSaleReturnOrder($data, $goods, $cost, $info);
                }
            });

            if($id){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加销售退货单
     * */
    private function addSaleReturnOrder($data, $goods, $cost){//print_r($data);print_r($goods);die;
        $apply = SaleReturnOrder::create($data);

        $id = 0;
        if($apply){
            $res = 1;
            foreach($goods as $v){
                $goods_data[] = [
                    'return_order_id' => $apply->id,
                    'sale_order_id' => $v['order_id'],
                    'sale_order_goods_id' => $v['id'],
                    'goods_id' => $v['goods_id'],
                    'sku_id' => $v['sku_id'],
                    'goods_money' => $v['money'],
                    'return_money' => $v['return_money'],
                    'num' => $v['num'],
                    'status' => $data['status'],
                    'return_num' => $v['return_num'],
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                ];

                if($data['status']){
                    $up_sale_goods[] = [
                        'id' => $v['id'],
                        'apply_back_num' => '`apply_back_num`+' . $v['return_num'],
                    ];
                }
            }

            if(!empty($up_sale_goods)){
                $res = $this->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
            }
            if(!empty($goods_data)){
                $res = SaleReturnOrderGoods::insert($goods_data);
            }

            //费用处理
            if(!empty($cost)){
                foreach($cost as $key=>&$val){
                    $val['type'] = 4;
                    $val['code_id'] = $apply->id;
                    $val['created_at'] = date('Y-m-d H:i:s',time());
                    $val['updated_at'] = date('Y-m-d H:i:s',time());
                    unset($val['id']);
                }
                $res = CostInformation::insert($cost);//添加费用信息
            }

            if($res){
                $id = $apply->id;
            }
        }
        return $id;
    }


    /*
     * 修改销售退货单
     * */
    private function editSaleReturnOrder($data, $goods, $cost, $info){//print_r($data); print_r($goods);die;
        $id = 0;

        $res = SaleReturnOrder::where('id', $data['id'])->update($data);
        if($res){
            $goods = array_field_as_key($goods, 'id');
            $select_goods = array_keys($goods);
            $return_goods = SaleReturnOrderGoods::where('return_order_id', $data['id'])->get(['return_num', 'sale_order_goods_id', 'id'])->toArray();
            foreach($return_goods as $v){
                $has_goods[$v['id']] = $v['sale_order_goods_id'];
                $has_return_goods[$v['id']] = $v['return_num'];
            }
            //print_r($goods);print_r($select_goods);print_r($has_goods);print_r($has_return_goods);die;

            $add_ids = array_diff($select_goods, $has_goods);
            $up_ids = array_intersect($has_goods, $select_goods);
            $del_ids = array_diff($has_goods, $select_goods);
            //print_r($goods);print_r($add_ids);print_r($up_ids);print_r($del_ids);print_r($has_return_goods);die;

            $add_data = $up_data = $del_data = $up_sale_goods = $num = [];
            if(!empty($add_ids)){
                foreach($add_ids as $v){
                    $g_info = $goods[$v];
                    $add_data[] = [
                        'return_order_id' => $data['id'],
                        'sale_order_id' => $data['sale_order_id'],
                        'sale_order_goods_id' => $v,
                        'goods_id' => $g_info['goods_id'],
                        'sku_id' => $g_info['sku_id'],
                        'goods_money' => $g_info['money'],
                        'return_money' => $g_info['return_money'],
                        'num' => $g_info['num'],
                        'status' => $data['status'],
                        'return_num' => $g_info['return_num'],
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];

                    if($data['status']){
                        $up_sale_goods[] = [
                            'id' => $v,
                            'apply_back_num' => '`apply_back_num`+' . $g_info['return_num'],
                        ];
                    }
                }
                $res = SaleReturnOrderGoods::insert($add_data);
            }
            if(!empty($up_ids)){
                foreach($up_ids as $k=>$v){
                    $g_info = $goods[$v];
                    $up_data[] = [
                        'id' => $k,
                        'goods_money' => $g_info['money'],
                        'return_money' => $g_info['return_money'],
                        'num' => $g_info['num'],
                        'status' => $data['status'],
                        'return_num' => $g_info['return_num'],
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];

                    if($data['status']){
                        if($info['status'] == 1){
                            //上次已提交
                            $up_sale_goods[] = [
                                'id' => $v,
                                'apply_back_num' => '`apply_back_num`+' . ($g_info['return_num']-$has_return_goods[$k]),
                            ];
                            $num[] = $g_info['return_num']-$has_return_goods[$k];
                        }else if($info['status'] == 0){
                            //上次为草稿
                            $up_sale_goods[] = [
                                'id' => $v,
                                'apply_back_num' => '`apply_back_num`+' . $g_info['return_num'],
                            ];
                            $num[] = $g_info['return_num'];
                        }
                    }
                }
                $res = $this->updateBatch('pas_sale_return_order_goods', $up_data);
            }

            //费用处理
            if(!empty($cost)){
                $has_cost = CostInformation::where([['type', 4], ['code_id', $data['id']]])->pluck('id')->toArray();
                $add_cost = $up_cost = [];
                foreach($cost as $key=>$val){
                    if(!empty($val['id'])){
                        $cost_id = $val['id'];
                        unset($val['id']);
                        $up_cost[] = array_merge(['id'=>$cost_id], $val);
                    }else{
                        $val['type'] = 4;
                        $val['code_id'] = $data['id'];
                        $val['created_at'] = date('Y-m-d H:i:s',time());
                        $val['updated_at'] = date('Y-m-d H:i:s',time());
                        unset($val['id']);
                        $add_cost[] = $val;
                    }
                }
                //print_r($has_cost);print_r($up_cost);die;
                if(!empty($add_cost)){
                    $res = CostInformation::insert($cost);//添加费用信息
                }
                if(!empty($up_cost)){
                    $res = $this->updateBatch('pas_cost_information', $up_cost);

                    $del_cost = array_diff($has_cost, array_column($up_cost, 'id'));
                    if(!empty($del_cost)){
                        $res = CostInformation::whereIn('id', $del_cost)->where('type', 4)->update(['status'=>0]);
                    }
                }
            }

            if(!empty($del_ids)){
                if($info['status'] == 1){
                    //上次已提交
                    foreach($del_ids as $k=>$v){
                        $up_sale_goods[] = [
                            'id' => $v,
                            'apply_back_num' => '`apply_back_num`-' . $has_return_goods[$k],
                        ];
                        $num[] = $has_return_goods[$k];
                    }
                }
                $del_ids = array_filter(array_unique(array_keys($del_ids)));
                $res = SaleReturnOrderGoods::whereIn('id', $del_ids)->where('return_order_id', $data['id'])->delete();
            }
            $num = array_filter($num);
            if(!empty($up_sale_goods) && count($num)){
                $res = $this->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
            }

            if($res){
                $id = $data['id'];
            }
        }
        return $id;
    }


    /*
     * 销售退货单详情
     * */
    public function saleReturnOrderDetail($param, $user){
        try{
            throw_if(empty($param['id']), new Exception('请选择退货单'));

            $return = SaleReturnOrder::where('id', $param['id'])->with('return_goods')->first()->toArray();
            throw_if(empty($return), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));

            $return_goods = array_field_as_key($return['return_goods'], 'sale_order_goods_id');

            $sale = SaleOrder::where('id', $return['sale_order_id'])->whereIn('status', [4,6])->with('goods')->first()->toArray();
            throw_if(empty($sale), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));

            $return['sale_order_sn'] = $sale['order_sn'];
            $return['total_return_money'] = $return['total_money'];
            unset($return['return_goods'], $return['total_money'], $return['order_money'], $sale['order_sn']);
            $sale['user_id'] = $return['create_user_id'];
            $data = $this->orderShowDetail($sale, $return_goods);
            $data = array_merge($data, $return);
            $data['status_name'] = $this->in_status[$data['status']];

            if($return['entrise_id']){
                $entry = Entry::findOrFail($return['entrise_id']);
                $data['process'] = app()->make(ReturnOrderRepository::class)->fetchEntryProcess($entry);
                $entry_info = Proc::findUserProcByEntryId($user->id, $return['entrise_id']);
                $data['is_handle'] = (!empty($entry_info) && $entry_info->status == Proc::STATUS_IN_HAND) ? 1 : 0;
                $data['handle_id'] = (!empty($entry_info) && $entry_info->status == Proc::STATUS_IN_HAND) ? $entry_info->id : 0;
            }

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 相关订单详细信息展示
     * $info 销售单信息
     * $show_goods 退货商品
     * $return_in_order 退货入库商品
     * */
    private function orderShowDetail($info, $show_goods){//print_r($info);print_r($show_goods);print_r($return_in_order);die;
        $user_id = [$info['user_id'], $info['sale_user_id']];
        $user_id = array_unique(array_filter($user_id));
        $user_info = DB::table('users')->whereIn('id', $user_id)->pluck('chinese_name', 'id')->toArray();
        $info['user_name'] = !empty($user_info[$info['user_id']]) ? $user_info[$info['user_id']] : '';
        $info['sale_user_name'] = !empty($user_info[$info['sale_user_id']]) ? $user_info[$info['sale_user_id']] : '';
        $info['goods_num'] = array_sum(array_column($info['goods'], 'num'));
        $info['shipping_name'] = !empty($info['invoice']) ? $info['invoice']['title'] : '';
        unset($info['invoice']);

        $info['buy_user_name'] = '';
        if(!empty($info['buy_user_id'])){
            $customer = app()->make(\App\Repositories\RpcRepository::class)->getCustomerById($info['buy_user_id']);
            $info['buy_user_name'] = empty($customer['cusname']) ? '' : $customer['cusname'];
        }

        $goods_info = [];
        $goods = array_field_as_key($info['goods'], 'goods_id');//销售单的商品
        $goods_ids = array_filter(array_unique(array_column($show_goods, 'goods_id')));
        $sale_goods = array_field_as_key($info['goods'], 'id');

        /*$in_goods = [];
        if(!empty($return_in_order)){
            foreach($return_in_order as $k=>$v){
                foreach ($v['in_goods'] as $kk=>$vv){
                    $in_goods[$vv['return_order_goods_id']][] = $vv['in_num'];
                }
            }
        }*/

        foreach ($goods_ids as $v){
            $da = [
                'goods_id' => $goods[$v]['goods_id'],
                'goods_sn' => $goods[$v]['goods_sn'],
                'goods_name' => $goods[$v]['goods_name'],
                'thumb_img' => $goods[$v]['thumb_img']
            ];

            foreach($show_goods as $k=>$v1){//print_r($v1); print_r($sale_goods[$k]);die;
                if($v1['goods_id'] == $v){
                    $v1 = array_merge($v1, $sale_goods[$k]);
                    $sku_name = explode('+', $v1['sku_name']);
                    $child = [
                        'id' => $v1['id'],
                        'sku_id' => $v1['sku_id'],
                        'price' => $v1['price'],
                        'num' => $v1['num'],
                        'money' => $v1['money'],
                        'sku_name' => $sku_name,
                        'out_num' => $v1['out_num'],
                        'apply_out_num' => $v1['apply_out_num'],
                        'back_num' => $v1['back_num'],
                        'apply_back_num' => $v1['apply_back_num'],
                        'in_num' => $v1['in_num'],
                        'apply_in_num' => $v1['apply_in_num'],
                        'lave_out_num' => ($v1['num'] - $v1['out_num'] - $v1['back_num'] - $v1['apply_out_num'] - $v1['apply_back_num']),
                        'lave_back_num' => ($v1['num'] - $v1['out_num'] - $v1['back_num'] - $v1['apply_out_num'] - $v1['apply_back_num']),
                        'lave_in_num' => ($v1['back_num'] - $v1['in_num'] - $v1['apply_in_num'])
                    ];
                    $da['child'][] = $child;
                }
            }
            $goods_info[] = $da;
        }

        $info['goods'] = $goods_info;
        return $info;
    }


    /*
     * 退货单撤回
     * */
    public function cancelSaleReturnOrder($param, $user){
        try{
            throw_if(empty($param['id']), new Exception('请选择销售退货单'));
            $info = SaleReturnOrder::where([['id', $param['id']], ['status', 1], ['user_id', $user->id]])->with('return_goods')->select('entrise_id','id')->first()->toArray();
            throw_if(empty($info), new Exception(ConstFile::API_DELETE_RECORDS_NOT_EXIST_MESSAGE));

            $res = 0;
            DB::transaction(function() use($user, $param, $info, &$res) {
                if($info['entrise_id']){
                    $res = app()->make(ReturnOrderRepository::class)->cancel($info['entrise_id'], '', $user);//审批流撤销
                    if($res){
                        $data['status']=2;
                        $data['updated_at']=date('Y-m-d H:i:s',time());
                        $res = SaleReturnOrder::where('id', $info['id'])->update($data);
                        $res = SaleReturnOrderGoods::where('return_order_id', $info['id'])->update(['status'=>0]);

                        $up_sale_goods = [];
                        foreach($info['return_goods'] as $v){
                            $up_sale_goods[] = [
                                'id' => $v['sale_order_goods_id'],
                                'apply_back_num' => '`apply_back_num`-'.$v['return_num']
                            ];
                        }
                        if(!empty($up_sale_goods)){
                            $res = $this->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
                        }
                    }
                }
            });

            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 销售退货单列表
     * */
    public function saleReturnOrderList($param){
        try{
            //$list = SaleOrder::leftJoin('users', 'users.id', '=', 'pas_sale_orders.buy_user_id')->select('pas_sale_orders.*', 'users.chinese_name');
            $list = SaleReturnOrder::leftJoin('pas_sale_orders', 'pas_sale_orders.id', '=', 'pas_sale_return_orders.sale_order_id')
                ->select('pas_sale_return_orders.*', 'pas_sale_orders.buy_user_id', 'pas_sale_orders.actual_money', 'pas_sale_orders.total_money as sale_total_money');

            if(isset($param['status']) && !in_array(-1, $param['status'])){
                $list->where('pas_sale_return_orders.status', $param['status']);
            }
            if(!empty($param['buy_user_id'])){
                $list->where('pas_sale_orders.buy_user_id', $param['buy_user_id']);
            }
            if(!empty($param['sale_user_id'])){
                $list->where('pas_sale_orders.sale_user_id', $param['sale_user_id']);
            }
            if(!empty($param['stime'])){
                $list->where('pas_sale_return_orders.updated_at', '>=', $param['stime']);
            }
            if(!empty($param['etime'])){
                $list->where('pas_sale_return_orders.updated_at', '<=', $param['etime']);
            }
            if(!empty($param['min_money'])){
                $list->where('pas_sale_return_orders.refunded_money', '>=', $param['min_money']);
            }
            if(!empty($param['max_money'])){
                $list->where('pas_sale_return_orders.refunded_money', '<=', $param['max_money']);
            }
            $list = $list->paginate($param['limit'])->toArray();

            $data = [];
            if(!empty($list['data'])){
                $users = array_column($list['data'], 'buy_user_id');
                $client = new Client(config('app.customer_url'), false);
                $info = $client->getCustomerByIds($users);
                $info = array_field_as_key($info, 'id');

                foreach($list['data'] as $v){
                    $da = [
                        'id' => $v['id'],
                        'user_name' => empty($info[$v['buy_user_id']]['cusname']) ? '' : $info[$v['buy_user_id']]['cusname'],
                        'order_sn' => $v['order_sn'],
                        'time' => $v['updated_at'],
                        'return_num' => $v['return_num'],
                        'money' => $v['real_refund_money'],//退款金额
                        'sale_total_money' => $v['sale_total_money'],//销售金额
                        'balance' => $v['sale_total_money'] - $v['actual_money'],
                        'is_finish' => ($v['sale_total_money'] - $v['actual_money']) > 0 ? 0 : 1,
                        'status' => $v['status'],
                        'order_status_name' => $v['status'] >= 4 ? '已审核' : $this->in_status[$v['status']],
                        'in_status_name' => $v['status'] >= 4 ? $this->in_status[$v['status']] : '',
                        'pay_status_name' => ($v['sale_total_money'] - $v['actual_money']) > 0 ? ($v['sale_total_money'] - $v['actual_money']) : '已结清'
                    ];
                    $data[] = $da;
                }
            }

            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $data;
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 修改销售退货入库单详情
     * */
    public function editSaleReturnOrderInDetail($param){
        try{
            throw_if(empty($param['id']), new Exception('请选择退货单'));

            $return = SaleReturnOrder::where('id', $param['id'])->with('return_goods')->first()->toArray();
            throw_if(empty($return), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));
            $return_goods = array_field_as_key($return['return_goods'], 'sale_order_goods_id');//退货商品
            $sale = SaleOrder::where('id', $return['sale_order_id'])->whereIn('status', [4,6])->with('goods')->first()->toArray();
            throw_if(empty($sale), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));

            $return['sale_order_sn'] = $sale['order_sn'];
            $return['total_return_money'] = $return['total_money'];
            unset($return['return_goods'], $return['total_money'], $return['order_money'], $sale['order_sn'], $return['return_in_order']);
            $sale['user_id'] = $return['create_user_id'];
            $data = $this->orderShowDetail($sale, $return_goods);

            $data = array_merge($data, $return);
            $data['status_name'] = $this->in_status[$data['status']];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 退货入库单详情
     * */
    public function returnInWarehouseDetail($param, $user){
        try{
            if(empty($param['id'])){
                return returnJson('请选择入库单', ConstFile::API_RESPONSE_FAIL);
            }

            $apply = SaleReturnInWarehouse::where('id', $param['id'])->with('in_goods')->first()->toArray();//入库单信息
            if(empty($apply)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
            $return = SaleReturnOrder::where('id', $apply['return_order_id'])->with('return_goods')->first()->toArray();

            $sale = SaleOrder::where('pas_sale_orders.id', $apply['sale_order_id'])->whereIn('pas_sale_orders.status', [4,6])
                ->leftJoin('pas_sale_invoices', 'pas_sale_orders.invoice_id', '=', 'pas_sale_invoices.id')
                ->with('goods')->select('pas_sale_orders.*', 'pas_sale_invoices.shipping_id')->first()->toArray();
            if(empty($sale)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }

            $in_goods = array_field_as_key($apply['in_goods'], 'sale_order_goods_id');//入库商品
            $return_goods = array_field_as_key($return['return_goods'], 'sale_order_goods_id');//退货商品
            $data = $this->saleOrderDetail($sale);

            if($apply['entrise_id']){
                $entry = Entry::findOrFail($apply['entrise_id']);
                $data['process'] = app()->make(ReturnOrderRepository::class)->fetchEntryProcess($entry);
                $entry_info = Proc::findUserProcByEntryId($user->id, $apply['entrise_id']);
                $data['is_handle'] = (!empty($entry_info) && $entry_info->status == Proc::STATUS_IN_HAND) ? 1 : 0;
                $data['handle_id'] = (!empty($entry_info) && $entry_info->status == Proc::STATUS_IN_HAND) ? $entry_info->id : 0;
            }

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加修改退货入库单
     * */
    public function returnInWarehouse($param, $user){
        try{
            $error = $this->checkInWarehostData($param);
            throw_if(empty($error['status']), new Exception($error['msg']));

            $sale = SaleReturnOrder::where([['pas_sale_return_orders.id', $param['order_id']],['pas_sale_return_orders.status', 4]])
                ->leftJoin('pas_sale_orders', 'pas_sale_return_orders.sale_order_id', '=', 'pas_sale_orders.id')
                ->leftJoin('pas_sale_invoices', 'pas_sale_orders.invoice_id', '=', 'pas_sale_invoices.id')
                ->with('return_goods')->select('pas_sale_return_orders.id', 'pas_sale_return_orders.order_sn', 'pas_sale_return_orders.sale_order_id', 'pas_sale_return_orders.user_id', 'pas_sale_orders.order_sn as sale_order_sn', 'pas_sale_invoices.shipping_id')->first()->toArray();
            throw_if(empty($sale), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));
            $order_goods = array_field_as_key($sale['return_goods'], 'id');//退货单商品

            $info = [];
            if(!empty($param['id'])){
                $info = SaleReturnInWarehouse::where('id', $param['id'])->with('in_goods')->first()->toArray();
                throw_if(empty($info), new Exception(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE));
                $in_goods = array_field_as_key($info['in_goods'], 'return_order_goods_id');
            }

            $goods = [];
            $in_num = 0;
            foreach($error['item'] as $v){
                throw_if(empty($order_goods[$v['id']]), new Exception('请选择入库商品'));
                $da = $order_goods[$v['id']];
                if(!empty($in_goods)){
                    $numable = $da['return_num'] - $da['in_num'] - $da['apply_in_num'] + $in_goods[$v['id']]['in_num'];
                }else{
                    $numable = $da['return_num'] - $da['in_num'] - $da['apply_in_num'];
                }
                throw_if($v['num'] > $numable, new Exception('请填写合理的入库数量'));

                $da['in_num'] = $v['num'];
                $goods[] = $da;
                $in_num += $v['num'];
            }
            //print_r($sale);print_r($goods);print_r($info);die;

            $id = 0;
            DB::transaction(function () use ($param, $goods, $sale, $info, $in_num, $user, &$id) {
                $num = array_sum(array_column($goods, 'num'));
                $data = [
                    'num' => $num,
                    'in_num' => $in_num,
                    'in_time' => $param['in_time'],
                    'remark' => $param['remark'],
                    'status' => $param['status']
                ];

                if(empty($param['id'])){
                    $data['user_id'] = $user->id;
                    $data['in_sn'] = $this->getCodes('IW');
                    $data['sale_order_id'] = $sale['sale_order_id'];
                    $data['return_order_id'] = $sale['id'];

                    if($data['status'] == 1){
                        //提交审批流程，添加数据
                        $dataOne['title'] = '销售退货入库申请单号为'.trim($data['in_sn']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_sale_return_in_warehouse');//添加销售退货单 审核流程
                        $data['entrise_id'] = $entry->id;
                    }
                    $id = $this->addInWarehouse($data, $goods);
                }else{
                    $data['id'] = $param['id'];
                    $data['updated_at'] = date('Y-m-d H:i:s', time());

                    if (!$info['entrise_id'] && $data['status'] == 1) {
                        //提交审批流程，添加数据
                        $dataOne['title'] = '销售退货入库申请单号为' . trim($info['in_sn']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_sale_return_in_warehouse');//添加销售退货单 审核流程
                        $data['entrise_id'] = $entry->id;
                    }
                    $id = $this->editInWarehouse($data, $goods, $info);
                }
            });

            if($id){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加退货申请入库单
     * */
    private function addInWarehouse($data, $goods){//print_r($data);print_r($goods);die;
        $apply = SaleReturnInWarehouse::create($data);

        $id = 0;
        if($apply){
            foreach($goods as $v){
                $goods_data[] = [
                    'in_id' => $apply->id,
                    'return_order_goods_id' => $v['id'],//退货单商品表主键id
                    'goods_id' => $v['goods_id'],
                    'sku_id' => $v['sku_id'],
                    'in_num' => $v['in_num'],
                    'status' => $data['status'],
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                ];
                if($data['status']){
                    $up_sale_goods[] = [
                        'id' => $v['id'],
                        'apply_in_num' => '`apply_in_num`+' . $v['in_num']
                    ];
                }
            }
            if(!empty($up_sale_goods)){
                $res = $this->updateBatch('pas_sale_return_order_goods', $up_sale_goods, 0);
            }
            if(!empty($goods_data)){
                $res = SaleReturnInWarehouseGoods::insert($goods_data);
                if($res){
                    $id = $apply->id;
                }
            }
        }
        return $id;
    }


    /*
     * 修改退货申请入库单
     * */
    private function editInWarehouse($data, $goods, $info){//print_r($data); print_r($goods);print_r($info);die;
        $id = 0;

        $res = SaleReturnInWarehouse::where('id', $data['id'])->update($data);
        if($res){
            $goods = array_field_as_key($goods, 'id');
            $select_goods = array_keys($goods);//选中的商品
            //$has_goods = SaleReturnInWarehouseGoods::where('in_id', $data['id'])->pluck('sale_order_goods_id', 'id')->toArray();
            $return_goods = SaleReturnInWarehouseGoods::where('in_id', $data['id'])->get(['in_num', 'return_order_goods_id', 'id'])->toArray();

            foreach($return_goods as $v){
                $has_goods[$v['id']] = $v['return_order_goods_id'];
                $has_in_goods[$v['id']] = $v['in_num'];
            }
            //print_r($goods);print_r($select_goods);print_r($has_goods);print_r($has_in_goods);die;

            $add_ids = array_diff($select_goods, $has_goods);
            $up_ids = array_intersect($has_goods, $select_goods);
            $del_ids = array_diff($has_goods, $select_goods);
            //print_r($add_ids);print_r($up_ids);print_r($del_ids);print_r($goods);die;

            $add_data = $up_data = $del_data = $up_sale_goods = $num = [];
            if(!empty($add_ids)){
                foreach($add_ids as $v){
                    $g_info = $goods[$v];
                    $add_data[] = [
                        'in_id' => $data['id'],
                        'return_order_goods_id' => $v,
                        'goods_id' => $g_info['goods_id'],
                        'sku_id' => $g_info['sku_id'],
                        'in_num' => $g_info['in_num'],
                        'status' => $data['status'],
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];

                    if($data['status']) {
                        $up_sale_goods[] = [
                            'id' => $v,
                            'apply_in_num' => '`apply_in_num`+' . $g_info['in_num']
                        ];
                        $num[] = $g_info['in_num'];
                    }
                }
                $res = SaleReturnInWarehouseGoods::insert($add_data);
            }

            if(!empty($up_ids)){
                foreach($up_ids as $k=>$v){
                    $g_info = $goods[$v];
                    $up_data[] = [
                        'id' => $k,
                        'in_num' => $g_info['in_num'],
                        'status' => $data['status'],
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];
                    if($data['status']){
                        if($info['status'] == 1){
                            //上次已提交
                            $up_sale_goods[] = [
                                'id' => $v,
                                'apply_in_num' => '`apply_in_num`+' . ($g_info['in_num']-$has_in_goods[$k]),
                            ];
                            $num[] = $g_info['in_num']-$has_in_goods[$k];
                        }else if($info['status'] == 0){
                            //上次为草稿
                            $up_sale_goods[] = [
                                'id' => $v,
                                'apply_in_num' => '`apply_in_num`+' . $g_info['in_num'],
                            ];
                            $num[] = $g_info['in_num'];
                        }
                    }
                }
                $res = $this->updateBatch('pas_sale_return_in_warehouse_goods', $up_data);
            }

            if(!empty($del_ids)){
                if($info['status'] == 1){
                    //上次已提交
                    foreach($del_ids as $k=>$v){
                        $up_sale_goods[] = [
                            'id' => $v,
                            'apply_in_num' => '`apply_in_num`-' . $has_in_goods[$k],
                        ];
                        $num[] = $has_in_goods[$k];
                    }
                }
                $del_ids = array_filter(array_unique(array_keys($del_ids)));
                $res = SaleReturnInWarehouseGoods::whereIn('id', $del_ids)->where('in_id', $data['id'])->delete();
            }

            $num = array_filter($num);
            if(!empty($up_sale_goods) && count($num)) {
                $res = $this->updateBatch('pas_sale_return_order_goods', $up_sale_goods, 0);
            }

            if($res){
                $id = $data['id'];
            }
        }
        return $id;
    }


    /*
     * 撤销退货入库单
     * */
    public function cancelSaleInWarehouse($param, $user){
        try{
            throw_if(empty($param['id']), new Exception('请选择退货入库单'));
            $info = SaleReturnInWarehouse::where([['id', $param['id']], ['status', 1], ['user_id', $user->id]])->with('in_goods')->select('entrise_id','id', 'status')->first()->toArray();
            throw_if(empty($info), new Exception(ConstFile::API_DELETE_RECORDS_NOT_EXIST_MESSAGE));

            $res = 0;
            DB::transaction(function() use($user, $param, $info, &$res) {
                if($info['entrise_id']){
                    $res = app()->make(ReturnOrderRepository::class)->cancel($info['entrise_id'], '', $user);//审批流撤销

                    if($res){
                        $data['status']=2;
                        $data['updated_at']=date('Y-m-d H:i:s',time());
                        $res = SaleReturnInWarehouse::where('id', $info['id'])->update($data);
                        $res = SaleReturnInWarehouseGoods::where('in_id', $info['id'])->update(['status'=>0]);

                        $up_sale_goods = [];
                        foreach($info['in_goods'] as $v){
                            $up_sale_goods[] = [
                                'id' => $v['return_order_goods_id'],
                                'apply_in_num' => '`apply_in_num`-'.$v['in_num']
                            ];
                        }
                        if(!empty($up_sale_goods)){
                            $res = $this->updateBatch('pas_sale_return_order_goods', $up_sale_goods, 0);
                        }
                    }
                }
            });

            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 入库申请列表
     * */
    public function saleReturnInList($param, $user){
        try{
            $list = SaleReturnInWarehouse::where('user_id', $user->id);
            if($param['status']== 1){
                //待入库
                $list->whereIn('status', [1,4])->where('in_num', 0);
            }else if($param['status'] == 2){
                //已入库
                $list->where('status', 4)->whereRaw('in_num = num');
            }else if($param['status'] == 3){
                //部分入库
                $list->where('status', 4)->whereRaw('in_num < num')->where('in_num', '>', 0);
            }else if($param['status'] == 4){
                //草稿
                $list->where('status', 0);
            }else{
                $list->whereIn('status', [2,3]);
            }
            $list = $list->with('sale_order')->with(['in_goods'=>function($query){
                $query->with('goods');
            }])->paginate($param['limit'])->toArray();//print_r($list);die;

            $data = [];
            if(!empty($list['data'])){
                $sale_order = array_column($list['data'], 'sale_order');//print_r($sale_order);die;
                $buy_user_ids = array_unique(array_column($sale_order, 'buy_user_id'));//print_r($buy_user_ids);die;

                $client = new Client(config('app.customer_url'), false);
                $customer = $client->getCustomerByIds($buy_user_ids);
                $customer = array_field_as_key($customer, 'id');

                foreach($list['data'] as $v){
                    $goods = !empty($v['in_goods'][0]) ? $v['in_goods'][0] : [];
                    $data[] = [
                        'id' => $v['id'],
                        'buy_user_name' => !empty($customer[$v['sale_order']['buy_user_id']]) ? $customer[$v['sale_order']['buy_user_id']]['cusname'] : '',
                        'goods_name' => $goods ? $goods['goods']['goods_name'] : '',
                        'sale_order_sn' => $v['sale_order']['order_sn'],
                        'created_at' => $v['created_at'],
                        'num' => $v['num'],
                        'in_num' => $v['in_num'],
                        'lave_num' => $v['num'] - $v['in_num']
                    ];
                }
            }

            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $data;
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 处理选择购买的商品数据
     * */
    private function handleBuyGoods($goods){
        $data = [];
        if(!empty($goods)){
            $goods_ids = $sku = $sku_num = [];
            foreach($goods as $v){
                $goods_ids[] = $v['goods_id'];
                $sku_num[$v['goods_id']] = array_field_as_key($v['item'], 'sku_id', ['sku_id']) ;

                foreach($v['item'] as $vv){
                    $sku[] = $vv['sku_id'];
                }
            }
            $sku = array_unique($sku);

            $data = Goods::whereIn('goods_id', $goods_ids)->whereNull('deleted_at')->where('status', 1)->get(['goods_id','goods_name','thumb_img', 'goods_sn','cost_price', 'price', 'wholesale_price'])->toArray();
            $sku_info = GoodsSpecificPrice::whereIn('id', $sku)->whereNull('deleted_at')->select('id', 'goods_id', 'key', 'key_name', 'cost_price', 'price', 'wholesale_price', 'store_count','sku', 'sku_name')->get()->toArray();

            foreach ($data as &$v){
                $num = $money = 0;
                foreach ($sku_info as $vv){
                    $vv['sku_name'] = explode('+', $vv['sku_name']);
                    $vv['num'] = $sku_num[$v['goods_id']][$vv['id']]['num'];
                    $v['child'][] = $vv;
                    $num += $vv['num'];
                    $money += $vv['num'] * $vv['price'];
                }
                $v['sku_num'] = count($sku_num[$v['goods_id']]);
                $v['goods_num'] = $num;
                $v['money'] = $money;
            }
        }
        return $data;
    }


    /*
     * 组合规格结果
     * */
    //[{
    //	"spec_id": "1",
    //	"spec_name": "颜色",
    //	"item_id": "13",
    //	"item_name": "蓝色"
    //},{
    //	"spec_id": "2",
    //	"spec_name": "尺寸",
    //	"item_id": "3",
    //	"item_name": "S"
    //}]
    public function getSpecificCombine($skuInfo, $type = 1){
        $sku = $sku_name = $key = $key_name = [];
        $skuInfo = array_field_as_key($skuInfo, 'spec_id');//print_r($skuInfo);die;

        $key = array_keys($skuInfo);
        sort($key);//print_r($key);die;

        foreach($key as $v){
            $sku[] = $skuInfo[$v]['item_id'];
            if($type){
                $sku_name[] = $skuInfo[$v]['item_name'];
                $key_name[] = $skuInfo[$v]['spec_name'];
            }
        }

        $data['key'] = implode('_', $key);
        $data['sku'] = implode('_', $sku);
        if($type){
            $data['key_name'] = implode('+', $key_name);
            $data['sku_name'] = implode('+', $sku_name);
        }

        return $data;
    }


    /*
     * 检测销售发货单提交数据
     * */
    private function checkInvoiceData($param){
        $msg = '';

        if(empty($param['need'])){
            $msg = '请填写发货信息';
        }
        foreach($param['need'] as $k => $v){
            if(in_array($k, array_keys($this->invoice_field)) && empty($v)){
                $msg = $this->invoice_field[$k];
            }
        }
        return $msg;
    }


    /*
     * 检测销售单提交数据
     * */
    private function checkSaleOrderData($param){
        $msg = '';
        $goods = $cost = [];

        if(empty($param['need'])){
            $msg = '请填写发货信息';
        }
        if(empty($param['goods'])){
            $msg = '请添加商品';
        }
        if(!in_array($param['status'], [0,1])){
            $msg = '请选择提交按钮';
        }

        $goods = htmlspecialchars_decode($param['goods']);
        $goods = json_decode($goods,true);
        if(empty($goods)){
            $msg = '请添加商品';
        }else{
            foreach($goods as $v){
                if(empty($v['goods_id'])){
                    $msg = '请选择商品';
                }
                if(empty($v['item'])){
                    $msg = '请选择商品规格';
                }

                foreach ($v['item'] as $vv){
                    if($vv['sku_id'] <= 0 ){
                        $msg = '请选择规格';
                    }
                    if($vv['num'] <= 0 ){
                        $msg = '请填写合理的购买数量';
                    }
                    if($vv['discount'] < 0 ||  $vv['discount'] > 100){
                        $msg = '请填写合理的折扣值';
                    }
                }
            }
        }
        foreach($param['need'] as $k => $v){
            if(in_array($k, array_keys($this->order_field)) && empty($v)){
                $msg = $this->order_field[$k];
            }
        }

        if(!empty($param['cost'])){
            $cost = htmlspecialchars_decode($param['cost']);
            $cost = json_decode($cost,true);
        }
        if($msg){
            return ['status'=>0, 'msg'=>$msg];
        }else{
            return ['status'=>1, 'goods'=>$goods, 'cost'=>$cost];
        }
    }


    /*
     * 检测修改销售单商品数据
     * */
    private function checkAddSaleGoodsData($param){
        $msg = '';

        if(empty($param['goods'])){
            $msg = '请选择商品';
        }else{
            $data = htmlspecialchars_decode($param['goods']);
            $data = json_decode($data,true);
            if(empty($data)){
                $msg = '请选择商品';
            }else{
                foreach($data as $v){
                    if(empty($v['goods_id'])){
                        $msg = '请选择商品';
                    }
                    if(empty($v['item'])){
                        $msg = '请选择商品规格';
                    }

                    foreach ($v['item'] as $vv){
                        if($vv['sku_id'] <= 0 ){
                            $msg = '请选择规格';
                        }
                        if($vv['num'] <= 0 ){
                            $msg = '请填写合理的购买数量';
                        }
                    }
                }
            }
        }
        return $msg;
    }


    /*
     * 检测销售出库申请数据
     * */
    private function checkOutWarehostData($param){
        $msg = '';

        if(empty($param['order_id'])){
            $msg = '请选择销售单';
        }
        if(empty($param['out_time'])){
            $msg = '请填写出库时间';
        }
        if(empty($param['goods'])){
            $msg = '请填写出库商品数量';
        }

        $data = htmlspecialchars_decode($param['goods']);
        $data = json_decode($data,true);
        if(empty($data)) {
            $msg = '请选择出库商品';
        }

        if($msg){
            return ['status'=>0, 'msg'=>$msg];
        }else{
            return ['status'=>1, 'item'=>$data, 'msg'=>$msg];
        }
    }


    /*
     * 检测退货单申请数据
     * */
    private function checkReturnOrderData($param){
        $msg = '';

        if(empty($param['need']['order_sn'])){
            $msg = '请填写退货单号';
        }
        if(empty($param['need']['sale_order_id'])){
            $msg = '请选择销售单';
        }
        if(empty($param['need']['create_user_id'])){
            $msg = '请选择制单人';
        }
        if(empty($param['need']['real_refund_money'])){
            $msg = '请填写实际退款金额';
        }
        if(empty($param['need']['return_time'])){
            $msg = '请填写退货时间';
        }
        if(empty($param['need']['return_money_time'])){
            $msg = '请填写退款时间';
        }
        if(empty($param['need']['business_time'])){
            $msg = '请填写业务时间';
        }
        if(empty($param['goods'])){
            $msg = '请选择退货商品';
        }

        $goods = htmlspecialchars_decode($param['goods']);
        $goods = json_decode($goods,true);
        if(empty($goods)) {
            $msg = '请选择退货商品';
        }

        $cost = [];
        if(!empty($param['cost'])){
            $cost = htmlspecialchars_decode($param['cost']);
            $cost = json_decode($cost,true);
        }

        if($msg){
            return ['status'=>0, 'msg'=>$msg];
        }else{
            return ['status'=>1, 'goods'=>$goods, 'cost'=>$cost, 'msg'=>$msg];
        }
    }


    /*
     * 检测退货单入库申请数据
     * */
    private function checkInWarehostData($param){
        $msg = '';

        if(empty($param['order_id'])){
            $msg = '请选择退货单';
        }
        if(empty($param['in_time'])){
            $msg = '请填写入库时间';
        }
        if(empty($param['goods'])){
            $msg = '请填写入库商品';
        }

        $data = htmlspecialchars_decode($param['goods']);
        $data = json_decode($data,true);
        if(empty($data)) {
            $msg = '请选择入库商品';
        }

        if($msg){
            return ['status'=>0, 'msg'=>$msg];
        }else{
            return ['status'=>1, 'item'=>$data, 'msg'=>$msg];
        }
    }


    /*
     * 批量更新
     * $type 1字符串 0数字
     * */
    public function updateBatch($tableName = "", $multipleData = array(), $type = 1){
        if( $tableName && !empty($multipleData) ) {
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
           // var_dump($updateColumn);

            unset($updateColumn[0]);
            //var_dump($multipleData);die;
            $whereIn = "";

            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";

                foreach( $multipleData as $data ) {
                    if($type){
                        $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                    }else{
                        $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN ".$data[$uColumn]." ";
                    }
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
            //var_dump($q);die;
            return DB::update(DB::raw($q));
        } else {
            return false;
        }
    }

}

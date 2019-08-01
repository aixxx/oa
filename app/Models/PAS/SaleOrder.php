<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;
use App\Models\PAS\SaleOutWarehouse;
use App\Models\PAS\SaleReturnInWarehouse;

/**
 * App\Models\PAS\SaleOrder
 *
 * @property int $id 自动编号
 * @property string|null $order_sn 销售单号
 * @property int|null $user_id 申请用户编号
 * @property int|null $buy_user_id 销售的客户
 * @property int|null $goods_num 销售的商品总数
 * @property float|null $goods_money 商品总金额
 * @property float|null $total_money 此单金额
 * @property float|null $receivable_money 应收金额
 * @property float|null $actual_money 实收金额
 * @property float|null $zero_money 抹零金额
 * @property float|null $other_money 其它费用金额
 * @property float|null $discount 订单折扣
 * @property string|null $expected_pay_time 预计付款时间
 * @property string|null $bank_name 开户银行
 * @property string|null $subbranch 开户支行
 * @property string|null $bank_account 银行账户
 * @property string|null $account_holder 开户人
 * @property string|null $business_time 业务日期
 * @property int|null $account_period 账期 (单位天)
 * @property int|null $sale_user_id 销售员
 * @property int|null $invoice_id 发货方式
 * @property string|null $remark 备注
 * @property string|null $annex 附件
 * @property int|null $status 状态 0草稿 1审核中 2已撤回 3已退回 4审核完成(待出库) 5部分出库 6出库完成
 * @property int|null $entrise_id 审核流程id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PAS\SaleOrderGoods[] $goods
 * @property-read \App\Models\PAS\SaleInvoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereAccountHolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereAccountPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereActualMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereAnnex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereBusinessTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereBuyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereEntriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereExpectedPayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereGoodsMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereGoodsNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereOtherMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereReceivableMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereSaleUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereSubbranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereTotalMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOrder whereZeroMoney($value)
 * @mixin \Eloquent
 */
class SaleOrder extends Model
{
    protected $table = 'pas_sale_orders';
    protected $guarded = [];

    /*
     * 关联销售单商品
     * */
    public function goods(){
        return $this->hasMany('App\Models\PAS\SaleOrderGoods', 'order_id', 'id')
            ->leftJoin('pas_goods', 'pas_sale_order_goods.goods_id', '=', 'pas_goods.goods_id')
            ->leftJoin('pas_goods_specific_prices', 'pas_goods_specific_prices.id', '=', 'pas_sale_order_goods.sku_id')
            ->select('pas_sale_order_goods.*', 'pas_goods.goods_sn', 'pas_goods.goods_name', 'thumb_img', 'pas_goods_specific_prices.sku_name');
    }


    /*
     * 关联物流
     * */
    public function invoice(){
        return $this->hasOne('App\Models\PAS\Warehouse\WarehouseDeliveryType', 'id', 'invoice_id')
            ->leftJoin('pas_logistics', 'pas_logistics.id', '=', 'pas_warehouse_delivery_type.logistics_id')
            ->select('pas_warehouse_delivery_type.*', 'pas_logistics.title');
    }


    /*
     * 处理出库数据
     * $out_id 出库申请单id
     * $arr 出库数量[['id'=>1, 'sale_order_goods_id'=>2, 'out_num'=>5],['id'=>2, 'sale_order_goods_id'=>3, 'out_num'=>3]]
     * pas_sale_out_warehouse_goods表 id，sale_order_goods_id，out_num字段
     * */
    public function handle_sale_out($out_id, $arr){
        if(empty($out_id) || empty($arr)){
            return false;
        }
        $out_order = SaleOutWarehouse::where('id', $out_id)->with('out_goods')->first()->toArray();//出库申请数据
        if(!empty($out_order)){
            $out_order = array_field_as_key($out_order['out_goods'], 'id');
            $up_out_data = $up_sale_goods = [];
            foreach($arr as $v){
                if(($out_order[$v['id']]['out_num'] - $out_order[$v['id']]['has_out_num'] - $v['out_num']) < 0){
                    return false;
                }else{
                    $up_out_data[] = [
                        'id' => $v['id'],
                        'has_out_num' => '`has_out_num`+'.$v['out_num']
                    ];
                    $up_sale_goods[] = [
                        'id' => $v['sale_order_goods_id'],
                        'out_num' => '`out_num`+'.$v['out_num'],
                        'apply_out_num' => '`apply_out_num`-'.$v['out_num']
                    ];
                }
            }
            $res = app()->make(\App\Repositories\PAS\GoodsRepository::class)->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
            if($res){
                return app()->make(\App\Repositories\PAS\GoodsRepository::class)->updateBatch('pas_sale_out_warehouse_goods', $up_out_data, 0);
            }else {
                return false;
            }
        }else{
            return false;
        }
    }


    /*
     * 处理入库数据
     * $in_id 入库申请单id
     * $arr 入库数量
     * pas_sale_return_in_warehouse_goods表 id，return_order_goods_id，in_num字段
     * */
    public function handle_sale_in($in_id, $arr){
        if(empty($in_id) || empty($arr)){
            return false;
        }
        $out_order = SaleReturnInWarehouse::where('id', $in_id)->with('in_goods')->first()->toArray();//出库申请数据
        if(!empty($out_order)){
            $out_order = array_field_as_key($out_order['in_goods'], 'id');
            $up_out_data = $up_sale_goods = [];
            foreach($arr as $v){
                if(($out_order[$v['id']]['in_num'] - $out_order[$v['id']]['has_in_num'] - $v['in_num']) < 0){
                    return false;
                }else{
                    $up_out_data[] = [
                        'id' => $v['id'],
                        'has_in_num' => '`has_in_num`+'.$v['in_num']
                    ];
                    $up_sale_goods[] = [
                        'id' => $v['return_order_goods_id'],
                        'in_num' => '`in_num`+'.$v['in_num'],
                        'apply_in_num' => '`apply_in_num`-'.$v['in_num']
                    ];
                }
            }
            $res = app()->make(\App\Repositories\PAS\GoodsRepository::class)->updateBatch('pas_sale_return_order_goods', $up_sale_goods, 0);
            if($res){
                return app()->make(\App\Repositories\PAS\GoodsRepository::class)->updateBatch('pas_sale_return_in_warehouse_goods', $up_out_data, 0);
            }else {
                return false;
            }
        }else{
            return false;
        }
    }

}

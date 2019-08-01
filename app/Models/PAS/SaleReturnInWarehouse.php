<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\SaleReturnInWarehouse
 *
 * @property int $id
 * @property int|null $user_id 申请人
 * @property string|null $in_sn 出库单申请编号
 * @property int|null $sale_order_id 销售订单id
 * @property string|null $sale_order_sn 销售订单编号
 * @property int|null $return_order_id 退货单id
 * @property string|null $return_order_sn 退货单编号
 * @property int|null $num 销售商品总数量
 * @property int|null $in_num 入库数量
 * @property string|null $in_time 入库时间
 * @property int|null $shipping_id 物流id
 * @property int|null $status 申请单状态 0草稿 1申请
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PAS\SaleReturnInWarehouseGoods[] $in_goods
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereInNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereInSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereInTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereReturnOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereReturnOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereSaleOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereSaleOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereShippingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleReturnInWarehouse whereUserId($value)
 * @mixin \Eloquent
 */
class SaleReturnInWarehouse extends Model
{
    protected $table = 'pas_sale_return_in_warehouse';
    protected $guarded = [];


    /*
     * 关联入库单商品
     * */
    public function in_goods(){
        return $this->hasMany(SaleReturnInWarehouseGoods::class, 'in_id', 'id');
    }

    /*
     * 关联销售单
     * */
    public function sale_order(){
        return $this->hasOne(SaleOrder::class, 'id', 'sale_order_id');
    }
}

<?php

namespace App\Models\PAS;

use App\Models\PAS\Warehouse\Warehouse;
use App\Models\PAS\Warehouse\WarehouseDeliveryType;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\SaleOutWarehouse
 *
 * @property int $id
 * @property int|null $user_id 申请人
 * @property string|null $out_sn 出库单申请编号
 * @property int|null $order_id 销售订单id
 * @property int|null $order_sn 销售订单编号
 * @property int|null $num 销售商品总数量
 * @property int|null $out_num 商品出库总数量
 * @property string|null $out_time 出库时间
 * @property int|null $shipping_id 物流id
 * @property int|null $status 申请单状态 0草稿 1申请
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PAS\SaleOutWarehouseGoods[] $out_goods
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereOutNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereOutSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereOutTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereShippingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouse whereUserId($value)
 * @mixin \Eloquent
 */
class SaleOutWarehouse extends Model
{
    const STATUS_DRAFT = 0;
    const STATUS_STAY_OUT = 1; //申请完成 待出库
    const STATUS_HALF_OUT = 2; //部分出库
    const STATUS_OK_OUT = 3; //已出库

    protected $table = 'pas_sale_out_warehouse';
    protected $guarded = [];


    /*
     * 关联出库单商品
     * */
    public function out_goods(){
        return $this->hasMany(SaleOutWarehouseGoods::class, 'out_id', 'id');
    }


    /*
     * 关联销售单
     * */
    public function sale_order(){
        return $this->hasOne(SaleOrder::class, 'id', 'order_id');
    }

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    /*
     * 发货方式
     */
    public function delivery(){
        return $this->hasOne(WarehouseDeliveryType::class, 'id', 'shipping_id');
    }

}

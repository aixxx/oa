<?php

namespace App\Models\PAS\Warehouse;

use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\PAS\Purchase\WarehousingApply;
use Illuminate\Database\Eloquent\Model;

/**
 * 业务变化  类名与实际有些微差异
 * 仓库安排表
 * App\Models\PAS\Warehouse\WarehouseInCard
 *
 * @property int $id
 * @property int|null $type 申请单 类型 1、采购入库  2、退货入库 3、调货入库
 * @property string|null $in_no 入库单号
 * @property int|null $apply_id 申请单ID
 * @property int|null $goods_allocation_id 仓库ID
 * @property int|null $create_user_id 制单人ID
 * @property int|null $create_user_name 制单人名
 * @property int|null $cargo_user_id 配货人ID
 * @property int|null $cargo_user_name 配货人名
 * @property string|null $delivery_type 送货方式
 * @property int|null $delivery_type_id 送货方式Id
 * @property string|null $delivery_name 送货人姓名
 * @property int|null $status 状态
 * @property int|null $percent 百分比
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereCargoUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereCargoUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereCreateUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereDeliveryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereDeliveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereDeliveryTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereGoodsAllocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereInNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInCard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WarehouseInCard extends Model
{
    const TYPE_BUY = 1;  //1、采购入库  2、退货入库 3、调货入库
    const TYPE_BACK = 2;
    const TYPE_ALLOT = 3;

    const STATUS_OK = 1;
    const STATUS_PART = 2;
    const STATUS_CANCEL = 3;
    //
    protected $table = 'pas_warehouse_in_card';

    protected $fillable = [
        'type',
        'in_no',
        'apply_id',
        'warehouse_id',
        'goods_allocation_id',
        'create_user_id',
        'create_user_name',
        'cargo_user_id',
        'cargo_user_name',
        'delivery_type',
        'delivery_type_id',
        'delivery_name',
        'status',
        'percent',
        'remark',
        'created_at',
        'updated_at',
    ];




    public function inCardGoods(){
       return $this->hasMany(WarehouseInGoods::class,  'in_id', 'id');
    }

    public function goodsFlow(){
        return $this->hasMany(GoodsFlow::class, 'plan_id', 'id');
    }
}

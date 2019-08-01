<?php

namespace App\Models\PAS\Warehouse;

use App\Models\PAS\Goods;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\AllotCart
 *
 * @property int $id
 * @property int|null $warehouse_from_id 调出仓库id
 * @property int|null $warehouse_allocation_from_id 调出货位id
 * @property int|null $warehouse_to_id 调入仓库id
 * @property int|null $warehouse_allocation_to_id 调入货位id
 * @property string|null $business_date 业务日期
 * @property string|null $remark 备注
 * @property int|null $number 合计
 * @property int|null $create_user_id 制单人
 * @property int|null $create_user_name 制单人姓名
 * @property int|null $cargo_user_id 配货人
 * @property int|null $cargo_user_name 配货人姓名
 * @property string|null $delivery_type 发货方式
 * @property int|null $status 状态  0草稿   1待出库   2待出库  3待入库  4完成
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereBusinessDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereCargoUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereCargoUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereCreateUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereDeliveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereWarehouseAllocationFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereWarehouseAllocationToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereWarehouseFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCart whereWarehouseToId($value)
 * @mixin \Eloquent
 */
class AllotCart extends Model
{
    //
    const STATUS_ZERO = 0;//0草稿
    const STATUS_ONE = 1;//1待出库
    const STATUS_TOW = 2;//2待出库
    const STATUS_THREE = 3;//3待入库
    const STATUS_FOUR = 4;//4完成

    const STATUS_DRAFT = 0; //草稿
    const STATUS_OUT_STAY = 2; //待出库
    const STATUS_IN_STAY = 3; //待入库
    const STATUS_OK = 4; //完成

    public static $_status = [
        self::STATUS_DRAFT => '草稿',
        self::STATUS_OUT_STAY => '待出库',
        self::STATUS_ONE => '待出库',
        self::STATUS_IN_STAY => '待入库',
        self::STATUS_OK => '完成',
    ];

    protected $table = 'pas_allot_cart';
    protected $fillable = [
        'code',
        'warehouse_from_id',
        'warehouse_allocation_from_id',
        'warehouse_to_id',
        'warehouse_allocation_to_id',
        'business_date',
        'remark',
        'number',
        'create_user_id',
        'create_user_name',
        'cargo_user_id',
        'cargo_user_name',
        'delivery_type',
        'status',
        'updated_at',
        'created_at',
    ];

    public function cardGoods(){
        return $this->hasMany(AllotCartGoods::class, 'allot_id', 'id');
    }
   public function warehouseOut()
   {
       return $this->hasOne(Warehouse::class, 'id', 'warehouse_from_id')->select(['id','title']);
   }
    public function warehouseEnter()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_to_id')->select(['id','title']);
    }
}

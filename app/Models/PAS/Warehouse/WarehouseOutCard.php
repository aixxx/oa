<?php

namespace App\Models\PAS\Warehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\WarehouseOutCard
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $out_no 出库单号
 * @property int|null $out_type 出库类型 1、调拨 2、采购退货 3、销售单
 * @property int|null $warehouse_id 仓库ID
 * @property int|null $allocation_id 货位ID
 * @property int|null $status 状态
 * @property int|null $create_user_id 制单人ID
 * @property string|null $create_user_name 制单人名
 * @property string|null $deliver_date 发货日期
 * @property string|null $business_date 业务日期
 * @property int|null $deliver_type 发货方式
 * @property int|null $number 出库数
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $apply_id 出库单申请单据ID
 */
class WarehouseOutCard extends Model
{
    const TYPE_SALE = 1;
    const TYPE_ALLOT = 2;
    const TYPE_BACK = 3;
    //
    protected $table = 'pas_warehouse_out_card';

    protected $fillable = [
        'out_no',
        'out_type',
        'warehouse_id',
        'allocation_id',
        'status',
        'create_user_id',
        'create_user_name',
        'deliver_date',
        'business_date',
        'deliver_type',
        'number',
        'remark',
        'updated_at',
        'created_at',
    ];

    public function outGoods(){
        return $this->hasMany(WarehouseOutGoods::class , 'out_id', 'id');
    }

    public function logistics(){
        return $this->hasOne(Logistics::class, 'id', 'deliver_type');
    }

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

}

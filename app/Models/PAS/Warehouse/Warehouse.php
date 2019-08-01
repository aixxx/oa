<?php

namespace App\Models\PAS\Warehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\Warehouse
 *
 * @property int $id 自动编号
 * @property int|null $user_id 创建仓库用户编号
 * @property string|null $title 仓库名称
 * @property string|null $alias 仓库别名
 * @property int|null $charge_id 负责人id
 * @property string|null $charge_name 负责人姓名
 * @property float|null $warehouse_area 仓库面积
 * @property string|null $address 仓库地址
 * @property int|null $stwarehouse 仓库货位
 * @property int|null $row_number 仓库排数
 * @property string|null $telephone 联系电话
 * @property int|null $status 状态 0已删除 1启用 2停用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereChargeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereChargeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereRowNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereStwarehouse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Warehouse whereWarehouseArea($value)
 * @mixin \Eloquent
 */
class Warehouse extends Model
{
    //
    protected $table = 'pas_warehouse';

    public $fillable = [
        'user_id',
        'title',
        'alias',
        'charge_id',
        'charge_name',
        'warehouse_area',
        'address',
        'stwarehouse',
        'row_number',
        'telephone',
        'status',
        'created_at',
        'updated_at',
    ];

    public function allowAllocation(){
        return $this->hasMany(GoodsAllocation::class, 'warehouse_id', 'id')
            ->where('status', '=', 0);
    }
    public function skus(){
        return $this->hasMany(GoodsAllocationGoods::class, 'warehouse_id', 'id');
    }
}

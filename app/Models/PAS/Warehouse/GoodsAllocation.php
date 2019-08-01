<?php

namespace App\Models\PAS\Warehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\GoodsAllocation
 *
 * @property int $id
 * @property int $row_num 排数
 * @property int|null $capacity 容量
 * @property int|null $status 状态
 * @property int|null $is_private 是否vip
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $no 货位号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereRowNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsAllocation extends Model
{
    //
    protected $table = 'pas_goods_allocation';

    protected $fillable = [
        'row_num',
        'no',
        'capacity',
        'status',
        'is_private',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function skus(){
        return $this->hasMany(GoodsAllocationGoods::class, 'allocation_id', 'id');
    }
}

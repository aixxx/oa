<?php

namespace App\Models\PAS\Warehouse;

use App\Models\PAS\Goods;
use App\Models\PAS\GoodsAttribute;
use App\Models\PAS\GoodsSpecificPrice;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\GoodsAllocationGoods
 *
 * @property int $id
 * @property int|null $allocation_id 货位id
 * @property int|null $goods_id 商品id
 * @property int|null $number 数量
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $sku_id skuID
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods whereAllocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsAllocationGoods whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoodsAllocationGoods extends Model
{
    //
    protected $table = 'goods_allocation_goods';
    protected $fillable = [
        'allocation_id',
        'warehouse_id',
        'goods_id',
        'number',
        'sku_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function sku(){
        return $this->hasOne(GoodsSpecificPrice::class, 'id', 'sku_id')
            ->select(['id', 'sku_name']);
    }

    public function goods_allocation()
    {
        return $this->hasMany(GoodsAllocation::class, 'id', 'allocation_id')
            ->select(['no', 'id']);
    }
    public function goods_allocation_no()
    {
        return $this->hasOne(GoodsAllocation::class, 'id', 'allocation_id')
            ->select('id','no');
    }
    public function warehouse(){
        return $this->hasMany(Warehouse::class, 'id', 'warehouse_id')
            ->select(['id', 'title']);
    }

    public function goods(){
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id')
            ->select(['goods_id', 'goods_name', 'goods_sn']);
    }
}

<?php

namespace App\Models\PAS\Warehouse;

use App\Models\PAS\Goods;
use App\Models\PAS\GoodsSpecificPrice;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\AllotCartGoods
 *
 * @property int $id
 * @property int|null $allot_id 调拨单id
 * @property int|null $goods_id 商品ID
 * @property int|null $sku_id sku_id
 * @property int|null $warehouse_id warehouse_id
 * @property int|null $number 调拨数量
 * @property int|null $status 状态
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereAllotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\AllotCartGoods whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AllotCartGoods extends Model
{
    //
    protected $table = 'pas_allot_card_goods';

    protected $fillable = [
        'allot_id',
        'warehouse_id',
        'goods_id',
        'sku_id',
        'number',
        'status',
        'deleted_at',
        'updated_at',
        'created_at',
    ];

    public function goodsInfo(){
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id');
    }

    public function sku(){
        return $this->hasOne(GoodsSpecificPrice::class, 'id', 'sku_id');
    }

    public function warehouse(){
        return $this->hasOne(Warehouse::class , 'id', 'warehouse_id');
    }

}

<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\GoodsSpecificPrice
 *
 * @property int $id
 * @property int|null $goods_id 商品id
 * @property string|null $key 规格键名
 * @property string|null $key_name 规格键名中文
 * @property float $cost_price 成本价格
 * @property float|null $price 零售价
 * @property float|null $wholesale_price 批发价
 * @property int|null $store_count 库存数量
 * @property int|null $freeze_store_count 冻结库存
 * @property string|null $bar_code 商品条形码
 * @property string|null $sku SKU
 * @property string|null $sku_name sku健名中文
 * @property int|null $store_upper_limit 库存上限
 * @property int|null $store_lower_limit 库存下限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\PAS\Goods $goods
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereBarCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereFreezeStoreCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereKeyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereStoreCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereStoreLowerLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereStoreUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\GoodsSpecificPrice whereWholesalePrice($value)
 * @mixin \Eloquent
 */
class GoodsSpecificPrice extends Model
{
    protected $table = 'pas_goods_specific_prices';
    protected $guarded = [];

    public function goods(){
        return $this->hasOne(Goods::class , 'goods_id', 'goods_id');
    }
}

<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\SaleOutWarehouseGoods
 *
 * @property int $id
 * @property int|null $out_id 销售订单id
 * @property int|null $sale_order_goods_id 销售单商品表主键id
 * @property int|null $goods_id 商品id
 * @property int|null $sku_id skuid
 * @property int|null $out_num 出库数量
 * @property int|null $status 状态（备用）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereOutId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereOutNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereSaleOrderGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $has_out_num 仓库实际已出库数量
 * @property-read \App\Models\PAS\Goods $goods
 * @property-read \App\Models\PAS\SaleOrderGoods $orderGoods
 * @property-read \App\Models\PAS\GoodsSpecificPrice $sku
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\SaleOutWarehouseGoods whereHasOutNum($value)
 */
class SaleOutWarehouseGoods extends Model
{
    protected $table = 'pas_sale_out_warehouse_goods';
    protected $guarded = [];

    public function orderGoods(){
        return $this->hasOne(SaleOrderGoods::class, 'id', 'sale_order_goods_id');
    }

    public function goods(){
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id');
    }

    public function sku(){
        return $this->hasOne(GoodsSpecificPrice::class, 'id', 'sku_id');
    }
}

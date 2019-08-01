<?php

namespace App\Models\PAS\Warehouse;

use App\Models\PAS\Goods;
use App\Models\PAS\GoodsSpecificPrice;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\StockCheckGoods
 *
 * @property int $id
 * @property int|null $check_id 盘点id
 * @property int|null $goods_id 商品ID
 * @property int|null $sku_id skuID
 * @property int|null $number 盘点数
 * @property int|null $profit_loss 盈亏
 * @property int|null $status 状态
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereCheckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereProfitLoss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheckGoods whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockCheckGoods extends Model
{
    //
    protected $table = 'pas_stock_check_goods';

    protected $fillable = [
        'check_id',
        'goods_id',
        'sku_id',
        'number',
        'profit_loss',
        'status',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function goods(){
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id');
    }

    public function sku(){
        return $this->hasOne(GoodsSpecificPrice::class, 'id', 'sku_id');
    }
}

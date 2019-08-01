<?php

namespace App\Models\PAS\Warehouse;

use App\Models\PAS\Goods;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\WarehouseOutGoods
 *
 * @property int $id
 * @property int|null $out_id 外出单id
 * @property int|null $goods_id 商品id
 * @property string|null $goods_no 商品编号
 * @property int|null $sku_id skuId
 * @property int|null $apply_num 申请数
 * @property int|null $outed_num 出库数
 * @property int|null $house_num 库存数
 * @property int|null $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereApplyNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereGoodsNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereHouseNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereOutId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereOutedNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseOutGoods whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WarehouseOutGoods extends Model
{
    //
    protected $table = 'pas_warehouse_out_goods';
    protected $fillable = [
        'out_id',
        'goods_id',
        'goods_no',
        'sku_id',
        'apply_num',
        'outed_num',
        'house_num',
        'status',
        'updated_at',
        'created_at',
    ];

    public function goods(){
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }
}

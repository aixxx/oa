<?php

namespace App\Models\PAS\Warehouse;

use App\Models\PAS\Goods;
use App\Models\PAS\GoodsSpecificPrice;
use App\Models\PAS\Purchase\PurchaseCommodityContent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\WarehouseInGoods
 *
 * @property int $id
 * @property int|null $warehouse_id 仓库ID
 * @property int|null $in_id 入库单id
 * @property int|null $goods_id 商品id
 * @property string|null $goods_no 商品编号
 * @property int|null $sku_id skuId
 * @property int|null $in_num 申请数
 * @property int|null $stored_num 入库数
 * @property int|null $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereGoodsNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereInId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereInNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereStoredNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $type 数据源类型
 * @property int|null $apply_id 申请单ID
 * @property int|null $apply_goods_id 申请单商品主键ID
 * @property-read \App\Models\PAS\Goods $goodsInfo
 * @property-read \App\Models\PAS\Purchase\PurchaseCommodityContent $inCardSkuInfo
 * @property-read \App\Models\PAS\GoodsSpecificPrice $skuInfo
 * @property-read \App\Models\PAS\Warehouse\Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereApplyGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseInGoods whereWarehouseId($value)
 */
class WarehouseInGoods extends Model
{
    //
    protected $table = 'pas_warehouse_in_goods';
    protected $fillable = [
        'in_id',
        'type',
        'apply_id',
        'apply_goods_id',//便于申请单查询
        'warehouse_id',
        'goods_id',
        'goods_no',
        'sku_id',
        'in_num',
        'stored_num',
        'status',
        'updated_at',
        'created_at',
    ];

    public function goodsInfo(){
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id');
    }

    public function skuInfo(){
        return $this->hasOne(GoodsSpecificPrice::class, 'id', 'sku_id');
    }

//    public function inCardSkuInfo(){
//        return $this->hasOne(PurchaseCommodityContent::class, 'id', 'sku_id');
//    }
    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

}

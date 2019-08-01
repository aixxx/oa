<?php

namespace App\Models\PAS\Warehouse;

use App\Models\PAS\Goods;
use App\Models\PAS\GoodsSpecificPrice;
use App\Models\PAS\Purchase\WarehousingApply;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\GoodsFlow
 *
 * @property int $id
 * @property int|null $sku_name 商品货号
 * @property int|null $sku_id 商品货号
 * @property int|null $card_no 编号
 * @property int|null $warehouse_id 仓库
 * @property int|null $type 状态
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereCardNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\GoodsFlow whereWarehouseId($value)
 * @mixin \Eloquent
 */
class GoodsFlow extends Model
{
    const TYPE_SALE_OUT = 3;
    const TYPE_PURCHASE_IN = 1;
    public static $_types = [
        self::TYPE_SALE_OUT => '销售出库',
        self::TYPE_PURCHASE_IN => '采购入库',
    ];
    //
    protected $table = 'pas_goods_flow';
    protected $fillable = [
        'sku_name',
        'sku_id',
        'card_no',
        'warehouse_id',
        'type',
        'goods_id',
        'plan_id',
        'allocation_id',
        'apply_id',
        'updated_at',
        'created_at',
    ];

    public function sku(){
       return $this->hasOne(GoodsSpecificPrice::class, 'id', 'sku_id');
    }

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function goods(){
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id');
    }

    public function plan(){
        return $this->hasOne(WarehouseInCard::class, 'id', 'plan_id');
    }

    public function allocation(){
        return $this->hasOne(GoodsAllocation::class, 'id', 'allocation_id');
    }

}

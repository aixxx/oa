<?php

namespace App\Models\PAS\Purchase;

use App\Models\PAS\GoodsSpecificPrice;
use App\Models\PAS\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Purchase\WarehousingApplyGoods
 *
 * @property int $id
 * @property string|null $code 入库申请表（退货申请）
 * @property int|null $p_id 入库申请表（退货申请）id
 * @property int|null $pcc_id 采购商品(sku)表数据id
 * @property int|null $number 申请数量
 * @property int|null $r_number 库成功数量（退货成功数量）
 * @property float|null $money 总金额
 * @property int|null $status 状态
 * @property int|null $type 1申请入库  2申请退货
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PAS\Purchase\WarehousingApply $apply
 * @property-read \App\Models\PAS\Purchase\PurchaseCommodityContent $sku
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods wherePId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods wherePccId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereRNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApplyGoods whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WarehousingApplyGoods extends Model
{
    const TYPE_IN = 1;
    const TYPE_BACK = 2;
    //
    protected $table = 'pas_warehousing_apply_content';
    protected $fillable = [
        'code',
        'p_id',
        'pcc_id',
        'number',
        'r_number',
        'money',
        'status',
        'type',
        'warehouse_id',
    ];

    public function apply(){
        return $this->hasOne(WarehousingApply::class, 'id', 'p_id');
    }

    public function sku(){
        return $this->hasOne(PurchaseCommodityContent::class, 'id', 'pcc_id');
    }

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}

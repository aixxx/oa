<?php

namespace App\Models\PAS\Purchase;
   
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Purchase\WarehousingApply
 *
 * @property int $id
 * @property string|null $code 入库单号
 * @property string|null $p_code 采购单号
 * @property string|null $business_date 业务日期
 * @property int|null $supplier_id 供应商id
 * @property int|null $apply_id 经手人id
 * @property string|null $apply_name 经手人名称
 * @property float|null $payable_money 此前应付钱
 * @property string|null $remarks 备注
 * @property int|null $status 状态  0草稿 1待入库未安排 2待入库 - 仓库已安排 3部分入库 4全部入库
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id 数据编号id
 * @property string|null $supplier_name 供应商名称
 * @property string|null $goods_name 商品名称
 * @property float|null $money 总金额
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PAS\Purchase\WarehousingApplyGoods[] $goods
 * @property-read \App\Models\PAS\Purchase\Supplier $supplier
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereApplyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereBusinessDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply wherePCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply wherePayableMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\WarehousingApply whereUserId($value)
 * @mixin \Eloquent
 */
class WarehousingApply extends Model {
    const STATUS_DRAFT = 0; //0草稿 1待入库未安排 2待入库 - 仓库已安排 3部分入库 4全部入库
    const STATUS_HOUSE_NO = 1;
    const STATUS_HOUSE_YES = 2;
    const STATUS_PART = 3;
    const STATUS_OK = 4;
    public static $_status = [
        self::STATUS_DRAFT => '草稿',
        self::STATUS_HOUSE_NO => '待入库 - 未安排',
        self::STATUS_HOUSE_YES => '待入库 - 仓库已安排',
        self::STATUS_PART => '部分入库',
        self::STATUS_OK => '全部入库',
    ];

    protected $table = 'pas_warehousing_apply';

    protected $fillable = ['id',"code","p_code","user_id","status","supplier_id","supplier_name",'apply_id','apply_name','payable_money','remarks'];

    public function applyGoods(){
        return $this->hasMany(WarehousingApplyGoods::class, 'p_id', 'id')
            ->where('type', '=', WarehousingApplyGoods::TYPE_IN);
    }

    public function supplier(){
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

//    //商品信息
//    public function skuList()
//    {
//        return $this->hasMany(PurchaseCommodityContent::class, 'p_code', 'p_code')
//            ->select(['p_code','goods_name','goods_url', 'goods_id','id','sku','number','price','money','war_number','r_number','rw_number','wa_number','awa_numbe','sku_id']);
//    }

}
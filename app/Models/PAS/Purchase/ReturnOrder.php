<?php

namespace App\Models\PAS\Purchase;
   
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Purchase\ReturnOrder
 *
 * @property int $id
 * @property string|null $code 退货单号
 * @property string|null $p_code 采购单号
 * @property string|null $business_date 业务日期
 * @property int|null $supplier_id 供应商id
 * @property int|null $apply_id 经手人id
 * @property string|null $apply_name 经手人名称
 * @property float|null $payable_money 此前应付钱
 * @property int|null $type 0未入库  1表示已入库
 * @property string|null $remarks 备注
 * @property int|null $number 退货总数
 * @property float|null $money 退货总金额
 * @property int|null $entrise_id 会议工作流编号
 * @property int|null $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id 添加退货单的用户id
 * @property string|null $supplier_name 供应商名称
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PAS\Purchase\CostInformation[] $costList
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereApplyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereApplyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereBusinessDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereEntriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder wherePCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder wherePayableMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Purchase\ReturnOrder whereUserId($value)
 * @mixin \Eloquent
 */
class ReturnOrder extends Model {
    const STATUS_DEFAULT_DRAFT = 0;	//0草稿
    const STATUS_STAY_PENDING = 1;//1待审核
    const STATUS_PART_WITHDRAWAL = 2;//2已撤回
    const STATUS_RETURN = 3;//3已退回
    const STATUS_PASS_OK = 4;//4已完成
    const STATUS_DEFAULT = 5;
    const STATUS_STAY = 6;
    const STATUS_PART =7;
    const STATUS_OK = 8;
    public static $_status = [
        self::STATUS_DEFAULT => '出库草稿',
        self::STATUS_STAY => '待出库',
        self::STATUS_PART => '部分出库',
        self::STATUS_OK => '已出库',
    ];

    protected $table = 'pas_return_order';


    protected $fillable = ['id',"user_id","code","business_date","supplier_id","payable_money","apply_name",'apply_id','earnest_money','status'];
    //费用信息数据查询
    public function costList()
    {
        return $this->hasMany('App\Models\PAS\Purchase\CostInformation', 'code_id', 'id')
            ->where('status',1)
            ->where('type',2)
            ->select(['id','code_id','title','money','nature','payment']);

    }

    public function supplier(){
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function applyGoods(){
        return $this->hasMany(WarehousingApplyGoods::class, 'p_id', 'id')
            ->where('status',1)
            ->where('type',2);
    }
}
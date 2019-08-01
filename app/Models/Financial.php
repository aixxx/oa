<?php

namespace App\Models;

use App\Models\PAS\SaleOrder;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

/**
 * Class Financial.
 *
 * @package namespace App\Models;
 */
class Financial extends Model
{
	use SoftDeletes;

	protected $table = 'financial';


    /**
     * 财务相关状态
     */
    const FINANCIALS_STARUS_CANCEL = -2;//撤销
    const FINANCIALS_STARUS_REJUEST = -1;//拒绝
    const FINANCIALS_STARUS_SUBMIT = 1;//待审批
    const FINANCIALS_STARUS_CHECKING = 2;//审批中
    const FINANCIALS_STARUS_CHECK_FINISH = 3;//审批完成
    const FINANCIALS_STARUS_PEDING = 4;//待入账
    const FINANCIALS_STARUS_PENDING_BUDGET = 5;//待收支
    const FINANCIALS_STARUS_RECEIVED_BUDGET = 6;//已收支
    const FINANCIALS_STARUS_WAITING_INVOICE = 7;//待发票
    const FINANCIALS_STARUS_FINISH = 8;//已完成
    /**
     * 客户类型
     */
    const FINANCIALS_CUSTOM_TYPE = 1;//客户
    const FINANCIALS_PROJECT_TYPE = 2;//项目
    const FINANCIALS_OEDER_TYPE = 3;//订单

    const FINANCIALS_FEE_BOOTH_YES = 1;//平摊
    const FINANCIALS_FEE_BOOTH_NO = 2;//不平摊

    const FINANCIALS_LOAN_BILL_YES = 1;//关联借款单
    const FINANCIALS_LOAN_BILL_NO = 2;//不关联借款单

    const FINANCIALS_LINKED_ORDER_YES = 1;//关联订单
    const FINANCIALS_LINKED_ORDER_NO = 2;//不关联订单




    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'code',
        'flow_id',
        'title',
        'user_id',
        'company_id',
        'primary_dept',
        'entry_id',
        'status',
        'budget_id',
        'expense_amount',
        'account_type',
        'account_number',
        'account_period',
        'unittype',
        'current_unit',
        'transaction',
        'loan_bill',
		'associated_projects',
		'linked_order',
        'endtime',
        'type_id',
        'procs_id',
        'reasons',
        'loan_bill_id',
        'projects_id',
        'order_id',
        'sum_money',
        'cur_money',
        'child_status',
        'applicant_chinese_name',
        'end_period_at',
        'bank',
        'bank_name',
        'bank_address',
        'company_account',
        'sof_id',
        'fee_booth'
	];
	/**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [
        "id"
    ];
    protected $hidden = [
        'deleted_at'
    ];
    public  function getEntry(){
        return $this->hasOne(Entry::class,'id', 'entry_id');
    }
    public  function getFlow(){
        return $this->hasOne(Flow::class,'id', 'flow_id')->select('id','flow_no','flow_name');
    }
    public function financeDetail(){
       return $this->hasMany(FinancialDetail::class,'financial_id','id');
    }
    public function financePic(){
        return $this->hasMany(FinancialPic::class,'financial_id','id')
            ->select('id','pic_url','pic_type','financial_id');
    }
    public  function users(){
        return $this->hasOne(User::class,'id', 'user_id')->select('id','chinese_name');
    }
    //付款组织
    public  function company(){
        return $this->hasOne(Department::class,'id', 'company_id')->select('id','name');
    }
    //付款部门
    public  function dept(){
        return $this->hasOne(Department::class,'id', 'primary_dept')->select('id','name');
    }
    //财务订单
    public function financeOrder(){
        return $this->hasMany(FinancialOrder::class,'financial_id','id');
    }
    //关联借款
    public function loan_bill(){
        return $this->hasMany(FinancialLoanBill::class,'financial_id','id');
    }


}

<?php

namespace App\Models\Workflow;

use App\Http\Helpers\Dh;
use App\Http\Helpers\StringHelper;
use App\Models\Financial;
use App\Models\Message\Message;
use App\Models\Welfare;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DevFixException;
use phpDocumentor\Reflection\Types\Self_;
use UserFixException;
use Exception;

/**
 * App\Models\Workflow\Entry
 *
 * @property int $id
 * @property string $title            标题
 * @property int $user_id
 * @property int $flow_id
 * @property int $process_id
 * @property int $circle
 * @property int $status           当前状态 0处理中
 *           9通过 -1驳回 -2撤销 -9草稿\n1：流程中\n9：处理完成
 * @property int $pid
 * @property int $enter_process_id
 * @property int $enter_proc_id
 * @property int $child
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Workflow\Process $child_process
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\Entry[] $children
 * @property-read \App\Models\Workflow\Process $enter_process
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\EntryData[] $entry_data
 * @property-read \App\Models\Workflow\Flow $flow
 * @property-read \App\Models\Workflow\Entry $parent_entry
 * @property-read \App\Models\Workflow\Process $process
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\Proc[] $procs
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereCircle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereEnterProcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereEnterProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @property int $origin_auth_id
 *           申请单真实登录人id
 * @property string $origin_auth_name
 *           申请单真实登录人姓名
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereOriginAuthId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereOriginAuthName($value)
 * @property string|null $finish_at 流程审批完成时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Entry whereFinishAt($value)
 */
class Entry extends Model
{
    // 申请单状态
    const STATUS_IN_HAND = 0; // 进行中
    const STATUS_REJECTED = -1; // 驳回
    const STATUS_CANCEL = -2; // 撤销
    const STATUS_DRAFT = -9; // 草稿
    const STATUS_FINISHED = 9; // 处理完成

    public static $_status = [
        self::STATUS_IN_HAND => '审批进行中',
        self::STATUS_REJECTED => '审批驳回',
        self::STATUS_CANCEL => '撤销审批',
        self::STATUS_DRAFT => '申请草稿',
        self::STATUS_FINISHED => '审批完成',
    ];

    //工作流特定流程编号，对应workflow_flows表的flow_no字段
    const WORK_FLOW_NO_CAPITAL_FEE_EXPENSE = 'fee_expense';//财务-报销
    const WORK_FLOW_NO_FINANCE_PAYMENT = 'finance_payment'; //财务-支付
    const WORK_FLOW_NO_HOLIDAY = 'holiday';//请假流程
    const WORK_FLOW_NO_COMPANY_INFO = 'company_info';//企业信息修改
    const WORK_FLOW_NO_ATTENDANCE_OVERTIME = 'attendance_overtime';//考勤-加班
    const WORK_FLOW_NO_ATTENDANCE_RETROACTIVE = 'attendance_retroactive';//考勤-打卡补签
    const WORK_FLOW_NO_ATTENDANCE_BUSINESS_TRAVEL = 'attendance_business_travel';//考勤-出差
    const WORK_FLOW_NO_ATTENDANCE_RESUMPTION = 'attendance_resumption';//考勤-销假
    const WORK_FLOW_NO_APPLY_CERTIFICATE = 'apply_certificate';//用章申请
    const WORK_FLOW_NO_CONTRACT_APPLY = 'contract_apply';//合同申请
    const WORK_FLOW_NO_FIXED_ASSET_PURCHASE_APPLY = 'asset_purchase';//采购申请
    const WORK_FLOW_NO_FIXED_ASSET_USE_APPLY = 'asset_apply';//领用申请
    //new flow no
    const WORK_FLOW_NO_ENTRY_APPlY = 'entry_apply';//入职申请
    const WORK_FLOW_NO_POSITIVE_APPlY = 'positive_apply';//转正申请
    const WORK_FLOW_NO_POSITIVE_WAGE_APPlY = 'positive_wage_apply';//转正工资包申请
    const WORK_FLOW_NO_ACTIVE_LEAVE_APPlY = 'active_leave_apply';//主动离职申请
    const WORK_FLOW_NO_FIRE_STAFF_APPlY = 'fire_staff_apply';//HR开除员工
    const WORK_FLOW_NO_LEAVE_HANDOVER_APPLY = 'leave_handover_apply';//离职交接单申请

    const WORK_FLOW_NO_CONTRACT_APPROVAL = 'contract_approval';//入职合同申请

    const WORK_FLOW_NO_FINANCE_LOAN = 'finance_loan'; //财务-借款
    const WORK_FLOW_NO_FINANCE_REPAYMENT = 'finance_repayment'; //财务-还款
    const WORK_FLOW_NO_FINANCE_RECEIVABLES = 'finance_receivables'; //财务-收款

    const VACATION_TYPE_EXTRA = 'extra'; //加班
    const VACATION_TYPE_LEAVE = 'leave';  //请假
    const WORK_FLOW_NO_OUTSIDE_PUNCH = 'outside_punch'; //外出
    const VACATION_TYPE_BUSINESS_TRIP = 'business_trip';  //出差
    const VACATION_TYPE_PATCH = 'patch';  //补卡
    const WORK_FLOW_NO_OFFICIAL_CONREACT = 'official_contract';
    const WORK_FLOW_NO_ADMINISTRATIVE_DOCUMENTS = 'administrative_documents';//行政文件

    const WORK_FLOW_NO_SALARY_COMPLAIN = 'salary_complain';//工资条申诉
    const WORK_FLOW_NO_PERFORMANCE_COMPLAIN = 'performance_complain';//绩效申诉

    const WORK_FLOW_NO_EXECUTIVE_CREATE_CAR = 'executive_cars';//行政新建车辆
    const WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_USE = 'executive_cars_use';//行政申请用车
    const WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_APPOINT = 'executive_cars_appoint';//行政申请用车
    const WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_SENDBACK = 'executive_cars_sendback';//行政归还用车
    const WORK_FLOW_NO_INTELLIGENCE_APPLY = 'intelligence_apply';//情报
    const WORK_FLOW_NO_INSPECTOR_APPLY = 'inspector_apply';//情报

    const WORK_FLOW_NO_WELFARE = 'welfare';//福利
    const WORK_FLOW_NO_SALARY_STATISTICS = 'salary_statistics';//薪资统计审批

    /***************资产*****************/
    const CORPORATE_ASSETS_USE = 'corporate_assets_use';//资产领用
    const CORPORATE_ASSETS_BORROW = 'corporate_assets_borrow';//资产借用
    const CORPORATE_ASSETS_RETURN = 'corporate_assets_return';//资产归还
    const CORPORATE_ASSETS_TRANSFER = 'corporate_assets_transfer';//资产调拨
    const CORPORATE_ASSETS_REPAIR = 'corporate_assets_repair';//资产送修
    const CORPORATE_ASSETS_SCRAPPED = 'corporate_assets_scrapped';//资产报废
    const CORPORATE_ASSETS_VALUEADDED = 'corporate_assets_valueadded';//资产增值
    const CORPORATE_ASSETS_DEPRECIATION = 'corporate_assets_depreciation';//资产折旧

    const ORDER_NO_MAP = [
        self:: WORK_FLOW_NO_ENTRY_APPlY =>          'EA',//入职申请
        self:: WORK_FLOW_NO_ACTIVE_LEAVE_APPlY =>   'ALA',//主动离职申请
        self:: WORK_FLOW_NO_FIRE_STAFF_APPlY =>     'FSA',//HR开除员工
        self:: WORK_FLOW_NO_LEAVE_HANDOVER_APPLY => 'LHA',//离职交接单申请
        self:: WORK_FLOW_NO_POSITIVE_APPlY =>       'PA',//转正申请
        self:: WORK_FLOW_NO_POSITIVE_WAGE_APPlY =>  'PWA',//转正工资包申请

        self::VACATION_TYPE_EXTRA                => 'JB', //加班
        self::VACATION_TYPE_LEAVE                => 'QJ',  //请假
        self::WORK_FLOW_NO_OUTSIDE_PUNCH         => 'WC', //外出
        self::VACATION_TYPE_BUSINESS_TRIP        => 'CC',  //出差
        self::VACATION_TYPE_PATCH                => 'BK',  //补卡
    ];

    //    use SoftDeletes;
    const STATUS_MAP = [
        self::STATUS_IN_HAND => '进行中',
        self::STATUS_FINISHED => '结束',
        self::STATUS_REJECTED => '驳回',
        self::STATUS_CANCEL => '撤销',
        self::STATUS_DRAFT => '草稿',
    ];
    //    use SoftDeletes;
    const VACATION_TYPE_ANNUAL = '1';  // 年假

    //行政部分
    const OFFICIAL_DOC = 'official_doc';  //公文

    //自定义路由设置
    const CUSTOMIZE = [
        'executive_cars',
        'executive_cars_use',
        'executive_cars_appoint',
        'executive_cars_sendback',
    ];
    protected $table = "workflow_entries";

    protected $fillable = [
        'title',
        'flow_id',
        'user_id',
        'status',
        'process_id',
        'circle',
        'enter_process_id',
        'child',
        'pid',
        'enter_proc_id',
        'finish_at',
        //'origin_auth_id',
        //'origin_auth_name',
        'order_no',
    ];

    public function flow()
    {
        return $this->belongsTo('App\Models\Workflow\Flow', "flow_id");
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', "user_id");
    }

    public function procs()
    {
        return $this->hasMany('App\Models\Workflow\Proc', "entry_id");
    }

    public function hasOneProcs()
    {
        return $this->hasOne('App\Models\Workflow\Proc', "entry_id")->select('id');
    }

    public function finance()
    {
        return $this->hasOne(Financial::class, "entry_id", 'id');
    }

    /**
     * @return Model|null|object|static
     */
    public function procsFirstNode()
    {
        return $this->hasMany('App\Models\Workflow\Proc', "entry_id")->orderBy('process_id')->first();
    }

    public function process()
    {
        return $this->belongsTo('App\Models\Workflow\Process', "process_id");
    }

    public function entry_data()
    {
        return $this->hasMany('App\Models\Workflow\EntryData', "entry_id");
    }

    public function entry_welfare()
    {
        return $this->hasOne(Welfare::class, "entries_id", "id");
    }

    public function parent_entry()
    {
        return $this->belongsTo('App\Models\Workflow\Entry', 'pid');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Workflow\Entry', 'pid');
    }

    public function enter_process()
    {
        return $this->belongsTo('App\Models\Workflow\Process', 'enter_process_id');
    }

    public function child_process()
    {
        return $this->belongsTo('App\Models\Workflow\Process', 'child');
    }

    /**
     * 是否草稿
     *
     * @return bool
     * @author hurs
     */
    public function isDraft()
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function isInHand()
    {
        return $this->status == self::STATUS_IN_HAND;
    }

    public function isFinish()
    {
        return $this->status == self::STATUS_FINISHED;
    }

    /**
     * 判断申请单是否可以修改
     *
     * @return bool
     * @throws \Exception
     */
    public function checkEntryCanUpdate()
    {
        if ($this->status != self::STATUS_DRAFT
//            && ($this->flow->flow_no != self::WORK_FLOW_NO_ACTIVE_LEAVE_APPlY)
//            && ($this->flow->flow_no != self::WORK_FLOW_NO_FIRE_STAFF_APPlY)
        ) {
            // 只有在草稿状态下才可以修改申请单数据
            throw new UserFixException("只有在草稿状态下才可以修改申请单数据");
        }

        return true;
    }

    /**
     * 根据id查询entry
     *
     * @param $id
     *
     */
    public static function findByIdOrFail($id)
    {
        return self::where('id', $id)->first();
    }

    /**
     * 获取主流程列表
     *
     * @param       $user_id
     * @param       $user_id
     * @param array $statusFilter
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getWithProProcess($user_id, $statusFilter = [], $limit = null)
    {
        $query = Entry::with([
            "procs" => function ($query) {
                $query->orderBy("id", 'DESC')->take(1);
            },
            "process",
        ]);

        if ($statusFilter !== null) {
            $query->whereIn('status', $statusFilter);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->where(['user_id' => $user_id, 'pid' => 0])->orderBy('id', 'DESC')->get();
    }

    /**
     * @param $user_id
     * @return Collection|static[]
     * @author hurs
     */
    public static function getTodoEntry($user_id)
    {
        return self::with('user', 'entry_data', 'flow')->with([
            'procs' => function ($query) use ($user_id) {
                $query->where('status', Proc::STATUS_IN_HAND)->where('user_id', $user_id);
            },
        ])->whereHas("procs", function ($query) use ($user_id) {
            $query->where('status', Proc::STATUS_IN_HAND)->where('user_id', $user_id);
        })->where('status', self::STATUS_IN_HAND)->orderBy('flow_id')->orderBy('id')->get();
    }

    /**
     * 获取用户的申请单列表
     *
     * @param     $user_id
     * @param     $params
     * @param int $pageSize
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getApplyEntries($user_id, $params, $pageSize = 20)
    {
        $query = self::orderBy('id', 'desc')->where(['user_id' => $user_id, 'pid' => 0]);

        $query->whereHas('flow', function ($q) use ($params) {
            if (isset($params['flow']) && !empty($params['flow'])) {
                $q->where('flow_no', '=', $params['flow']);
            }
        });

        if (isset($params['create_begin']) && !empty($params['create_begin'])) {
            $begin = Dh::getDateStart($params['create_begin'], false);
            $query->where('created_at', '>=', $begin);
        }

        if (isset($params['create_end']) && !empty($params['create_end'])) {
            $end = Dh::getDateEnd($params['create_end'], false);
            $query->where('created_at', '<', $end);
        }

        if (isset($params['entry_status']) && !StringHelper::isEmpty($params['entry_status'])) {
            $query->where('status', '=', $params['entry_status']);
        }

        return $query->paginate($pageSize, ['*'], 'Apply');
    }

    public static function getApplyEntryIdsByUser($user_id, $params)
    {
        $query = self::orderBy('id', 'desc')->where(['user_id' => $user_id, 'pid' => 0]);

        $flowNos = [
            Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE,
            Entry::WORK_FLOW_NO_FINANCE_PAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_LOAN,
            Entry::WORK_FLOW_NO_FINANCE_REPAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_RECEIVABLES
        ];
        $query->whereHas('flow', function ($q) use ($flowNos) {
            $q->where('flow_no', '=', $flowNos);
        });

        if (isset($params['create_begin']) && !empty($params['create_begin'])) {
            $begin = Dh::getDateStart($params['create_begin'], false);
            $query->where('created_at', '>=', $begin);
        }

        if (isset($params['create_end']) && !empty($params['create_end'])) {
            $end = Dh::getDateEnd($params['create_end'], false);
            $query->where('created_at', '<', $end);
        }

        if (isset($params['entry_status']) && !StringHelper::isEmpty($params['entry_status'])) {
            $query->where('status', '=', $params['entry_status']);
        }

        return $query->pluck('id');
    }

    public function getStatusDesc()
    {
        switch ($this->status) {
            case self::STATUS_IN_HAND:
                return '进行中';
            case self::STATUS_FINISHED:
                return '结束';
            case self::STATUS_REJECTED:
                return '驳回';
            case self::STATUS_CANCEL:
                return '撤销';
            case self::STATUS_DRAFT:
                return '草稿';
            default:
                throw new DevFixException('类型未定义:' . $this->status);
        }
    }

    public static function deleteEntry($id)
    {
        return Entry::whereIn('status', [Entry::STATUS_DRAFT, Entry::STATUS_IN_HAND])
            ->where('id', $id)
            ->update(['status' => self::STATUS_CANCEL]);
    }

    /**
     * 申请单数据与form匹配
     *
     * @return array
     */
    public function entryFormData($withHidden = false)
    {
        $formMap = [];
        foreach ($this->flow->template->template_form as $form) {
            if ($form->field_type != 'hidden' || $withHidden) {
                $formMap[$form->field] = $form->field_name;
            }
        }

        $datas = $this->entry_data;
        $dataForm = [];

        foreach ($datas as $data) {
            $field = $data->field_name;
            $value = $data->field_value;

            if (!isset($formMap[$field])) {
                // 把不需要显示的过滤掉
                continue;
            }
            $dataForm[$field] = [
                'value' => $value,
                'field' => $field,
                'field_name' => $formMap[$field],
            ];
        }

        return $dataForm;
    }

    /**
     * 获取表单当前审批节点数据
     *
     * @return Collection
     */
    public function getCurrentStepProcs()
    {
        return $this->hasMany('App\Models\Workflow\Proc', "entry_id")->get()->filter(function ($proc) {
            return $proc->status == self::STATUS_IN_HAND;
        });
    }

    /**
     * 查询用户的指定申请单
     *
     * @param $user_id
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public static function findUserEntry($user_id, $id)
    {
        $info= Entry::where('user_id', $user_id)->where('id',$id)->first();
        if(!$info){
            $info=false;
        }
        return $info;

    }

    public static function findUserEntryData($user_id, $id)
    {
        return Entry::with('flow')->where('user_id', $user_id)->findOrFail($id);
    }

    public function updateProcessId($process_id, $status = null)
    {
        if (!is_null($status)) {
            $data = self::STATUS_FINISHED == $status ? [
                'status' => $status,
                'process_id' => $process_id,
                'finish_at' => Dh::todayDateWithHourMinuteSecond(),
            ] : [
                'status' => $status,
                'process_id' => $process_id,
            ];
        } else {
            $data = ['process_id' => $process_id];
        }
        return $this->update($data);
    }

    public function updateParentChildId($process_id, $status = null)
    {
        if (!is_null($status)) {
            return $this->parent_entry->update([
                'status' => $status,
                'child' => $process_id,
            ]);
        } else {
            return $this->parent_entry->update([
                'child' => $process_id,
            ]);
        }
    }

    public function isChildEntry()
    {
        return $this->pid > 0;
    }

    public static function firstChildEntry($pid, $circle)
    {
        return Entry::where(['pid' => $pid, 'circle' => $circle])->first();
    }

    public static function createByParentProc(Proc $proc, Flowlink $flowlink)
    {
        return Entry::create([
            'title' => $proc->entry->title,
            'flow_id' => $flowlink->process->child_flow_id,
            'user_id' => $proc->entry->user_id,
            'status' => Entry::STATUS_IN_HAND,
            'pid' => $proc->entry->id,
            'circle' => $proc->entry->circle,
            'enter_process_id' => $flowlink->process_id,
            'enter_proc_id' => $proc->id,
        ]);
    }

    public static function fetchInProcessNum($workflow_no, $userId)
    {
        $flow = Flow::findByFlowNo($workflow_no);

        if (!$flow) {
            return 0;
        }

        return Entry::where('user_id', $userId)
            ->where('flow_id', $flow->id)
            ->whereIn('status', [self::STATUS_FINISHED, self::STATUS_IN_HAND])
            ->where('created_at', '>=', Dh::thisMonthStart(false))
            ->where('created_at', '<', Dh::thisMonthEnd(false))
            ->count();
    }

    public static function getAllApplyEntries($params, $pageSize = 20)
    {
        $query = self::with('user')
            ->orderBy('id', 'desc')
            ->where('pid', '=', 0)
            ->where('status', '<>', Entry::STATUS_DRAFT);

        $query->whereHas('user', function ($q) use ($params) {
            if (isset($params['userNameOrNo']) && !empty($params['userNameOrNo'])) {
                $q->where('chinese_name', 'like', "%" . $params['userNameOrNo'] . "%")
                    ->orWhere('employee_num', 'like', "%" . $params['userNameOrNo'] . "%");
            }
        });

        $query->whereHas('flow', function ($q) use ($params) {
            if (isset($params['flow']) && !empty($params['flow'])) {
                $q->where('flow_no', '=', $params['flow']);
            }
        });

        if (isset($params['create_begin']) && !empty($params['create_begin'])) {
            $begin = Dh::getDateStart($params['create_begin'], false);
            $query->where('created_at', '>=', $begin);
        }

        if (isset($params['create_end']) && !empty($params['create_end'])) {
            $end = Dh::getDateEnd($params['create_end'], false);
            $query->where('created_at', '<', $end);
        }

        if (isset($params['entry_status']) && !StringHelper::isEmpty($params['entry_status'])) {
            $query->where('status', '=', $params['entry_status']);
        }

        return $query->paginate($pageSize, ['*'], 'Apply');
    }

    public static function fetchEntry($uid, $flow_id, $begin_time, $end_time)
    {
        $arr = Entry::query()->where('user_id', '=', $uid)
            ->where('flow_id', '=', $flow_id)
            ->where('status', '=', self::STATUS_FINISHED)
            ->with('entry_data')->get();
        $res = [];
        foreach ($arr as $item) {
            /** @var EntryData $item */
            $data = $item->entry_data;
            $sTime = $eTime = '';
            foreach ($data as $datum) {
                if ($datum->field_name == 'begin_time') {
                    $sTime = date('Y-m-d', strtotime($datum->field_value));
                } elseif ($datum->field_name == 'end_time') {
                    $eTime = date('Y-m-d', strtotime($datum->field_value));
                }
            }
            if ($sTime <= $begin_time && $eTime >= $end_time) {
                $res[] = $item;
            }
        }
        return $res;
    }

    public static function generateOrderNo($flowNo)
    {
        $orderMap = self::ORDER_NO_MAP;
        //throw_if(!array_key_exists($flowNo, $orderMap), new Exception('不存在的审批单号前缀'));
        $prefix = isset($orderMap[$flowNo]) ? $orderMap[$flowNo] : 'OTHER';
        return getCode($prefix);
    }
}

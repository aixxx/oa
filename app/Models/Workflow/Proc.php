<?php

namespace App\Models\Workflow;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Http\Helpers\Dh;
use App\Http\Helpers\StringHelper;
use App\Models\Comments\TotalComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * App\Models\Workflow\Proc
 *
 * @property int $id
 * @property int $entry_id
 * @property int $flow_id 流程id
 * @property int $process_id
 * @property string $process_name
 * @property int $user_id
 * @property string $user_name 审核人名称
 * @property string $dept_name 审核人部门名称
 * @property int $auditor_id 具体操作人
 * @property string $auditor_name 操作人名称
 * @property string $auditor_dept 操作人部门
 * @property int $status 当前处理状态 0待处理 9通过 -1驳回\n0：处理中\n-1：驳回\n9：会签
 * @property string $content 批复内容
 * @property int $is_read 是否查看
 * @property int $is_real 审核人和操作人是否同一人
 * @property int $circle
 * @property string $beizhu
 * @property int $concurrence 并行查找解决字段， 部门 角色 指定 分组用
 * @property string $note
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $authorizer_ids 授权人id，可能是多个,逗号分隔
 * @property string $authorizer_names 授权人姓名，可能是多个,逗号分隔
 * @property-read \App\Models\Workflow\Entry $entry
 * @property-read \App\Models\Workflow\Flow $flow
 * @property-read \App\Models\Workflow\Process $process
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\Proc[] $procs
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereAuditorDept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereAuditorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereAuditorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereAuthorizerIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereAuthorizerNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereBeizhu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereCircle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereConcurrence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereDeptName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereIsReal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereProcessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereUserName($value)
 * @mixin \Eloquent
 * @property int $origin_auth_id 审批的真实登录用户
 * @property string $origin_auth_name 审批的真实登录用户姓名
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereOriginAuthId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Proc whereOriginAuthName($value)
 */
class Proc extends Model
{
    // 节点状态
    const STATUS_IN_HAND = 0; // 处理中
    const STATUS_PASSED = 9; // 通过
    const STATUS_REJECTED = -1; // 驳回

    const IS_READ_YES = 1; // 已查看
    const IS_READ_NO = 0; // 未查看

    const SYS_EXAM_AUTO_MERGE = 'sys_auto_merge'; // 系统审批,节点自动合并

    protected $table = "workflow_procs";

    protected $fillable = [
        'entry_id',
        'flow_id',
        'process_id',
        'process_name',
        'user_id',
        'status',
        'content',
        'is_read',
        'user_name',
        'dept_name',
        'auditor_id',
        'auditor_name',
        'auditor_dept',
        'circle',
        'beizhu',
        'concurrence',
        'authorizer_ids',
        'authorizer_names',
        'origin_auth_id',
        'origin_auth_name',
        'finish_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', "user_id");
    }

    public function entry()
    {
        return $this->belongsTo('App\Models\Workflow\Entry', "entry_id");
    }

    public function process()
    {
        return $this->belongsTo('App\Models\Workflow\Process', "process_id");
    }

    public function flow()
    {
        return $this->belongsTo('App\Models\Workflow\Flow', "flow_id");
    }
    //获取评论列表
    public function totalComments()
    {
        return $this->hasMany(TotalComment::class, "relation_id",'id')
            ->select('relation_id','comment_text','comment_img','comment_time');
    }


    public function procs()
    {
        return $this->hasMany('App\Models\Workflow\Proc', 'entry_id');
    }

    /**
     * 获取申请单的审批记录
     * @param $entry_id
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getEntryProcs($entry_id)
    {
        $procs = Proc::select(DB::raw("min(id) id,entry_id,process_id,process_name,GROUP_CONCAT(user_name SEPARATOR ' / ') user_name,user_id,auditor_name,auditor_id,status,content,max(updated_at) updated_at,finish_at"))
            ->with('entry', 'process')
            ->where(['entry_id' => $entry_id])
            ->where('user_id','>',0)
            ->groupBy('process_id', 'concurrence', 'circle')
            ->orderBy('id', 'ASC')
            ->get();
        return $procs;
    }

    /**
     * 获取申请单的审批记录,对外提供
     * @param $entry_id
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getEntryProcsForPublic($entry_id)
    {
        $procs = Proc::where(['entry_id' => $entry_id])->where('status', self::STATUS_PASSED)->orderBy('id', 'ASC')->get();
        return $procs;
    }

    /**
     * 获取某人的审批列表
     * 查看用户审核过的申请单
     * @param     $user_id
     * @param     $params
     * @param int $pageSize
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getUserAuditedByPage($user_id, $params, $pageSize = 20)
    {
        $query = static::userProcQuery($user_id, [self::STATUS_PASSED, self::STATUS_REJECTED]);

        $query->whereHas('process', function ($q) {
            // 第一步为申请步骤,所以此处不加载
            $q->where('position', '<>', 0);
        });

        $query->whereHas('flow', function ($q) use ($params) {
            if (isset($params['flow']) && !empty($params['flow'])) {
                $q->where('flow_no', '=', $params['flow']);
            }
        });

        $query->whereHas('entry', function ($q) use ($user_id, $params) {
            if (isset($params['create_begin']) && !empty($params['create_begin'])) {
                $begin = Dh::getDateStart($params['create_begin'], false);
                $q->where('created_at', '>=', $begin);
            }

            if (isset($params['create_end']) && !empty($params['create_end'])) {
                $end = Dh::getDateEnd($params['create_end'], false);
                $q->where('created_at', '<', $end);
            }

            if (isset($params['entry_status']) && !StringHelper::isEmpty($params['entry_status'])) {
                $q->where('status', '=', $params['entry_status']);
            }
        });

        if (isset($params['chinese_name']) && !empty($params['chinese_name'])) {
            $chineseName = $params['chinese_name'];
            $query->whereHas('entry.user', function ($q) use ($chineseName) {
                $q->where('chinese_name', 'like', "%$chineseName%");
            });
        }


        if(isset($params['flow_id']) && !empty($params['flow_id'])){
            $flow_id = $params['flow_id'];
            $query->whereIn('flow_id', $flow_id);
        }
        return $query->where(['auditor_id' => $user_id])->distinct()->orderBy('id', 'desc')->paginate($pageSize, ['*'], 'Audited');
    }

    /**
     * 查询待审批的申请单
     * @param     $user_id
     * @param     $params
     * @param int $pageSize
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getUserProcByPage($user_id, $params, $pageSize = 20)
    {
        $query = static::userProcQuery($user_id);

        $query->whereHas('flow', function ($q) use ($params) {
            if (isset($params['flow']) && !empty($params['flow'])) {
                $q->where('flow_no', '=', $params['flow']);
            }
        });

        $query->whereHas('entry', function ($q) use ($params) {
            // 申请单需要在申请中状态,撤销的状态不需要显示在这里
            $q->where('status', '=', self::STATUS_IN_HAND);

            if (isset($params['create_begin']) && !empty($params['create_begin'])) {
                $begin = Dh::getDateStart($params['create_begin'], false);
                $q->where('created_at', '>=', $begin);
            }

            if (isset($params['create_end']) && !empty($params['create_end'])) {
                $end = Dh::getDateEnd($params['create_end'], false);
                $q->where('created_at', '<', $end);
            }
        });

        if (isset($params['chinese_name']) && !empty($params['chinese_name'])) {
            $chineseName = $params['chinese_name'];
            $query->whereHas('entry.user', function ($q) use ($chineseName) {
                $q->where('chinese_name', 'like', "%$chineseName%");
            });
        }

        if(isset($params['flow_id']) && !empty($params['flow_id'])){
            $flow_id = $params['flow_id'];
            $query->whereIn('flow_id', $flow_id);
        }

        return $query->orderBy('id', 'desc')->paginate($pageSize, ['*'], 'Proc');
    }

    /**
     * 获取某人的财务相关申请单
     * 查看财务审核过的申请单
     * @param     $user_id
     * @param     $params
     * @param int $pageSize
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getUserFinanceAuditedByPage($user_id, $params, $pageSize = 20)
    {
        $query = static::userProcQuery($user_id, [self::STATUS_PASSED, self::STATUS_REJECTED]);

        $query->whereHas('process', function ($q) {
            // 第一步为申请步骤,所以此处不加载
            $q->where('position', '<>', 0);
        });

        $flowNos = [
            Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE,
            Entry::WORK_FLOW_NO_FINANCE_PAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_LOAN,
            Entry::WORK_FLOW_NO_FINANCE_REPAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_RECEIVABLES
        ];

        $query->whereHas('flow', function ($q) use ($flowNos) {
            $q->whereIn('flow_no', $flowNos);
        });

        $query->whereHas('entry', function ($q) use ($user_id, $params) {
            if (isset($params['create_begin']) && !empty($params['create_begin'])) {
                $begin = Dh::getDateStart($params['create_begin'], false);
                $q->where('created_at', '>=', $begin);
            }

            if (isset($params['create_end']) && !empty($params['create_end'])) {
                $end = Dh::getDateEnd($params['create_end'], false);
                $q->where('created_at', '<', $end);
            }

            if (isset($params['entry_status']) && !StringHelper::isEmpty($params['entry_status'])) {
                $q->where('status', '=', $params['entry_status']);
            }
        });

        if (isset($params['chinese_name']) && !empty($params['chinese_name'])) {
            $chineseName = $params['chinese_name'];
            $query->whereHas('entry.user', function ($q) use ($chineseName) {
                $q->where('chinese_name', 'like', "%$chineseName%");
            });
        }

        return $query->where(['auditor_id' => $user_id])->distinct()->orderBy('id', 'desc')->paginate($pageSize, ['*'], 'Audited');
    }

    /**
     * 查询待审批的财务相关申请单
     * @param     $user_id
     * @param     $params
     * @param int $pageSize
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getUserFinanceProcByPage($user_id, $params, $pageSize = 20)
    {
        $query = static::userProcQuery($user_id);
        $flowNos = [
            Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE,
            Entry::WORK_FLOW_NO_FINANCE_PAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_LOAN,
            Entry::WORK_FLOW_NO_FINANCE_REPAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_RECEIVABLES
        ];

        $query->whereHas('flow', function ($q) use ($flowNos) {
            $q->whereIn('flow_no', $flowNos);
        });

        $query->whereHas('entry', function ($q) use ($params) {
            // 申请单需要在申请中状态,撤销的状态不需要显示在这里
            $q->where('status', '=', self::STATUS_IN_HAND);

            if (isset($params['create_begin']) && !empty($params['create_begin'])) {
                $begin = Dh::getDateStart($params['create_begin'], false);
                $q->where('created_at', '>=', $begin);
            }

            if (isset($params['create_end']) && !empty($params['create_end'])) {
                $end = Dh::getDateEnd($params['create_end'], false);
                $q->where('created_at', '<', $end);
            }
        });

        if (isset($params['chinese_name']) && !empty($params['chinese_name'])) {
            $chineseName = $params['chinese_name'];
            $query->whereHas('entry.user', function ($q) use ($chineseName) {
                $q->where('chinese_name', 'like', "%$chineseName%");
            });
        }

        return $query->orderBy('id', 'desc')->paginate($pageSize, ['*'], 'Proc');
    }

    /**
     * 获取某人的待审批列表
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author hurs
     */
    public static function getUserProc($user_id, $limit = null)
    {
        $query = static::userProcQuery($user_id);

        $query->whereHas('entry', function ($q) {
            // 申请单需要在申请中状态
            $q->where('status', '=', self::STATUS_IN_HAND);
        });

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->orderBy("is_read", "ASC")->orderBy("status", "ASC")->orderBy("id", "DESC")->get();
    }

    /**
     * 获取未审批某人的entry_ids
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author hurs
     */
    public static function getProcEntryIdsByUser($user_id, $params)
    {
        $query = static::userProcQuery($user_id, $params['entry_status']);
        $flowNos = [
            Entry::WORK_FLOW_NO_CAPITAL_FEE_EXPENSE,
            Entry::WORK_FLOW_NO_FINANCE_PAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_LOAN,
            Entry::WORK_FLOW_NO_FINANCE_REPAYMENT,
            Entry::WORK_FLOW_NO_FINANCE_RECEIVABLES
        ];

        $query->whereHas('flow', function ($q) use ($flowNos) {
            $q->whereIn('flow_no', $flowNos);
        });

        if (isset($params['create_begin']) && !empty($params['create_begin'])) {
            $begin = Dh::getDateStart($params['create_begin'], false);
            $query->where('created_at', '>=', $begin);
        }

        if (isset($params['create_end']) && !empty($params['create_end'])) {
            $end = Dh::getDateEnd($params['create_end'], false);
            $query->where('created_at', '<', $end);
        }

        return $query->pluck('entry_id');
    }

    /**
     * 用户审批申请单查询
     * @param     $user_id
     * @param int $status
     *
     * @return $this
     */
    private static function userProcQuery($user_id, $status = [self::STATUS_IN_HAND])
    {
        $query = Proc::whereHas('entry')
            ->with("entry.user")
            ->where(['user_id' => $user_id])
            ->whereIn('status', $status);

        return $query;
    }

    public static function findUserProc($user_id, $id)
    {
        /*return Proc::with('entry.user')
            ->where(['user_id' => $user_id])
            ->where(["status" => Proc::STATUS_IN_HAND])
            ->findOrFail($id);*/
        /**
         * findOrFail 方法直接抛异常出去
         * 根据业务需求， 查询不到Proc的时候， 返回 ： 已审批
         * 避免修改代码地方过多， 直接在这里判断 抛出异常
         * 2019-06-01 xuxiao
         */
        $info = Proc::with('entry.user')
            ->where(['user_id' => $user_id])
            ->where(["status" => Proc::STATUS_IN_HAND])
            ->find($id);
        if(empty($info))
            throw new DiyException(ConstFile::$contractStatusMsg[ConstFile::CONTRACT_STATUS_TWO], ConstFile::API_RESPONSE_FAIL);
        return $info;
    }

    /**
     * 根据用户Id 和 entry_id 获取Proc
     * @param $user_id
     * @param $entry_id
     * @return Proc|Model|object|null
     */
    public static function findUserProcByEntryId($user_id, $entry_id)
    {
        return Proc::where(['user_id' => $user_id])
            ->where(["entry_id" => $entry_id])
            ->first();
    }

    public static function finishProc(Proc $proc, $exam_user_id, $exam_user_name, $login_user_id, $login_user_name, $content = '')
    {
        Proc::where([
            'entry_id' => $proc->entry_id,
            'process_id' => $proc->process_id,
            'circle' => $proc->entry->circle,
            'status' => Proc::STATUS_IN_HAND,
        ])->update([
            'status' => Proc::STATUS_PASSED,
            'auditor_id' => $exam_user_id,
            'auditor_name' => $exam_user_name,
            'auditor_dept' => '',//Auth::user()->dept->dept_name,
            'content' => $content,
            'origin_auth_id' => $login_user_id,
            'origin_auth_name' => $login_user_name,
            'finish_at' => (new Carbon())->toDateTimeString(),
        ]);
    }

    /**
     * 查询用户审批节点信息,不过滤节点状态
     * @param $user_id
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public static function findUserProcAllStatus($user_id, $id)
    {
        return static::where('user_id', $user_id)->findOrFail($id);
    }
}

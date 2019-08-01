<?php

namespace App\Models\Workflow;

use App\Models\Company;
use App\Models\Message\Message;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AuthUserShadowService;
use App\Services\ContractService;
use App\Services\Workflow\WorkFlowBusinessService;
use App\Services\WorkflowMessageService;
use App\Services\WorkflowUserService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use UserFixException;
use DevFixException;
use Exception;
use HurryHandleException;
use Auth;

/**
 * App\Models\Workflow\Workflow
 *
 * @property int $id
 * @property string $flow_no
 * @property string $flow_name
 * @property int $template_id
 * @property int $type_id
 * @property string $flowchart
 * @property string $jsplumb
 * @property int $is_publish
 * @property int $is_show
 * @property string $introduction 流程说明
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $leader_link_type 领导人审批条线,business:业务条线;report:汇报关系条线
 * @property int $version 版本号，以时间戳做版本
 * @property int $is_abandon 是否废弃，1：废弃；0：未废弃
 * @property int $create_user_id 创建人Id
 * @property int $clone_from_flow_id 克隆自哪只流程
 * @property string|null $can_view_users 能看到本流程的用户id集合
 * @property string|null $can_view_departments 能看到本流程的部门id集合
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereCanViewDepartments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereCanViewUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereCloneFromFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereFlowName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereFlowNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereFlowchart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereIsAbandon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereIsPublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereJsplumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereLeaderLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Workflow whereVersion($value)
 * @mixin \Eloquent
 */
class Workflow extends Model
{
    protected $table = 'workflow_flows';

    const STATUS_REJECTED = -1;//驳回
    const STATUS_FLOWING = 0;//处理中
    const STATUS_COMPLETED = 9;//处理完成

    // 审批人标志
    const SYS_AUDITOR_APPLYERE = '-1000'; // 申请人自身
    // 部分负责人审批层级标记
    const SYS_AUDITOR_DEPART_HEAD_FLAG_LVL1 = '-2001'; // 一级部门负责人即中心负责人
    const SYS_AUDITOR_DEPART_HEAD_FLAG_LVL2 = '-2002'; // 二级部门负责人
    const SYS_AUDITOR_DEPART_HEAD_FLAG_LVL3 = '-2003'; // 三级部门负责人
    const SYS_AUDITOR_DEPART_HEAD_FLAG_LVL4 = '-2004'; // 四级部门负责人
    const SYS_AUDITOR_SUPERIOR_LEADER_LVL1 = '-4001'; // 一级领导
    const SYS_AUDITOR_SUPERIOR_LEADER_LVL2 = '-4002'; // 二级领导
    const SYS_AUDITOR_SUPERIOR_LEADER_LVL3 = '-4003'; // 三级领导
    const SYS_AUDITOR_SUPERIOR_LEADER_LVL4 = '-4004'; // 四级领导
    const SYS_AUDITOR_NO_BODY = '-5001'; // 不需要审批

    // 部分负责人审批层级标记map
    const SYS_AUDITOR_FLAG_MAP = [
        self::SYS_AUDITOR_APPLYERE              => '发起人',
        self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL1 => '一级部门负责人',
        self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL2 => '二级部门负责人',
        self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL3 => '三级部门负责人',
        self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL4 => '四级部门负责人',
        self::SYS_AUDITOR_SUPERIOR_LEADER_LVL1  => '一级领导',
        self::SYS_AUDITOR_SUPERIOR_LEADER_LVL2  => '二级领导',
        self::SYS_AUDITOR_SUPERIOR_LEADER_LVL3  => '三级领导',
        self::SYS_AUDITOR_SUPERIOR_LEADER_LVL4  => '四级领导',
        self::SYS_AUDITOR_NO_BODY               => '自动审批',
    ];

    const MAX_PASS_TIMES = 30; // 最大通过次数,防止节点配置出现死循环
    private $passedTimes = 0;  // 记录执行pass操作的次数

    private $authAuditor; // 审批人信息

    public function __construct()
    {
        $this->authAuditor = new AuthUserShadowService();
    }

    /**
     * getNextAuditorIds 获得下一步审批人员工id
     *
     * @param Entry $entry
     * @param       $process_id
     *
     * @return array
     * @author hurs
     */
    private function getProcessAuditorIds(Entry $entry, $process_id)
    {
        $auditor_ids = [];
        //查看是否自动选人
        if ($flowlink = Flowlink::firstSysLink($process_id)) {
            if ($flowlink->auditor == self::SYS_AUDITOR_APPLYERE) {
                //发起人
                $auditor_ids[] = $entry->user_id;
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL4) {
                // 四级部门负责人
                $auditor_ids = array_merge($auditor_ids, self::getApplyerDepartHead($entry->user_id, 4));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL3) {
                // 三级部门负责人
                $auditor_ids = array_merge($auditor_ids, self::getApplyerDepartHead($entry->user_id, 3));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL2) {
                // 二级部门负责人
                $auditor_ids = array_merge($auditor_ids, self::getApplyerDepartHead($entry->user_id, 2));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL1) {
                // 一级部门负责人
                $auditor_ids = array_merge($auditor_ids, self::getApplyerDepartHead($entry->user_id, 1));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_SUPERIOR_LEADER_LVL1) {
                // 一级领导
                $auditor_ids = array_merge($auditor_ids, self::getApplyerSuperLeader($entry->user_id, 1));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_SUPERIOR_LEADER_LVL2) {
                // 二级领导
                $auditor_ids = array_merge($auditor_ids, self::getApplyerSuperLeader($entry->user_id, 2));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_SUPERIOR_LEADER_LVL3) {
                // 三级领导
                $auditor_ids = array_merge($auditor_ids, self::getApplyerSuperLeader($entry->user_id, 3));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_SUPERIOR_LEADER_LVL4) {
                // 四级领导
                $auditor_ids = array_merge($auditor_ids, self::getApplyerSuperLeader($entry->user_id, 4));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_NO_BODY) {
                // 自动审批
                $auditor_ids[] = 0;
            }
        } else {
            //并行
            if ($flowlink = Flowlink::firstEmpLink($process_id)) {
                //指定员工
                $auditor_ids = array_merge($auditor_ids, explode(',', $flowlink->auditor));
            }

            if ($flowlink = Flowlink::firstDeptLink($process_id)) {
                //指定部门
                $dept_ids = explode(',', $flowlink->auditor);

                $emp_ids = User::whereIn('dept_id', $dept_ids)->get()->pluck('id')->toArray();

                $auditor_ids = array_merge($auditor_ids, $emp_ids);
            }

            if ($flowlink = Flowlink::firstRoleLink($process_id)) {
                //指定角色
                $role_ids = explode(',', $flowlink->auditor);
                if ($flowlink->depands) {
                    try {
                        $company_name = $entry->entry_data()->where('field_name', $flowlink->depands)->first()->field_value;
                        if (!is_numeric($company_name)) {
                            $company_id = Company::getCompanyIdByName($company_name)->id;
                        } else {
                            $company_id = $company_name;
                        }
                        $roles = WorkflowRole::getCompanyRoleUserByIds($company_id, $role_ids);
                    } catch (Exception $e) {
                        report($e);
                        $roles = [];
                    }
                } else {
                    $roles = WorkflowRole::getCompanyRoleUserByIds($entry->user->company_id, $role_ids);
                }
                foreach ($roles as $role) {
                    foreach ($role->roleUser as $roleUser) {
                        $auditor_ids[] = $roleUser->user_id;
                    }
                }
            }
        }
        return array_unique($auditor_ids);
    }

    /**
     * getNextAuditorIds 获得下一步审批人员工id
     *
     * @param Entry $entry
     * @param       $process_id
     *
     * @return array
     * @author hurs
     */
    public static function getProcessAuditorIdsContract(Entry $entry, $process_id)
    {
        $auditor_ids = [];
        //查看是否自动选人
        if ($flowlink = Flowlink::firstSysLink($process_id)) {
            if ($flowlink->auditor == self::SYS_AUDITOR_APPLYERE) {
                //发起人
                $auditor_ids[] = $entry->user_id;
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL4) {
                // 四级部门负责人
                $auditor_ids = array_merge($auditor_ids, self::getApplyerDepartHead($entry->user_id, 4));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL3) {
                // 三级部门负责人
                $auditor_ids = array_merge($auditor_ids, self::getApplyerDepartHead($entry->user_id, 3));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL2) {
                // 二级部门负责人
                $auditor_ids = array_merge($auditor_ids, self::getApplyerDepartHead($entry->user_id, 2));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_DEPART_HEAD_FLAG_LVL1) {
                // 一级部门负责人
                $auditor_ids = array_merge($auditor_ids, self::getApplyerDepartHead($entry->user_id, 1));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_SUPERIOR_LEADER_LVL1) {
                // 一级领导
                $auditor_ids = array_merge($auditor_ids, self::getApplyerSuperLeader($entry->user_id, 1));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_SUPERIOR_LEADER_LVL2) {
                // 二级领导
                $auditor_ids = array_merge($auditor_ids, self::getApplyerSuperLeader($entry->user_id, 2));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_SUPERIOR_LEADER_LVL3) {
                // 三级领导
                $auditor_ids = array_merge($auditor_ids, self::getApplyerSuperLeader($entry->user_id, 3));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_SUPERIOR_LEADER_LVL4) {
                // 四级领导
                $auditor_ids = array_merge($auditor_ids, self::getApplyerSuperLeader($entry->user_id, 4));
            } elseif ($flowlink->auditor == self::SYS_AUDITOR_NO_BODY) {
                // 自动审批
                $auditor_ids[] = 0;
            }
        } else {
            //并行
            if ($flowlink = Flowlink::firstEmpLink($process_id)) {
                //指定员工
                $auditor_ids = array_merge($auditor_ids, explode(',', $flowlink->auditor));
            }

            if ($flowlink = Flowlink::firstDeptLink($process_id)) {
                //指定部门
                $dept_ids = explode(',', $flowlink->auditor);

                $emp_ids = User::whereIn('dept_id', $dept_ids)->get()->pluck('id')->toArray();

                $auditor_ids = array_merge($auditor_ids, $emp_ids);
            }

            if ($flowlink = Flowlink::firstRoleLink($process_id)) {
                //指定角色
                $role_ids = explode(',', $flowlink->auditor);
                if ($flowlink->depands) {
                    try {
                        $company_name = $entry->entry_data()->where('field_name', $flowlink->depands)->first()->field_value;
                        if (!is_numeric($company_name)) {
                            $company_id = Company::getCompanyIdByName($company_name)->id;
                        } else {
                            $company_id = $company_name;
                        }
                        $roles = WorkflowRole::getCompanyRoleUserByIds($company_id, $role_ids);
                    } catch (Exception $e) {
                        report($e);
                        $roles = [];
                    }
                } else {
                    $roles = WorkflowRole::getCompanyRoleUserByIds($entry->user->company_id, $role_ids);
                }
                foreach ($roles as $role) {
                    foreach ($role->roleUser as $roleUser) {
                        $auditor_ids[] = $roleUser->user_id;
                    }
                }
            }
        }
        return array_unique($auditor_ids);
    }

    /**
     * @param Entry $entry
     *
     * @return Process[]
     * @author hurs
     */
    public function getProcs(Entry $entry)
    {
        $procs      = Proc::getEntryProcs($entry->id);
        $workflow   = new Workflow();
        $processes  = new Collection();
        $process_id = 0;
        foreach ($procs as $proc) {
            $proc->process->auditors    = implode(" / ", $this->getProcessAuditorNames($entry, $proc->process_id, $user_ids));
            $proc->process->proc        = $proc;
            $proc->process->auditor_ids = $user_ids;
            $processes->add($proc->process);
            $process_id = $proc->process_id;
        }
        while ($process_id != Flowlink::LAST_FLOW_LINK) {
            $flowlink = $workflow->getPassFlowLink($entry->id, $process_id);
            if (!$flowlink || $flowlink->next_process_id == Flowlink::LAST_FLOW_LINK || $flowlink->auditor == Workflow::SYS_AUDITOR_NO_BODY) {
                break;
            }
            if ($entry->status == Entry::STATUS_CANCEL) {
//                $obj = new \stdClass();
//                $obj->process_name = '我';
//                $obj->auditor_name = '我';
//                $obj->status = Entry::STATUS_CANCEL;
//                $obj->status_name = Entry::$_status[Entry::STATUS_CANCEL];
//                $obj->proc = null;
//                $processes->add($obj);
                break;
            } else {
                $process              = Process::findOrFail($flowlink->next_process_id);
                $process->auditors    = implode(" / ", $this->getProcessAuditorNames($entry, $process->id, $user_ids));
                $process->auditor_ids = $user_ids;
                $processes->add($process);
            }

            $process_id = $flowlink->next_process_id;
        };

        return $processes;
    }

    private function getProcessAuditorNames(Entry $entry, $process_id, &$user_ids = null)
    {
        $user_ids = $this->getProcessAuditorIds($entry, $process_id);
        if ($user_ids == [0]) {
            $users = collect([User::noBody()]);
        } else {
            $users = User::findByIds($user_ids);
        }
        return $users->pluck('chinese_name')->toArray();
    }

    /**
     * [setFirstProcessAuditor 初始流转]
     *
     * @param [type] $entry    [description]
     * @param [type] $flowlink [description]
     */
    public function setFirstProcessAuditor(Entry $entry, Flowlink $flowlink)
    {
        // 提交前校验表单
        $workflowBusiness = new WorkFlowBusinessService($entry);
        $workflowBusiness->checkValidate(); // 校验申请单数据是否有效
        if (!Flowlink::firstNotConditionLink($flowlink->process_id)) {
            //第一步未指定审核人 自动进入下一步操作
            $proc = $entry->procs()->create([
                'flow_id'      => $entry->flow_id,
                'process_id'   => $flowlink->process_id,
                'process_name' => $flowlink->process->process_name,
                'user_id'      => $entry->user_id,
                'user_name'    => $entry->user->chinese_name,
                'dept_name'    => '',//$entry->user->departments->name,
                'auditor_id'   => $entry->user_id,
                'auditor_name' => $entry->user->chinese_name,
                'auditor_dept' => '', //$entry->user->departments->name,
                'status'       => Proc::STATUS_IN_HAND,
                'circle'       => $entry->circle,
                'concurrence'  => time(),
            ]);

            $this->pass($proc->id);
        } else {
            $entry->process_id = $flowlink->process_id;
            $this->goToProcess($entry, $flowlink->process_id);

            $entry->save();
        }
        WorkflowMessageService::passNotify($entry);
    }

    /**
     * flowlink 流转,并通知
     *
     * @param $proc_id
     */
    public function passWithNotify($proc_id)
    {
        $this->pass($proc_id);
        WorkflowMessageService::passNotify(Proc::find($proc_id)->entry);
    }

    /**
     * flowlink 流转
     *
     * @param      $proc_id        int 当前审批节点的proc id
     * @param null $exam_user_id int 审批人员id
     * @param null $exam_user_name string 审批人姓名
     *
     * @throws \Exception
     */
    public function pass($proc_id, $exam_user_id = null, $exam_user_name = null)
    {
        if ($this->passedTimes > self::MAX_PASS_TIMES) {
            // 已经超过最大执行次数
            throw new DevFixException(sprintf('审批节点流转出现死循环,请检查,proc_id:%s', $proc_id));
        }

        $exam_user_id   = !is_null($exam_user_id) ? $exam_user_id : $this->authAuditor->id();
        $exam_user_name = !is_null($exam_user_name) ? $exam_user_name : $this->authAuditor->user()->chinese_name;

        $proc = Proc::findUserProc($exam_user_id, $proc_id);
        if (!($proc->entry->isInHand() || $proc->entry->isDraft())) {
            // 草稿状态在第一步提交申请的时候会走到这里,安全起见这里先临时处理下增加个草稿状态
            throw new UserFixException('流程状态已变更、请刷新后重试');
        }
        //有条件
        $flowlink = self::getPassFlowLink($proc->entry_id, $proc->process_id);
        if (empty($flowlink)) {
            throw new DevFixException('未满足流转条件，无法流转到下一步骤，请联系流程设置人员', 1);
        }

        if ($flowlink->process->child_flow_id > 0) {
            // 创建子流程
            $this->startChildProcess($proc, $flowlink);
        } elseif ($flowlink->NestIsLast()) {
            //最后一步
            $proc->entry->updateProcessId($flowlink->process_id, Entry::STATUS_FINISHED);
            /**************审批驳回， 发送通知******************/
            Message::addProc($proc, Message::MESSAGE_TYPE_WORKFLOW_PASS);
            //子流程结束
            if ($proc->entry->isChildEntry()) {
                if ($proc->entry->enter_process->child_after == 1) {
                    //同时结束父流程
                    $proc->entry->updateParentChildId(0, Entry::STATUS_FINISHED);
                    //                    WorkflowMessageService::completeNotify(Proc::find($proc->id));//todo 是否需要通知？还是应该调父流程的pass
                } else {
                    //进入设置的父流程步骤
                    if ($proc->entry->enter_process->child_back_process > 0) {
                        $this->goToProcess($proc->entry->parent_entry, $proc->entry->enter_process->child_back_process);
                        $proc->entry->parent_entry->updateProcessId($proc->entry->enter_process->child_back_process);
                    } else {
                        //默认进入父流程步骤下一步
                        $parent_flowlink = Flowlink::firstConditionLink($proc->entry->enter_process->id);

                        //判断是否为最后一步
                        if ($parent_flowlink->NestIsLast()) {
                            $proc->entry->parent_entry->updateProcessId($proc->entry->enter_process->child_back_process,
                                Entry::STATUS_FINISHED);
                            WorkflowMessageService::completeNotify(Proc::find($proc->id));
                        } else {
                            $this->goToProcess($proc->entry->parent_entry, $parent_flowlink->next_process_id);
                            $proc->entry->parent_entry->updateProcessId($parent_flowlink->next_process_id,
                                Entry::STATUS_IN_HAND);
                        }
                    }
                    $proc->entry->updateParentChildId(0);
                }

            } else {
                //流程结束通知
                WorkflowMessageService::completeNotify(Proc::find($proc->id));
            }
        } else {
            $this->goToProcess($proc->entry, $flowlink->next_process_id);

            $proc->entry->updateProcessId($flowlink->next_process_id);

            //如果父进程存在，修改父进程child process id
            if ($proc->entry->isChildEntry()) {
                $proc->entry->updateParentChildId($flowlink->next_process_id);
            }
        }
        //把proc标记成完成
        Proc::finishProc(
            $proc, $exam_user_id, $exam_user_name, Auth::id(), Auth::user()->chinese_name,
            \Request::input('content', '')
        );
        // 节点审批通过触发节点配置的事件
        $this->triggerEvent($proc);

        $this->passedTimes++;

        $this->tryMergeNextProc($proc);
    }

    public function reject($proc_id, $content = '')
    {
        $proc = Proc::findUserProc($this->authAuditor->id(), $proc_id);
        if (!$proc->entry->isInHand()) {
            throw new UserFixException('流程状态已变更、请刷新后重试');
        }
        //驳回
        Proc::where([
            'entry_id'   => $proc->entry_id,
            'process_id' => $proc->process_id,
            'circle'     => $proc->entry->circle,
            'status'     => Proc::STATUS_IN_HAND,
        ])->update([
            'status'           => Proc::STATUS_REJECTED,
            'auditor_id'       => $this->authAuditor->id(),
            'auditor_name'     => $this->authAuditor->user()->chinese_name,
            'auditor_dept'     => '',//$this->authAuditor->user()->dept->dept_name, //Auth::user()->dept->dept_name,
            'content'          => $content,
            'origin_auth_id'   => Auth::id(),
            'origin_auth_name' => Auth::user()->chinese_name,
            'finish_at'        => (new Carbon())->toDateTimeString(),
        ]);

        $proc->entry()->update([
            'status' => Entry::STATUS_REJECTED,
        ]);

        //判断是否存在父进程
        if ($proc->entry->pid > 0) {
            $proc->entry->parent_entry->update([
                'status' => Entry::STATUS_REJECTED,
                'child'  => $proc->process_id,
            ]);
        }
        //驳回申请之后需要调用的事件
        $this->triggerEvent($proc, false);
        WorkflowMessageService::rejectNotify(Proc::find($proc->id));
    }

    private function tryMergeNextProc(Proc $proc)
    {
        // 判断是否是之前已经审批过的,如果是则合并
        $curr_in_hand_process_id = $proc->entry->process_id; // 当前处在审批状态的节点
        if ($proc->entry->process->can_merge == Process::CAN_MERGE_YES && $repeat_process_id = $this->checkIsRepeatProcExam($proc->entry)) {
            // 获取重复审批人节点的proc
            $proc_passed = Proc::where([
                'entry_id'   => $proc->entry_id,
                'process_id' => $repeat_process_id,
                'circle'     => $proc->entry->circle,
                'status'     => Proc::STATUS_PASSED,
            ])->first();

            // 有重复审批节点,并且节点可以合并
            $proc_in_hand = Proc::where([
                'entry_id'   => $proc->entry_id,
                'process_id' => $curr_in_hand_process_id,
                'circle'     => $proc->entry->circle,
                'status'     => Proc::STATUS_IN_HAND,
                'user_id'    => $proc_passed->user_id // 此处不能使用用户登录id,应该用之前重复审批人节点的用户id,否则当前节点不包含登录人员的话无法执行pass
            ])->first();

            $this->pass($proc_in_hand->id, $proc_passed->user_id, $proc_passed->user_name);

            // 合并节点直接通过信息标记
            Proc::where([
                'entry_id'   => $proc->entry_id,
                'process_id' => $curr_in_hand_process_id,
                'circle'     => $proc->entry->circle,
                'status'     => Proc::STATUS_PASSED,
            ])->update([
                //                'auditor_id'   => 0,
                //                'auditor_name' => Proc::SYS_EXAM_AUTO_MERGE, // 系统自动审批
                //                'auditor_dept' => '',
                'content'   => '审批人相同,系统自动审批',
                'finish_at' => (new Carbon())->toDateTimeString(),
            ]);
            return;
        }
        $flowlink = Flowlink::firstSysLink($curr_in_hand_process_id);
        if ($flowlink && $flowlink->auditor == Workflow::SYS_AUDITOR_NO_BODY) {
            $proc_in_hand = Proc::where([
                'entry_id'   => $proc->entry_id,
                'process_id' => $curr_in_hand_process_id,
                'circle'     => $proc->entry->circle,
                'status'     => Proc::STATUS_IN_HAND,
                'user_id'    => 0,
            ])->first();
            if (!$proc_in_hand) {
                return;
            }
            $this->pass($proc_in_hand->id, 0, '');
        }
    }

    public function triggerEvent(Proc $process, $pass = true)
    {
        $events = $pass ? $process->process->getPassEvents() : $process->process->getRejectEvents();
        if (!empty($events)) {
            foreach ($events as $event) {
                event(new $event($process->id));
            }
        }
    }

    private function startChildProcess(Proc $proc, Flowlink $flowlink)
    {
        if ($flowlink->process->child_flow_id > 0) {
            if (!$child_entry = Entry::firstChildEntry($proc->entry->id, $proc->entry->circle)) {
                $child_entry = Entry::createByParentProc($proc, $flowlink);
            }
            $child_flowlink = Flowlink::firstStepLink($flowlink->process->child_flow_id);
            $this->setFirstProcessAuditor($child_entry, $child_flowlink);
            $child_entry->updateParentChildId($child_entry->process_id);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取可以通过的FlowLink
     *
     * @param $process_id
     * @param $entry_id
     *
     * @return Flowlink|null
     * @throws \Exception
     * @author hurs
     */
    private function getPassFlowLink($entry_id, $process_id)
    {

        $flowlinks = Flowlink::getConditionLink($process_id);
        if (count($flowlinks) <= 1) {
            return $flowlinks[0] ?? null;
        }
        //取条件的值
        $process_vars = ProcessVar::getByProcessId($process_id);
        foreach ($process_vars as $process_var) {
            $expression_filed = $process_var->expression_field;
            //当前步骤判断的变量 需要根据 $var->expression_field（如请假 day） 去查当前工作流提交的表单数据 里的值
            // 将表单数据转化为php变量,供公式计算
            $$expression_filed = EntryData::getFieldValue($entry_id, $process_var->expression_field);
        }
        $flowlink = null;
        foreach ($flowlinks as $v) {
            if ($v->expression === '') {
                throw new DevFixException('未设置流转条件，无法流转，请联系流程设置人员', 1);
            }
            $expression = $v->expression;
            $pass       = false; // 初始化变量
            preg_match_all('/(?<=[\s\S][=<>!][=\s])[\s]*[^\s)]+/u', $expression, $match);
            foreach ($match[0] ?? [] as $m) {
                if (!is_numeric($m)) {
                    $expression = str_replace($m, '"' . trim($m) . '"', $expression);
                }
            }
            try {
                $pass = $expression;
                //eval('$pass=(' . $expression . ');');
            } catch (Exception $e) {
                throw new DevFixException($e->getMessage() . ":" . $expression, $e->getCode(), $e);
            }
            if ($pass) {
                $flowlink = $v;
                break;
            }
        }

        return $flowlink;
    }

    /**
     * 根据审批人来判断前面是否已经审批过
     *
     * @param Entry $entry
     *
     * @return bool
     * @author hurs
     */
    private function checkIsRepeatProcExam(Entry $entry)
    {
        $exam_user_ids = []; // 审批人id列表
        $circle        = null;
        foreach ($entry->procs as $proc) {
            if ($proc->status == Proc::STATUS_IN_HAND) {
                // 取当前审批的人员信息
                $exam_user_ids[] = $proc->user_id;
                $circle          = $proc->circle;
            }
        }

        sort($exam_user_ids);
        $exam_user_ids_str = implode(',', $exam_user_ids); // 当前节点的审批人

        // 读取之前节点的所有审批人
        $procs = Proc::select(DB::raw("process_id,concurrence,circle,GROUP_CONCAT(user_id) user_ids"))
            ->where(['circle' => $circle])// 限定同一个审批单,过滤掉重新发送的数据
            ->where(['entry_id' => $entry->id, 'flow_id' => $entry->flow_id, 'status' => Proc::STATUS_PASSED])
            ->groupBy('process_id', 'concurrence', 'circle')
            ->orderBy('user_id', 'ASC')
            ->get();

        foreach ($procs as $proc) {
            if ($proc->user_ids == $exam_user_ids_str) {
                return $proc->process_id;
            }
        }

        return false;
    }

    /**
     * [goToProcess 前往固定流程步骤]
     *
     * @param Entry $entry
     * @param       $process_id
     *
     * @throws \Exception
     * @author hurs
     */
    protected function goToProcess(Entry $entry, $process_id)
    {
        /******************** 生成proc begin *******************/
        $auditor_ids = $this->getProcessAuditorIds($entry, $process_id); // 审批人

        if ($auditor_ids == [0]) {
            $auditors = collect([User::noBody()]);
        } else {
            $auditors = User::whereIn('id', $auditor_ids)->get(); // 获取审批人信息
        }
        if ($auditors->count() < 1) {
            throw new DevFixException("下一步骤未找到审核人", 1);
        }

        $time = time();
        foreach ($auditors as $v) {
            // 生成proc
            Proc::create([
                'entry_id'     => $entry->id,
                'flow_id'      => $entry->flow_id,
                'process_id'   => $process_id,
                'process_name' => Process::find($process_id)->process_name,
                'user_id'      => $v->id,
                'user_name'    => $v->chinese_name,
                'dept_name'    => '', //$v->dept->dept_name,
                'circle'       => $entry->circle,
                'status'       => Proc::STATUS_IN_HAND,
                'is_read'      => Proc::IS_READ_NO,
                'concurrence'  => $time,
            ]);
        }
        /******************** 生成proc end *******************/

        /******************* 生成代理审批proc begin *****************/
        // 获取当前有效的代理人
        $agents = AuthorizeAgent::getValidAgents($auditor_ids, $entry->flow->flow_no);
        foreach ($agents as $agent) {
            // 生成代理proc
            Proc::create([
                'entry_id'         => $entry->id,
                'flow_id'          => $entry->flow_id,
                'process_id'       => $process_id,
                'process_name'     => Process::find($process_id)->process_name,
                'user_id'          => $agent->agent_user_id,
                'user_name'        => $agent->agent_user_name,
                'dept_name'        => '', //$v->dept->dept_name,
                'circle'           => $entry->circle,
                'status'           => Proc::STATUS_IN_HAND,
                'is_read'          => Proc::IS_READ_NO,
                'concurrence'      => $time,
                'authorizer_ids'   => $agent->authorizer_user_id,
                'authorizer_names' => $agent->authorizer_user_name,
            ]);
        }

        /******************* 生成代理审批proc end *****************/
    }

    /**
     * 获取申请人的对应层级部门领导
     *
     * @param $applyer_id
     * @param $depart_lvl
     *
     * @return array
     * @throws \Exception
     */
    public static function getApplyerDepartHead($applyer_id, $depart_lvl)
    {
        $dept_lvl_leaders = WorkflowUserService::fetchUserDepartmentDeepInfo($applyer_id, $depart_lvl);
        if (empty($dept_lvl_leaders)) {
            throw new DevFixException(sprintf('未查询到指定部门负责人,applyerId:%s,deptLvl:%s', $applyer_id, $depart_lvl));
        }

        $leaders_ids = [];
        foreach ($dept_lvl_leaders as $dept_lvl_leader) {
            $leaders_ids[] = $dept_lvl_leader['user_id'];
        }

        $leaders_ids = array_unique($leaders_ids);

        return $leaders_ids;
    }

    public static function getApplyerSuperLeader($applyer_id, $depart_lvl)
    {
        $dept_lvl_leaders = WorkflowUserService::fetchLeaderInfo($applyer_id, $depart_lvl);
        if (empty($dept_lvl_leaders)) {
            throw new DevFixException(sprintf('未查询到指定部门负责人,applyerId:%s,deptLvl:%s', $applyer_id, $depart_lvl));
        }

        $leaders_ids = [];
        foreach ($dept_lvl_leaders as $dept_lvl_leader) {
            $leaders_ids[] = $dept_lvl_leader['user_id'];
        }

        $leaders_ids = array_unique($leaders_ids);

        return $leaders_ids;
    }

    public static function getApplyerReportHead($applyer_id, $report_lvl)
    {
        $report_lvl_leaders = WorkflowUserService::fetchUserReportRelationShip($applyer_id, $report_lvl);
        if (empty($report_lvl_leaders)) {
            throw new DevFixException(sprintf('未查询到指定汇报人,applyerId:%s,reportLvl:%s', $applyer_id, $report_lvl));
        }

        return $report_lvl_leaders;
    }

    /**
     * 获取申请人所在部门的层级,以及是否是领导
     *
     * @param $apply_id
     *
     * @return array
     * @throws \Exception
     */
    public static function getApplyerDepartLvlInfo($apply_id)
    {
        $user_dept_info = WorkflowUserService::fetchUserMainDepartmentInfo($apply_id);

        if (empty($user_dept_info)) {
            throw new DevFixException(sprintf('未查询到申请人的部门信息,applyerId:%s', $apply_id));
        }

        return [
            $user_dept_info['level'],
            $user_dept_info['is_leader'],
            $user_dept_info['id_card_no'], // 身份证号
            $user_dept_info['payee_bank_branch'], // 分行名称
            $user_dept_info['bank_card_no'] // 银行卡号
        ];
    }


    public static function generateHtml(Template $template, Entry $entry = null, $mapCompanyType = null, $userId = null)
    {
        return view(
            'workflow.template.tpl',
            [
                'template_forms'   => $template->template_form,
                'entry'            => $entry,
                'entry_data'       => $entry ? $entry->entry_data : null,
                'apply_basic_info' => self::getApplyerBasicInfo($entry, $mapCompanyType),
                'key_info'         => self::getApplerKeyInfo(),
                'user_id'          => $userId,
            ]
        )->render();
    }

    public static function generateHtmlForApi(Template $template, Entry $entry = null, $mapCompanyType = null, $userId = null)
    {
        return $template->template_form;
    }

    /**
     * @param Process[] $processes
     * @param Entry|null $entry
     *
     * @return string
     * @author hurs
     */
    public static function generateProcessHtml($processes, Entry $entry = null)
    {
        foreach ($processes as $key => $process) {
            if ($key && !$process->auditor_ids) {
                $entry_id  = $entry->id ?? 0;
                $k         = $key + 1;
                $exception = new HurryHandleException("流程ID: $entry_id 的第 $k 个节点没有审批人，请尽快核实");
                $exception->setUrl(route('workflow.entry.manager.show', ['id' => $entry_id, 'userId' => $entry->user_id ?? 0]));
                report($exception);
            }
        }
        return view(
            'workflow.template.process',
            [
                'processes' => $processes,
                'entry'     => $entry,
            ]
        )->render();
    }

    public static function generateProcessHtmlMap($processes, Entry $entry = null, $companyType = null)
    {
        $existProcesses = [];
        collect($processes)->each(function ($item, $key) use (&$existProcesses) {
            $existProcesses[$key] = $item['map_names'];
        });

        $view = 'qnn' == $companyType ? 'finance.flow.process' : 'workflow.template.process';
        return view(
            $view,
            [
                'processes'      => $processes,
                'entry'          => $entry,
                'companyType'    => $companyType,
                'existProcesses' => $existProcesses,
            ]
        )->render();
    }

    public static function generateHtmlShow(Template $template, Entry $entry = null)
    {
        return view(
            'workflow.template.tpl_show',
            [
                'template_forms' => $template->template_form,
                'entry_data'     => $entry->entry_data,
                'entry'          => $entry,
            ]
        )->render();
    }

    /**
     * @param string $flow_no 流程编号
     * @param string|Carbon $begin_at 筛选开始时间
     * @param string|Carbon $end_at 筛选结束时间
     * @param int $status 状态
     * @param int $entry_id 审批单id
     * @param int $user_id 审批归属
     *
     * @return array
     * @author hurs
     */
    public static function getFlowUserDataV1($flow_no, $begin_at = null, $end_at = null, $status = null, $entry_id = 0, $user_id = 0)
    {
        $flow = Flow::findAllByFlowNo($flow_no);
        if ($flow) {
            $flow_ids = $flow->pluck('id');
        } else {
            $flow_ids = [];
        }
        $templates  = $flow->pluck('template', 'id');
        $entry_data = EntryData::getFlowData($flow_ids, $begin_at, $end_at, $status, $entry_id, $user_id);
        $new_data   = [];
        foreach ($entry_data as $data) {
            $template_form = $templates[$data->flow_id]->template_form->where('field', $data->field_name)->first();

            $new_data[$data->entry_id]['form_data'][$data->field_name]['value'] = $data->field_value;
            $new_data[$data->entry_id]['form_data'][$data->field_name]['name']  = $template_form->field_name ?? null;
            if (!$new_data[$data->entry_id]['form_data'][$data->field_name]['name'] &&
                !$new_data[$data->entry_id]['form_data'][$data->field_name]['value']
            ) {
                unset($new_data[$data->entry_id]['form_data'][$data->field_name]);
            }
            $new_data[$data->entry_id]['entry']  = $data->entry->toArray();
            $new_data[$data->entry_id]['status'] = $data->entry->getStatusDesc();
        }
        foreach ($new_data as $entry_id => &$data) {
            $flow_proc    = Proc::getEntryProcsForPublic($entry_id)->toArray();
            $data['proc'] = $flow_proc;
        }

        return $new_data;
    }

    /**
     * @param $flow_no
     * @param null $begin_at
     * @param null $end_at
     * @param null $status
     * @param int $entry_id
     * @param int $user_id
     * @return array
     */
    public static function getFlowUserDataV2($flow_no, $begin_at = null, $end_at = null, $status = null, $entry_id = 0, $user_id = 0)
    {
        $flow = Flow::findAllByFlowNo($flow_no);
        if ($flow) {
            $flow_ids = $flow->pluck('id');
        } else {
            $flow_ids = [];
        }
        $templates  = $flow->pluck('template', 'id');
        $entry_data = EntryData::getFlowDataV2($flow_ids, $begin_at, $end_at, $status, $entry_id, $user_id);
        $new_data   = [];
        foreach ($entry_data as $data) {
            $template_form = $templates[$data->flow_id]->template_form->where('field', $data->field_name)->first();

            $new_data[$data->entry_id]['form_data'][$data->field_name]['value'] = $data->field_value;
            $new_data[$data->entry_id]['form_data'][$data->field_name]['name']  = $template_form->field_name ?? null;
            if (!$new_data[$data->entry_id]['form_data'][$data->field_name]['name'] &&
                !$new_data[$data->entry_id]['form_data'][$data->field_name]['value']
            ) {
                unset($new_data[$data->entry_id]['form_data'][$data->field_name]);
            }
            $new_data[$data->entry_id]['entry']  = $data->entry->toArray();
            $new_data[$data->entry_id]['status'] = $data->entry->getStatusDesc();
        }
        foreach ($new_data as $entry_id => &$data) {
            $flow_proc    = Proc::getEntryProcsForPublic($entry_id)->toArray();
            $data['proc'] = $flow_proc;
        }

        return $new_data;
    }

    /**
     * 获取申请人(即当前登录人员)的一些系统数据
     *
     * @return array
     */
    public static function getApplyerBasicInfo(Entry $entry = null, $mapCompanyType = null)
    {
        if (!empty($entry) && !$entry->isDraft()) {
            // 已提交的申请单
            $applyAt   = date('Y-m-d', strtotime($entry->created_at));
            $form_data = $entry->entryFormData(true);
        } else {
            list($dept_lvl, $is_leader, $id_card_no, $bank_name, $bank_num) = static::getApplyerDepartLvlInfo(Auth::id()); // 申请人信息
            $apply_basic_info = [
                'applyer_dept_lvl'  => $dept_lvl,
                // 所属部门层级
                'is_leader'         => $is_leader,
                //
                'primary_dept_path' => WorkflowUserService::fetchUserPrimaryDeptPath(Auth::id()),
//                //获取申请人主部门、主部门所在的中心,部门取全路径
            ];
            $path             = explode('/', $apply_basic_info['primary_dept_path']);
            array_shift($path);
            $apply_basic_info['primary_dept']           = join('/', $path);
            $apply_basic_info['company']                = Auth::user()->company->name;
            $apply_basic_info['applicant_chinese_name'] = Auth::user()->chinese_name;
            $apply_basic_info['applicant_position']     = Auth::user()->position;
            $apply_basic_info['applicant_employee_num'] = Auth::user()->employee_num;
            $apply_basic_info['applicant_join_at']      = Auth::user()->join_at;
        }
        $apply_basic_info['company_name_list'] = ContractService::getCompanyNameList();
        return $apply_basic_info;
    }

    public static function getApplerKeyInfo()
    {
        return [
            'applyer_dept_lvl'       => [
                'is_hidden' => true,
                'name'      => '部门层级',
            ], // 所属部门层级
            'is_leader'              => [
                'is_hidden' => true,
                'name'      => '是否领导',
            ], // 是否是领导
            'primary_dept_path'      => [
                'is_hidden' => false,
                'name'      => '部门全路径',
            ], // 成本中心
            'primary_dept'           => [
                'is_hidden' => false,
                'name'      => '部门',
            ],
            'company'                => [
                'is_hidden' => false,
                'name'      => '公司',
            ],
            'applicant_chinese_name' => [
                'is_hidden' => false,
                'name'      => '申请人姓名',
            ],
            'applicant_employee_num' => [
                'is_hidden' => false,
                'name'      => '申请人工号',
            ],
            'applicant_position'     => [
                'is_hidden' => false,
                'name'      => '申请人职位',
            ],
            'applicant_join_at'      => [
                'is_hidden' => false,
                'name'      => '申请人入职时间',
            ],
            'company_name_list'      => [
                'is_hidden' => false,
                'name'      => '公司列表',
            ],
        ];
    }

    /**
     * 克隆流程,生成新版本
     *
     * @param $old_flow_id
     *
     * @return array
     * @throws \Exception
     */
    public static function cloneFlowNewVersion($old_flow_id)
    {
        $oldFlow = Flow::findOrFail($old_flow_id);
        try {
            DB::beginTransaction();
            $newFlow             = Flow::cloneFlow($oldFlow->id);
            $oldFlow->is_abandon = Flow::ABANDON_YES; // 标记为废弃
            $oldFlow->save();
            DB::commit();

            return ['new' => $newFlow, 'old' => $oldFlow];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 记录错误日志
     *
     * @param $type
     * @param $msg
     */
    public static function errLog($type, $msg)
    {
        Log::error(sprintf('[%s] %s', $type, $msg));
    }

    /**
     * 获取员工已完成或进行中的请假流程数据
     *
     * @param $userId
     * @return array
     */
    public static function fetchUserWorkflowData($userId)
    {
        $entries = EntryData::leftJoin('workflow_entries AS we', 'workflow_entry_data.entry_id', 'we.id')
            ->where('we.user_id', $userId)
            ->whereIn('we.status', [Entry::STATUS_IN_HAND, Entry::STATUS_FINISHED])
            ->whereIn('we.flow_id', function ($query) {
                $query->select('id')
                    ->from(with(new Flow())->getTable())
                    ->where('flow_no', Entry::WORK_FLOW_NO_HOLIDAY)
                    ->whereIn('field_name', ['date_begin', 'date_end', 'holiday_type']);
            })
            ->get(['entry_id', 'field_name', 'field_value'])->toArray();

        return $entries;
    }

    public static function fetchUserEntryByFlowNo($userId, $flowNo = [Entry::WORK_FLOW_NO_ACTIVE_LEAVE_APPlY], $status = [Entry::STATUS_IN_HAND, Entry::STATUS_FINISHED])
    {
        return EntryData::leftJoin('workflow_entries AS we', 'workflow_entry_data.entry_id', 'we.id')
            ->where('we.user_id', $userId)
            ->whereIn('we.status', $status)
            ->whereIn('we.flow_id', function ($query) use ($flowNo) {
                $query->select('id')
                    ->from(with(new Flow())->getTable())
                    ->where('flow_no', $flowNo);
            })->count();
    }

    public static function fetchPositiveFlowNo($userId, $flowNo = [Entry::WORK_FLOW_NO_POSITIVE_APPlY], $status = [Entry::STATUS_IN_HAND, Entry::STATUS_FINISHED])
    {
        return EntryData::leftJoin('workflow_entries AS we', 'workflow_entry_data.entry_id', 'we.id')
            ->where('we.user_id', $userId)
            ->whereIn('we.status', $status)
            ->whereIn('we.flow_id', function ($query) use ($flowNo) {
                $query->select('id')
                    ->from(with(new Flow())->getTable())
                    ->where('flow_no', $flowNo);
            })->get()->toArray();
    }

    public static function fetchPositiveWageFlowNo($userId, $flowNo = [Entry::WORK_FLOW_NO_POSITIVE_WAGE_APPlY], $status = [Entry::STATUS_IN_HAND, Entry::STATUS_FINISHED])
    {
        return EntryData::leftJoin('workflow_entries AS we', 'workflow_entry_data.entry_id', 'we.id')
            ->where('we.user_id', $userId)
            ->whereIn('we.status', $status)
            ->whereIn('we.flow_id', function ($query) use ($flowNo) {
                $query->select('id')
                    ->from(with(new Flow())->getTable())
                    ->where('flow_no', $flowNo);
            })->get()->toArray();
    }

    /**
     * 被销掉的请假流程编号
     *
     * @param $userId
     * @return array
     */
    public static function fetchUserResumedWorkflowData($userId)
    {
        $entries = EntryData::leftJoin('workflow_entries AS we', 'workflow_entry_data.entry_id', 'we.id')
            ->where('we.user_id', $userId)
            ->where('we.status', Entry::STATUS_FINISHED)
            ->whereIn('we.flow_id', function ($query) {
                $query->select('id')
                    ->from(with(new Flow())->getTable())
                    ->where('flow_no', Entry::WORK_FLOW_NO_ATTENDANCE_RESUMPTION)
                    ->where('field_name', 'resumption_leave_list');  //被销掉的请假流程id
            })
            ->distinct()
            ->get(['field_value']);

        $result = [];

        foreach ($entries as $entry) {
            array_push($result, $entry->field_value);
        }

        return $result;
    }

    /**
     * 根据flow_no返回ID
     * @param $flow_no
     * @return int id
     */
    public static function getWorkFlowIdByFlowNo($flow_no)
    {
        return self::query()
            ->where('flow_no', $flow_no)
            ->pluck('id')
            ->first();
    }
}

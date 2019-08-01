<?php

namespace App\Repositories;

use App\Models\DepartUser;
use App\Models\Message\Message;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\WorkflowUserSync;
use Mockery\Exception;
use App\Models\Workflow\Entry;
use App\Models\Workflow\EntryData;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\FlowType;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Services\AuthUserShadowService;
use App\Services\WorkflowUserService;
use Illuminate\Http\Request;
use App\Models\Workflow\Flow;
use Illuminate\Support\Carbon;
use Auth;
use Hash;
use DB;
use App\Constant\ConstFile;
use App\Models\User;

class PositiveRepository extends ParentRepository
{
    public function model()
    {
        return Entry::class;
    }

    public function createWorkflow(Request $request)
    {
        try {
            //判断是否是个人
            $id = $request->get("user_id");
            if (empty($id)) {
                $ids = Auth::id();
            } else {
                $ids = $id;
            }
            //基础信息
            $list = $this->UserInfos($ids);
            $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
            $flow_id = $request->get('flow_id', 0);
            $flow_id = (int)$flow_id;
            if ($flow_id < 0) {
                throw new Exception(sprintf('无效的流程ID:%s', $flow_id));
            }

            if (!$canSeeFlowIds->contains($flow_id)) {
                throw new Exception('当前流程不可用');
            }

            $flow = Flow::publish()->findOrFail($flow_id);
            $user_id = Auth::id();
            Workflow::generateHtml($flow->template, null, null, $user_id);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();

            $this->data = [
                'user' => $list['list'],//员工基本资料
                'flow' => $flow->template->toArray(),
                'user_id' => $user_id,
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeWorkflow(Request $request)
    {//转正
        return $this->updateFlow($request, 0);
    }

    public function storeWageWorkflow(Request $request)
    {//工资包
        return $this->updateWageFlow($request, 0);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function workflowShow(Request $request)
    {
        try {
            //判断是否是个人
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $list = $this->UserInfos($authAuditor->id());
            $entry = Entry::findUserEntry($authAuditor->id(), $id);

            $templateForm = $this->fetchEntryTemplate($entry)->template_form;
            $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            $this->data = [
                "user" => $list['list'],
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry)
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFlow(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            $entry = $this->updateOrCreateEntry($request, $id); // 创建或更新申请单
            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            $entry->save();
            DB::commit();

            $this->data = ['entry' => $entry->toArray()];
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            Workflow::errLog('positive PositiveStore', $e->getMessage() . $e->getTraceAsString());
        }
        return $this->returnApiJson();
    }

    public function updateWageFlow(Request $request, $id)
    {//工资包
        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            $entry = $this->updateWageOrCreateEntry($request, $id); // 创建或更新申请单
            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            $entry->save();
            DB::commit();

            $this->data = ['entry' => $entry->toArray()];
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            Workflow::errLog('positive PositiveStore', $e->getMessage() . $e->getTraceAsString());
        }
        return $this->returnApiJson();
    }


    /**
     * 更新或插入申请单
     *
     * @param Request $request
     * @param         $id
     *
     * @return Entry
     * @author hurs
     */
    private function updateOrCreateEntry(Request $request, $id = 0)
    {
        $list = $request->all();
        $list['file_source_type'] = 'workflow';
        $list['file_source'] = 'positive_apply';
        $list['is_draft'] = null;
        $list['entry_id'] = null;
        if (!$id) {
            $authApplyer = new AuthUserShadowService(); // 以影子用户作为申请人
            //是否离职
            $info = WorkflowUserSync::where('user_id', $authApplyer->id())->first(['status']);
            if (!empty($info)) {
                if ($info->status == ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE || $info->status == ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_UNDER_HAND_OVER || $info->status == ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE_UNDER_CONFIRM_LEAVE) {
                    throw new Exception('当前转正流程无法使用', -1);
                }
            }
            $flow = Flow::findOrFail($list['flow_id']);
            $entry = Entry::create([
                'title' => $list['title'],
                'flow_id' => $list['flow_id'],
                'user_id' => $authApplyer->id(),
                'circle' => 1,
                'status' => Entry::STATUS_IN_HAND,
                'origin_auth_id' => Auth::id(),
                'origin_auth_name' => Auth::user()->name,
                'order_no' => Entry::generateOrderNo($flow->flow_no),
            ]);
        } else {
            $entry = Entry::findOrFail($id);
            $entry->checkEntryCanUpdate(); // 校验申请单是否可以修改
            $entry->update($list);
        }

        if (!empty($list['is_draft'])) {
            $entry->status = Entry::STATUS_DRAFT;
        } else {
            $entry->status = Entry::STATUS_IN_HAND;
        }
        $this->updateTpl($entry, $list['tpl'] ?? []);
        return $entry;
    }

    private function updateWageOrCreateEntry(Request $request, $id = 0)
    {//工资包
        $list = $request->all();

        $list['file_source_type'] = 'workflow';
        $list['file_source'] = 'positive_wage_apply';
        $list['is_draft'] = null;
        $list['entry_id'] = null;
        if (!$id) {
            $authApplyer = new AuthUserShadowService(); // 以影子用户作为申请人
            //是否离职
            $info = WorkflowUserSync::where('user_id', $authApplyer->id())->first(['status']);
            if (!empty($info)) {
                if ($info->status == ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE ||
                    $info->status == ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_UNDER_HAND_OVER ||
                    $info->status == ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE_UNDER_CONFIRM_LEAVE) {
                    throw new Exception('当前转正流程无法使用', -1);
                }
            }
            $flow = Flow::findOrFail($list['flow_id']);
            $entry = Entry::create([
                'title' => $list['title'],
                'flow_id' => $list['flow_id'],
                'user_id' => $authApplyer->id(),
                'circle' => 1,
                'status' => Entry::STATUS_IN_HAND,
                'origin_auth_id' => Auth::id(),
                'origin_auth_name' => Auth::user()->name,
                'order_no' => Entry::generateOrderNo($flow->flow_no),
            ]);
        } else {
            $entry = Entry::findOrFail($id);
            $entry->checkEntryCanUpdate(); // 校验申请单是否可以修改
            $entry->update($list);
        }
        if (!empty($list['is_draft'])) {
            $entry->status = Entry::STATUS_DRAFT;
        } else {
            $entry->status = Entry::STATUS_IN_HAND;
        }
        $aa = $this->updateTpl($entry, $list['tpl'] ?? []);
        return $entry;
    }

    private function updateTpl(Entry $entry, $tpl = [])
    {
        foreach ($tpl as $k => $v) {
            $val = is_array($v) ? json_encode($v) : $v;
            $val = $val === null ? '' : $val;
            EntryData::updateOrCreate(['entry_id' => $entry->id, 'field_name' => $k], [
                'flow_id' => $entry->flow_id,
                'field_value' => $val,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }
    }


    /**
     * 待转正人员申请单表单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show(Request $request)
    {
        try {
            //判断是否是个人
            $id = $request->get("user_id");
            if (empty($id)) {
                $ids = Auth::id();
            } else {
                $ids = $id;
            }
            //基础信息
            $list = $this->UserInfos($ids);
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_POSITIVE_APPlY);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);

            $this->data = [
                "user" => $list['list'],
                'flow_id' => $flow->id,
                'title' => $flow->template->template_name,
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }

        return $this->returnApiJson();
    }

    /**
     * 工资包申请单表单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function wageShowApply(Request $request)
    {
        try {
            //判断是否是个人
            $id = $request->get("user_id");
            if (empty($id)) {
                $this->message = "员工user_id参数为空";
                $this->code = "-1";
                return $this->returnApiJson();
            }
            //基础信息
            $list = $this->UserInfos($id);
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_POSITIVE_WAGE_APPlY);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
                "user" => $list['list'],
                'positivePlease' => isset($list['positivePlease']) ? $list['positivePlease'] : "",
                'arr' => isset($list['arr']) ? $list['arr'] : "",
                'flow_id' => $flow->id,
                'title' => $flow->template->template_name,
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }

        return $this->returnApiJson();
    }


    /**
     * @deprecated 审批人视角
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Exception
     */
    public function workflowAuthorityShow(Request $request)
    {
        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $process = Proc::findUserProcAllStatus($authAuditor->id(), $id);
            $entry = Entry::findOrFail($process->entry_id);
            $templateForm = $this->fetchEntryTemplate($entry)->template_form;
            $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            $list = $this->UserInfos($entry->user_id);
            $this->data = [
                'user' => isset($list['list']) ? $list['list'] : "",//员工基本资料/
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry),
                'proc' => $process->toArray()
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function passWorkflow(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                (new Workflow())->passWithNotify($request->get('id'));
            });
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function rejectWorkflow(Request $request)
    {
        $procsId = $request->get('id');
        try {
            DB::transaction(function () use ($request, $procsId) {
                (new Workflow())->reject($procsId, $request->input('content', ''));
            });
            /**************审批驳回， 发送通知******************/
            Message::addProc(Proc::find($procsId), Message::MESSAGE_TYPE_WORKFLOW_REJECT);

        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    private function fetchEntryTemplate(Entry $entry)
    {
        return $entry->pid > 0 ? $entry->parent_entry->flow->template : $entry->flow->template;
    }

    private function fetchEntryProcess(Entry $entry)
    {
        $processes = (new Workflow())->getProcs($entry);

        if (empty($processes)) {
            throw new Exception('流程没有配置审批节点');
        }
        $processAuditors = $temp = [];
        foreach ($processes as $process) {
            $temp['process_name'] = $process->process_name;
            $temp['auditor_name'] = '';
            $temp['approval_content'] = $process->proc ? $process->proc->content : '';

            if ($process->proc && $process->proc->auditor_name) {
                $temp['auditor_name'] = $process->proc->auditor_name;
            } elseif ($process->proc && $process->proc->user_name) {
                $temp['auditor_name'] = $process->proc->user_name;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            $temp['status'] = $process->proc ? $process->proc->status : '';
            $temp['status_name'] = '';

            if ($process->proc && $process->proc->status == Proc::STATUS_REJECTED) {
                $temp['status_name'] = '驳回';
            } elseif ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                $temp['status_name'] = '完成';
            } else {
                $temp['status_name'] = '待处理';
            }
            $processAuditors[] = $temp;
        }

        return $processAuditors;
    }

    private function fetchShowData($entry, $templateForm)
    {
        $entry = collect($entry->toArray());
        $templateForm = collect($templateForm);
        if ($entry->isEmpty() || $templateForm->isEmpty()) {
            return [];
        }

        $result = [];
        $templateForm->each(function ($item, $key) use ($entry, &$result) {
            $temp = [];
            $collect = $entry->where('field_name', $item->field);
            if (!in_array($item->field, ['div', 'email', 'password']) && $collect->isNotEmpty()) {
                $temp['title'] = $item['field_name'];
                $temp['value'] = ($collect->first())['field_value'];
                $result[] = $temp;
            }
        });
        return $result;
    }

    /**
     * @deprecated 员工基本信息
     * @param $id
     * @return array
     */
    public function UserInfos($id)
    {

        $list = [];
        //用户信息
        $user = UsersDetailInfo::with(
            'user',
            'user.primaryDepartUser',
            'user.primaryDepartUser.department',
            'user.contract',
            'user.describe'
        )->where('user_id', '=', $id)->first();

        if (!empty($user)) {
            $userInfo = $user->toArray();
            //资料输出
            if (!empty($userInfo['user'])) {
                $list[] = ['name' => '入职员工姓名', 'value' => $userInfo['user']['chinese_name']];
                if (!empty($userInfo['user']['primary_depart_user'])) {
                    $list[] = [
                        'name' => '用人部门',
                        'value' => $userInfo['user']['primary_depart_user']['department']['name']
                    ];
                }
                $list[] = ['name' => '职位', 'value' => $userInfo['user']['position']];
                $list[] = ['name' => '工号', 'value' => $userInfo['user']['employee_num']];
                $userStatus = $this->judge($userInfo['user_status'], 'user_status');
                $list[] = ['name' => '员工类型', 'value' => $userStatus];
                $list[] = ['name' => '入职日期', 'value' => $userInfo['user']['join_at']];

                if (!empty($userInfo['user']['contract'])) {
                    $probation = $this->judge($userInfo['user']['contract'][0]['probation'], 'probation');
                    $contract = $this->judge($userInfo['user']['contract'][0]['contract'], 'contract');
                    $list[] = ['name' => '试用期', 'value' => $probation];
                    $list[] = ['name' => '合同期限', 'value' => $contract];
                } else {
                    $list[] = ['name' => '试用期', 'value' => '无'];
                    $list[] = ['name' => '合同期限', 'value' => '无'];
                }
                $arr = [];
                if (!empty($userInfo['user']['describe'])) {
                    $positivePlease = $userInfo['user']['describe']['positive_please'];
                    $arr['salary_scale'] = $userInfo['user']['describe']['salary_scale'];
                    $arr['points_scale'] = $userInfo['user']['describe']['points_scale'];
                    return ['list' => $list, 'positivePlease' => $positivePlease, 'arr' => $arr];
                }
            }
        }


        return ['list' =>$list];
    }

    /**
     * @deprecated 判断
     * @param $status
     * @param $type
     * @return mixed
     */
    public function judge($status, $type)
    {
        $userStatus = "";
        switch ($type) {
            case 'user_status':
                if ($status == ConstFile::STAFF_TYPE_FULL_TIME) {
                    $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_FULL_TIME];
                } elseif ($status == ConstFile::STAFF_TYPE_PART_TIME) {
                    $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_PART_TIME];
                } elseif ($status == ConstFile::STAFF_TYPE_LABOR) {
                    $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_LABOR];
                } elseif ($status == ConstFile::STAFF_TYPE_OUT_SOURCE) {
                    $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_OUT_SOURCE];
                } elseif ($status == ConstFile::STAFF_TYPE_REHIRE) {
                    $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_REHIRE];
                }
                break;
            case "probation":
                if ($status == ConstFile::CONTRACT_PROBATION_ONE) {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PROBATION_ONE];
                } elseif ($status == ConstFile::CONTRACT_PROBATION_TWO) {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PROBATION_TWO];
                } elseif ($status == ConstFile::CONTRACT_PROBATION_THR) {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PROBATION_THR];
                }
                break;
            case "contract":
                if ($status == ConstFile::CONTRACT_PERIOD_ONE) {
                    $userStatus = ConstFile::$contract[ConstFile::CONTRACT_PERIOD_ONE];
                } elseif ($status == ConstFile::CONTRACT_PERIOD_THR) {
                    $userStatus = ConstFile::$contract[ConstFile::CONTRACT_PERIOD_THR];
                } elseif ($status == ConstFile::CONTRACT_PERIOD_FIV) {
                    $userStatus = ConstFile::$contract[ConstFile::CONTRACT_PERIOD_FIV];
                }
                break;
        }
        return $userStatus;
    }


    /**
     * @deprecated 工资包
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function wageShow(Request $request)
    {
        try {
            $id = $request->get('user_id');
            if (empty($id)) {
                $this->message = "请查看员工user_id参数是否为空";
                $this->code = "-1";
                return $this->returnApiJson();
            }
            $list = $this->UserInfos($id);
            $array = [
                [
                    'wage_classes' => '创业工资包',
                    'select' => false,
                    "data1" => [
                        ['select' => false, "name" => "20%", 'salary_scale' => '0.2'],
                        ['select' => false, "name" => "30%", 'salary_scale' => '0.3'],
                        ['select' => false, "name" => "40%", 'salary_scale' => '0.4']
                    ],
                    "data2" => [
                        ['select' => false, "name" => "100%", 'points_scale' => '1'],
                        ['select' => false, "name" => "90%", 'points_scale' => '0.9'],
                        ['select' => false, "name" => "80%", 'points_scale' => '0.8']
                    ]
                ],
                [
                    'wage_classes' => '发展工资包',
                    'select' => false,
                    "data1" => [
                        ['select' => false, "name" => "70%", 'salary_scale' => '0.7'],
                        ['select' => false, "name" => "80%", 'salary_scale' => '0.8'],
                        ['select' => false, "name" => "90%", 'salary_scale' => '0.9']
                    ],
                    "data2" => [
                        ['select' => false, "name" => "20%", 'points_scale' => '0.2'],
                        ['select' => false, "name" => "18%", 'points_scale' => '0.18'],
                        ['select' => false, "name" => "15%", 'points_scale' => '0.15']
                    ]
                ],
                [
                    'wage_classes' => '生活工资包',
                    'select' => false,
                    "data1" => [
                        ['select' => false, "name" => "100%", 'salary_scale' => '1'],
                    ],
                    "data2" => [
                        ['select' => false, "name" => "10%", 'points_scale' => '0.1'],
                    ]
                ],
                [
                    'wage_classes' => '全薪',
                    'select' => false,
                    "data1" => [],
                    "data2" => []
                ]
            ];

            $this->data = [
                'user' => $list['list'],//员工基本资料
                "wage" => $array,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * @deprecated 转正限制重复提交
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function positiveApplyEntry(Request $request)
    {
        $id = $request->get('user_id');
        if (empty($id)) {
            $userId = Auth::id();
        } else {
            $userId = $id;
        }
        try {
            $result = Workflow::fetchPositiveFlowNo($userId);
            $this->data = ['status' => count($result) > 0 ? 1 : 0];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }

        return $this->returnApiJson();
    }

    /**
     * @deprecated 工资包限制重复提交
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function fetchPositiveWageEntry(Request $request)
    {
        $id = $request->get('user_id');
        if (empty($id)) {
            $userId = Auth::id();
        } else {
            $userId = $id;
        }
        try {
            $result = Workflow::fetchPositiveWageFlowNo($userId);
            $this->data = ['status' => count($result) > 0 ? 1 : 0];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }

        return $this->returnApiJson();
    }

}
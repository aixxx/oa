<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Contract\Contract;
use App\Models\Salary\Salary;
use App\Models\Salary\UsersSalary;
use App\Models\Salary\UsersSalaryData;
use App\Models\Salary\UsersSalaryRelation;
use App\Models\TotalAudit\TotalAudit;
use App\Models\User;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\Entry;
use App\Models\Workflow\EntryData;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\FlowType;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Models\Workflow\WorkflowUserSync;
use App\Repositories\Performance\PerformanceTemplateRepository;
use App\Services\AuthUserShadowService;
use App\Services\WorkflowUserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use DB;
use Auth;

class ContractRepository extends ParentRepository
{

    public function model()
    {
        return Contract::class;
    }

    public function fetchToDo()
    {
        try {
            // 获取有审批权限的
            $authAuditor = new AuthUserShadowService();
            //我的申请
            $entries = Entry::getWithProProcess($authAuditor->id(), [Entry::STATUS_IN_HAND, Entry::STATUS_DRAFT]);
            //我的待办
            $process = Proc::getUserProc($authAuditor->id());

            $entriesList = $processList = [];

            if (!empty($entries)) {
                foreach ($entries as $k1 => $e) {
                    $entriesList[$k1]['title'] = $e['title'];
                    $entriesList[$k1]['url'] = route('api.flow.show') . "?id=" . $e['id'];
                }
            }

            if (!empty($process)) {
                foreach ($process as $k2 => $p) {
                    $processList[$k2]['title'] = $p->entry->title;
                    $processList[$k2]['url'] = route('api.auditor_flow.show') . "?id=" . $p['id'];
                }
            }

            $this->data = ['authAuditor' => $authAuditor, 'entries' => $entriesList, 'process' => $processList];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchWorkflowList()
    {
        try {
            $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
            $canSeeFlowIds = $canSeeFlowIds->toArray();
            $types = FlowType::with('publish_flow')->get();

            if (empty($types) || empty($canSeeFlowIds)) {
                return $this->returnApiJson();
            }

            $this->data = $temp = [];
            foreach ($types as $type) {
                foreach ($type->valid_flow as $flow) {
                    if (in_array($flow->id, $canSeeFlowIds)) {
                        $temp['name'] = $flow->flow_name;
                        $temp['url'] = route('api.contract.flow.create', ['flow_id' => $flow->id]);
                        $this->data[] = $temp;
                    }
                }
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function createWorkflow(Request $request)
    {
        try {
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
    public function storeWorkflow(Request $request, $user)
    {
        return $this->updateFlow($request, 0, $user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFlow(Request $request, $id, $user)
    {
        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            $entry = $this->updateOrCreateEntry($request, $id, $user); // 创建或更新申请单
            $contractData = $request->all();
            $contractData['tpl']['entrise_id'] = $entry->id;
            $contract = $this->contractCreate($contractData['tpl'], $user); // 创建或更新申请单

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
            Workflow::errLog('EntryUpdate', $e->getMessage() . $e->getTraceAsString());
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
        try {
            DB::transaction(function () use ($request) {
                (new Workflow())->reject($request->get('id'), $request->input('content', ''));
            });
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function history($flow_id = 0)
    {
        //need page
        $authAuditor = new AuthUserShadowService();
        $uid = $authAuditor->id();


        $query = Entry::query();
        if ($flow_id != 0) {
            $query->where('flow_id', '=', $flow_id);
            $flowObj = Flow::find($flow_id);
            $templateForm = $flowObj->template->template_form_show_in_todo;
        }
        $entries = $query->where('user_id', '=', $uid)->with(['user', 'entry_data'])->get();

        $arr = [];
        foreach ($entries as $entry) {
            if ($flow_id == 0) {
                $flowObj = Flow::find($entry->flow_id);
                if (empty($flowObj)) {
                    continue;
                }
                $templateForm = $flowObj->template->template_form_show_in_todo;
            }
            /** @var Entry $entry */
            $entry->info = $this->dealInfo($entry, $templateForm);
            $this->dealCreatedTime($entry);
            $entry->title = $entry->user->chinese_name . '提交的' . $entry->title;
            $entry->status = Entry::$_status[$entry->status];
            if ($entry->created_at->isCurrentMonth()) {
                $arr['本月'][] = $entry;
            } else {
                $arr['更早以前'][] = $entry;
            }
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,
            ConstFile::API_RESPONSE_SUCCESS, $arr);
    }

    /**
     * @param $entry
     */
    public function dealCreatedTime($entry)
    {
        $entry->created = $entry->created_at->hour . ':' . $entry->created_at->minute;
        if ($entry->created_at->isToday()) {
            $entry->created = $entry->created_at->hour . ':' . $entry->created_at->minute;
        } elseif ($entry->created_at->isYesterday()) {
            $entry->created = '昨天';
        } else {
            $entry->created = date('Y.m.d', strtotime($entry->created_at));
        }
        return $entry->created;
    }

    /**
     * @param $entry
     * @param $templateForm
     * @return array
     */
    public function dealInfo($entry, $templateForm)
    {
        $info = [];
        /** @var Entry $entry */
        $entriesData = $entry->entry_data;

        foreach ($entriesData as $datum) {

            foreach ($templateForm as $form) {
                /** @var EntryData $datum */
                if ($datum->field_name == $form->field) {
                    $info[$form->field_name] = $datum->field_value;
                } else {
                    continue;
                }
            }
        }
        unset($entry->entry_data);
        return $info;
    }

    /**
     * @description 添加入职合同
     * @author liushaobo
     * @time 2019\4\2
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function contractCreate(array $data, $user)
    {
        $error = $this->checkData($data);
        if ($error) {
            throw new Exception(sprintf('请求参数错误：' . $error), ConstFile::API_RESPONSE_FAIL);
        }
        //这里是权限判断，先保留
        DB::transaction(function () use ($data, $user) {
            //创建薪资模板
            $dataArray = $data['salarydata'];
            $userInfo = User::find($data['user_id']);
            $salary = Salary::find($data['template_id']);
            if (!$userInfo) {
                throw new Exception(sprintf('未查到该入职人员'), ConstFile::API_RESPONSE_FAIL);
            }

            if (!$salary) {
                throw new Exception(sprintf('不存id为%s的薪资组', $data['template_id']), ConstFile::API_RESPONSE_FAIL);
            }
            $contractInfo = Contract::where(['user_id' => $data['user_id'], 'company_id' => $userInfo->company_id])->orderBy('id', 'desc')->first();
            if ($contractInfo) {
                $contractInfoDueTime = Carbon::createFromTimeString($contractInfo['entry_at'])->addYear($contractInfo['contract'])->timestamp;
                $contractEntryTime = Carbon::createFromTimeString($data['entry_at'])->timestamp;
                if ($contractEntryTime <= $contractInfoDueTime) {
                    throw new Exception(sprintf('该用户已有一份入职合同'), ConstFile::API_RESPONSE_FAIL);
                }
            }
            if ($data['probation'] == ConstFile::CONTRACT_PROBATION_ONE) {
                $data['state'] = ConstFile::CONTRACT_STATE_TURN_POSIVIVE;
            } else {
                $data['state'] = ConstFile::CONTRACT_STATE_PROBATION_PERIOD;
            }
            $contract = $this->createContractData($data, $user, $salary, $userInfo, $contractInfo);
            if (!$contract) {
                throw new Exception(sprintf('入职合同添加失败'), ConstFile::API_RESPONSE_FAIL);
            }
            $userId = $userInfo['id'];
            $templateId = $data['template_id'];
            $performanceTemplate = app()->make(PerformanceTemplateRepository::class);
            Collect($dataArray)->each(function ($item, $key) use ($userId, $templateId, $contract, $userInfo, $user, $performanceTemplate) {
                //创建薪资和字典的关联
                $salary_relation = UsersSalaryRelation::find($item['relation_id']);
                if (!$salary_relation) {
                    throw new Exception(sprintf('不存id为%s的薪资字典', $item['relation_id']), ConstFile::API_RESPONSE_FAIL);
                }
                $this->createUsersSalary($item, $userId, $templateId, $userInfo, $user, $contract, $salary_relation->status);
                $this->createUsersSalaryData($item, $userId, $templateId, $userInfo, $user, $contract);
                $status = Q($salary_relation, 'status');
                if ($status == Salary::SALARY_RELATION_STATUS_BONUS) {
                    $performanceTemplate->setUpdate($userId, $item['field_data']);
                }
            });
            (new WorkflowUserSync)->where(['user_id' => Q($contract, 'user_id'), 'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_PENDING_CONTRACT])->delete();
            $userSyncData['apply_user_id'] = \Auth::id();
            $userSyncData['user_id'] = Q($contract, 'user_id');
            $userSyncData['status'] = ConstFile::WORKFLOW_USER_SYNC_STATUS_PENDING_CONTRACT;
            $userSyncData['entry_id'] = $contract['entrise_id'];
            $userSyncData['content_json'] = json_encode(['content' => '合同填写完成未审批,进入待签合同状态'], JSON_UNESCAPED_UNICODE);
            (new WorkflowUserSync)->fill($userSyncData)->save();

        });
    }

    /**
     * 待入职人员申请单表单(这时他仍是非系统用户,user表里没有记录)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function showPendingUsersForm(Request $request)
    {
        try {
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_CONTRACT_APPROVAL);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
                'flow_id' => $flow->id,
                'template' => $flow->template->toArray(),
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }

        return $this->returnApiJson();
    }

    /**
     * 审批人视角
     */
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function workflowAuthorityShow(Request $request)
    {

        try {
            $id = $request->get('id');

            $authAuditor = new AuthUserShadowService();
            $process = Proc::findUserProcAllStatus($authAuditor->id(), $id);
            $entry = Entry::findOrFail($process->entry_id);
            $templateForm = $this->fetchEntryTemplate($entry)->template_form;

            $showData = $this->fetchContract($process->entry_id, $templateForm);
            $userId = $showData['user_id'];
            $userInfo = $this->getUserInfo($userId);
            $this->data = [
                'user_info' => $userInfo,
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

    private function fetchEntryTemplate(Entry $entry)
    {
        return $entry->pid > 0 ? $entry->parent_entry->flow->template : $entry->flow->template;
    }

    private function fetchEntryTemplateForLeave()
    {
        return [
            ['field' => 'handover_person', 'field_name' => '交接人', 'field_value' => ''],
            ['field' => 'handover_work', 'field_name' => '交接工作', 'field_value' => ''],
            ['field' => 'handover_finance', 'field_name' => '财务交接事项', 'field_value' => ''],
        ];
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
     * 更新或插入申请单
     * @param Request $request
     * @param int $id
     * @return Entry|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    private function updateOrCreateEntry(Request $request, $id = 0)
    {
        $data = $request->all();

        $data['file_source_type'] = 'workflow';
        $data['file_source'] = 'entry_apply';
        $data['is_draft'] = null;
        $data['entry_id'] = null;

        if (!$id) {
            $authApplyer = new AuthUserShadowService(); // 以影子用户作为申请人
            $entry = Entry::create([
                'title' => $data['title'],
                'flow_id' => $data['flow_id'],
                'user_id' => $authApplyer->id(),
                'circle' => 1,
                'status' => Entry::STATUS_IN_HAND,
                'origin_auth_id' => Auth::id(),
                'origin_auth_name' => Auth::user()->name,
            ]);
        } else {
            $entry = Entry::findOrFail($id);
            $entry->checkEntryCanUpdate(); // 校验申请单是否可以修改
            $entry->update($data);
        }
        if (!empty($data['is_draft'])) {
            $entry->status = Entry::STATUS_DRAFT;
        } else {
            $entry->status = Entry::STATUS_IN_HAND;
        }
        $this->updateTpl($entry, $data['tpl'] ?? []);

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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    public function UserInfos($id)
    {
        $list = [];
        //用户信息
        $user = UsersDetailInfo::with(['user', 'user.primaryDepartUser', 'user.primaryDepartUser.department', 'user.contract'])
            ->where('user_id', '=', $id)->first();
        $userInfo = $user->toArray();
        $prefix = config('employee.employee_num_prefix');
        //资料输出
        if (!empty($userInfo['user'])) {
            $list[] = ['name' => '入职员工姓名', 'value' => $userInfo['user']['chinese_name']];
            if (!empty($userInfo['user']['primary_depart_user'])) {
                $list[] = ['name' => '用人部门', 'value' => $userInfo['user']['primary_depart_user']['department']['name']];
            }
            $list[] = ['name' => '职位', 'value' => $userInfo['user']['position']];
            $list[] = ['name' => '工号', 'value' => $prefix . $userInfo['user']['employee_num']];
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
        }
        return $list;
    }

    public function judge($status, $type)
    {
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
                } else {
                    $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_REHIRE];
                }
                break;
            case "probation":
                if ($status == ConstFile::CONTRACT_PROBATION_ONE) {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PROBATION_ONE];
                } elseif ($status == ConstFile::CONTRACT_PROBATION_TWO) {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PROBATION_TWO];
                } else {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PROBATION_THR];
                }
                break;
            case "contract":
                if ($status == ConstFile::CONTRACT_PERIOD_ONE) {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PERIOD_ONE];
                } elseif ($status == ConstFile::CONTRACT_PERIOD_THR) {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PERIOD_THR];
                } else {
                    $userStatus = ConstFile::$probation[ConstFile::CONTRACT_PERIOD_FIV];
                }
                break;
        }
        return $userStatus;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function workflowShow(Request $request)
    {
        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $entry = Entry::findUserEntry($authAuditor->id(), $id);

            $templateForm = $this->fetchEntryTemplate($entry)->template_form;

            //$showData = $this->fetchShowData($entry->entry_data, $templateForm);
            $showData = $this->fetchContract($id, $templateForm);

            $userId = $showData['user_id'];
            $userInfo = $this->getUserInfo($userId);
            $this->data = [
                'user_info' => $userInfo,
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
     * @param Request $request
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function hrList(Request $request, $user)
    {

        try {
            $workflowUserSync = WorkflowUserSync::with(['hasOneUser' => function ($query) use ($user) {
                $query->select(['*'])->where(['company_id' => $user->company_id]);
            }, 'hasOneUser.primaryDepartUser', 'hasOneUser.primaryDepartUser.department'])->with(['hasOneEntry', 'hasOneEntry.hasOneProcs' => function ($query) use ($user) {
                $query->select(['*'])->where(['status' => Entry::STATUS_IN_HAND]);
            }]);

            if (!empty($request->status)) {
                $workflowUserSync->where('status', '=', $request->status);
            }
            $workflowUserSync = $workflowUserSync->orderBy('created_at', 'desc')->get();
            //print_r($workflowUserSync->toArray());exit;
            $hrList = array();
            $prefix = config('employee.employee_num_prefix');
            foreach ($workflowUserSync as $key => $val) {
                $hrList[$key]['id'] = Q($val, 'id');
                $hrList[$key]['user_id'] = Q($val, 'user_id');
                $hrList[$key]['created_at'] = ($val->created_at)->toDateTimeString();
                $hrList[$key]['entry_id'] = Q($val, 'entry_id');
                $hrList[$key]['statusmsg'] = ConstFile::$workflowUserSyncStatus[Q($val, 'status')];
                $hrList[$key]['status'] = Q($val, 'status');
                $hrList[$key]['join_at'] = Q($val, 'hasOneUser', 'join_at');
                $hrList[$key]['position'] = Q($val, 'hasOneUser', 'position');
                $hrList[$key]['avatar'] = Q($val, 'hasOneUser', 'avatar');
                $hrList[$key]['chinese_name'] = Q($val, 'hasOneUser', 'chinese_name');
                $hrList[$key]['employee_num'] = $prefix . Q($val, 'hasOneUser', 'employee_num');
                $hrList[$key]['departname'] = Q($val, 'hasOneUser', 'primaryDepartUser', 'department', 'name');
                $hrList[$key]['procs_id'] = Q($val, 'hasOneEntry', 'hasOneProcs', 'id');
                $hrList[$key]['process_id'] = Q($val, 'hasOneEntry', 'process_id');
                $hrList[$key]['whether_power'] = false;

                if (Q($val, 'hasOneEntry')) {
                    if (Q($val, 'hasOneEntry', 'hasOneProcs', 'user_id') == $user->id) $hrList[$key]['whether_power'] = true;
                }
                if (!Q($val, 'hasOneEntry', 'hasOneProcs', 'id')) {
                    $hrList[$key]['whether_power'] = false;
                }

            }

            $data['hrList'] = $hrList;
            $data['status'] = ConstFile::$workflowUserSyncStatusMsg;
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @param $user
     * @return array|string
     */
    public function getPerformanceId($user)
    {
        try {
            $contract = Contract::where(['user_id' => $user->id, 'status' => ConstFile::CONTRACT_STATUS_TWO, 'company_id' => $user->company_id])->orderBy('version', 'desc')->first();
            $usersSalaryData = UsersSalaryData::with('hasOneUsersSalaryRelation')
                ->whereHas('hasOneUsersSalaryRelation', function ($query) {
                    $query->where('status', Salary::SALARY_RELATION_STATUS_BONUS);
                })
                ->where(['contract_id' => $contract->id, 'type' => Salary::SALARY_DATA_TYPE_CONTRACT])
                ->select(['*'])->get();
            $performanceIds = array_column($usersSalaryData->toArray(), 'field_data');
            return $performanceIds;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getUserInfo($userId)
    {
        try {
            $user = User::find($userId);
            $primaryDepartUser = $user->primaryDepartUser;
            $department = $primaryDepartUser->department;
            $detail = $user->detail;
            $userInfo['name'] = $user->name;
            $userInfo['chinese_name'] = $user->chinese_name;
            $userInfo['english_name'] = $user->english_name;
            $userInfo['avatar'] = $user->avatar;
            $userInfo['mobile'] = $user->mobile;
            $userInfo['join_at'] = $user->join_at;
            $userInfo['departname'] = $department->name;
            $userInfo['position'] = $user->position;
            $userInfo['user_status_msg'] = ConstFile::$staffTypeList[$detail->user_status ?? 1];
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $userInfo;
    }

    /**
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getUserStatus($user)
    {
        try {
            $pendingEntry = WorkflowUserSync::with(['hasOneUser'])->whereHas('hasOneUser', function ($query) use ($user) {
                $query->select(['*'])->where(['company_id' => $user->company_id]);
            })->where('status', ConstFile::WORKFLOW_USER_SYNC_STATUS_PENDING_ENTRY)->count();
            $pendingPositive = WorkflowUserSync::with(['hasOneUser'])->whereHas('hasOneUser', function ($query) use ($user) {
                $query->select(['*'])->where(['company_id' => $user->company_id]);
            })->where('status', ConstFile::WORKFLOW_USER_SYNC_STATUS_WAITING_TO_RUEN_POSITIVE)->count();
            $pendingLeaving = WorkflowUserSync::with(['hasOneUser'])->whereHas('hasOneUser', function ($query) use ($user) {
                $query->select(['*'])->where(['company_id' => $user->company_id]);
            })->where('status', ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE)->count();
            $contractExpired = WorkflowUserSync::with(['hasOneUser'])->whereHas('hasOneUser', function ($query) use ($user) {
                $query->select(['*'])->where(['company_id' => $user->company_id]);
            })->where('status', ConstFile::WORKFLOW_USER_SYNC_STATUS_CONTRACT_EXPIRED)->count();
            $first = ['id','chinese_name','avatar','is_person_perfect', 'is_card_perfect', 'is_edu_perfect', 'is_pic_perfect', 'is_family_perfect', 'is_urgent_perfect'];
            $result = User::where("status", User::STATUS_JOIN)
                ->select($first)
                ->get();
            $improvingData = [];

            foreach ($result as $key => $val) {
                $improvingDataSum = array_sum(array(
                    $val->is_person_perfect,
                    $val->is_card_perfect,
                    $val->is_edu_perfect,
                    $val->is_pic_perfect,
                    $val->is_family_perfect,
                    $val->is_urgent_perfect
                ));
                if($improvingDataSum < 100){
                    $improvingData[$key]['id'] = $val->id;
                    $improvingData[$key]['chinese_name'] = $val->chinese_name;
                    $improvingData[$key]['avatar'] = $val->fetchAvatar();
                }
            }
            $data['improving_data'] = array_values($improvingData);
            $data['improving_data_total'] = count($improvingData);
            $data['pending_entry'] = $pendingEntry;
            $data['pending_positive'] = $pendingPositive;
            $data['pending_leaving'] = $pendingLeaving;
            $data['contract_expired'] = $contractExpired;
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }


    /**
     * @description 获取入职合同
     * @author liushaobo
     * @time 2019\4\4
     * @param $entryId
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function fetchContract($entryId)
    {

        try {
            $contract = Contract::with(['hasManySalaryRelation'])
                ->where(['entrise_id' => $entryId])
                ->select()
                ->first();
            $data['probation'] = ConstFile::$probation[$contract->probation];
            $data['contract'] = ConstFile::$contract[$contract->contract];
            $data['template_name'] = $contract->template_name;
            $data['salary_list'] = $contract->hasManySalaryRelation->toArray();
            $data['salary'] = $contract->salary;
            $data['probation_ratio'] = $contract->probation_ratio;
            $data['entry_at'] = $contract->entry_at;
            $data['contract_end_at'] = $contract->contract_end_at;
            $data['state'] = $contract->state;
            $data['version'] = $contract->version;
            $data['status'] = $contract->status;
            $data['user_id'] = $contract->user_id;

        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }

    public function checkData($data)
    {

        if (empty($data)) {
            return '请求数据不能为空';
        }
        if (!isset($data['user_id']) || empty($data['user_id'])) {
            return '用户ID不能为空';
        }
        if (!isset($data['probation']) || empty($data['probation'])) {
            return '请选择试用期';
        }
        if (!isset($data['contract']) || empty($data['contract'])) {
            return '请选择合同期';
        }
        if (!isset($data['template_id']) || empty($data['template_id'])) {
            return '请选择合同期';
        }
        if (!isset($data['probation_ratio']) || empty($data['probation_ratio'])) {
            return '请选择试用期薪资比例';
        }
        if (!isset($data['entry_at']) || empty($data['entry_at'])) {
            return '请选择入职时间';
        }
        if (!isset($data['contract_end_at']) || empty($data['contract_end_at'])) {
            return '请选择合同结束时间';
        }
        return null;
    }

    /**
     * @param $data
     * @param $user
     * @param $salary
     * @param $userInfo
     * @param $departmentArray
     * @param $contractInfo
     * @return mixed
     */
    private function createContractData($data, $user, $salary, $userInfo, $contractInfo)
    {
        $contractData = array();
        $contractData['create_user_id'] = $user->id;
        $contractData['create_user_name'] = $user->name;
        $contractData['template_id'] = $user->id;
        $contractData['template_name'] = $salary->template_name;
        $contractData['company_id'] = $userInfo->company_id;
        $contractData['user_id'] = $userInfo['id'];
        $contractData['user_name'] = $userInfo['name'];
        $contractData['renew_count'] = 1;
        $contractData['probation'] = $data['probation'];
        $contractData['contract'] = $data['contract'];
        $contractData['probation_ratio'] = $data['probation_ratio'];
        $contractData['entry_at'] = $data['entry_at'];
        $contractData['contract_end_at'] = $data['contract_end_at'];
        $contractData['version'] = $contractInfo['version'] + 1;
        $contractData['salary_version'] = $contractInfo['salary_version'] + 1;
        $contractData['entrise_id'] = $data['entrise_id'];
        $contractData['state'] = $data['state'] ?? 1;
        $contract = Contract::create($contractData);
        return $contract;
    }

    /**
     * @param $item
     * @param $userId
     * @param $templateId
     * @param $userInfo
     * @param $user
     * @param $contract
     * @return mixed
     */
    private function createUsersSalaryData($item, $userId, $templateId, $userInfo, $user, $contract)
    {

        $userSalaryData = array();
        $userSalaryData['user_id'] = $userId;
        $userSalaryData['template_id'] = $templateId;
        $userSalaryData['field_id'] = $item['field_id'];
        $userSalaryData['field_name'] = $item['field_name'];
        $userSalaryData['relation_id'] = $item['relation_id'];
        $userSalaryData['field_data'] = $item['field_data'];
        $userSalaryData['company_id'] = $userInfo->company_id;
        $userSalaryData['create_salary_user_id'] = $user->id;
        $userSalaryData['create_salary_user_name'] = $user->name;
        $userSalaryData['contract_id'] = $contract->id;
        $userSalaryData['type'] = 2;
        return UsersSalaryData::create($userSalaryData);
    }

    /**
     * @param $item
     * @param $userId
     * @param $templateId
     * @param $userInfo
     * @param $user
     * @param $contract
     * @param $status
     * @return mixed
     */
    private function createUsersSalary($item, $userId, $templateId, $userInfo, $user, $contract, $status)
    {
        $userSalary = array();
        $userSalary['user_id'] = $userId;
        $userSalary['status'] = $status;
        $userSalary['version'] = $contract->salary_version;
        $userSalary['template_id'] = $templateId;
        $userSalary['relation_id'] = $item['relation_id'];
        $userSalary['field_id'] = $item['field_id'];
        $userSalary['field_name'] = $item['field_name'];
        $userSalary['field_data'] = $item['field_data'];
        $userSalary['company_id'] = $userInfo->company_id;
        $userSalary['create_salary_user_id'] = $user->id;
        $userSalary['create_salary_user_name'] = $user->name;
        $userSalary['contract_id'] = $contract->id;
        return UsersSalary::create($userSalary);
    }
}

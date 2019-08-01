<?php

namespace App\Repositories\Administrative;

use App\Models\DepartUser;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\Template;
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
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\ParentRepository;
use App\Models\Administrative\Contract;

class ContractRepository extends ParentRepository
{
    public function model()
    {
        return Entry::class;
    }

    public function fetchWorkflowList()
    {
        try {
            $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
            $canSeeFlowIds = $canSeeFlowIds->toArray();
            $types = FlowType::with('publish_flow')->where("type_name", "行政相关")->get();

            if (empty($types) || empty($canSeeFlowIds)) {
                return $this->returnApiJson();
            }

            $this->data = $temp = [];
            foreach ($types as $type) {
                foreach ($type->valid_flow as $flow) {
                    if (in_array($flow->id, $canSeeFlowIds)) {
                        $temp['name'] = $flow->flow_name;
                        if (in_array(Q($flow, 'flow_no'), ['official_contract'])) {
                            $temp['url'] = route('api.administrative.contract.create', ['flow_id' => $flow->id]);
                        } else {
                            $temp['url'] = route('api.flow.create', ['flow_id' => $flow->id]);
                        }

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
    public function storeWorkflow(Request $request)
    {
        return $this->updateFlow($request, 0);
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
            $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            $this->data = [
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry)
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function workflowShowLeaveFromAuditor(Request $request)
    {
        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $entry = Entry::findUserEntry($authAuditor->id(), $id);
            $this->data = [
                'entry' => $entry->entry_data->toArray(),
                'template' => $this->fetchEntryTemplate($entry)->template_form->toArray(),
                'template_other' => $this->fetchEntryTemplateForLeave(),
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
            //$id = $request->get('id',0);
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            $entry = $this->updateOrCreateEntry($request, $id); // 创建或更新申请单
            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            /****************合同信息添加*************/
            $arr = $request->get('tpl');
            $arr['user_id'] = Auth::id();
            $arr['process_userId'] = "&";
            $arr['status'] = ConstFile::ADMINISTRATIVE_CONTRACT_NO;
            $arr['entry_id'] = $entry->id;
            //添加内容
            Contract::create($arr);
            /****************end********************/
            $entry->save();
            DB::commit();

            $this->data = ['entry' => $entry->toArray()];
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            Workflow::errLog('AdministrativeContractUpdate', $e->getMessage() . $e->getTraceAsString());
        }
        return $this->returnApiJson();
    }

    public function createPendingUserFlow(Request $request)
    {
        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            //根据当前提交的信息生成用户并且自动登录
            $this->createUser($request);
            $entry = $this->updateOrCreateEntry($request); // 创建或更新申请单
            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            $entry->save();
            DB::commit();
            $this->data['entry_id'] = $entry->id;
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            Workflow::errLog(' pendinguser EntryCreate', $e->getMessage() . $e->getTraceAsString());
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
    public function updateOrCreateEntry(Request $request, $id = 0)
    {
        $data = $request->all();
        $data['file_source_type'] = 'workflow';
        $data['file_source'] = 'official_contract';
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

    public function updateTpl(Entry $entry, $tpl = [])
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
            $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            $this->data = [
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

    /**
     * 行政合同申请单表单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show(Request $request)
    {
        try {
            //判断是否是个人
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_ADMINISTRATIVE_DOCUMENTS);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
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
     * @param int $page
     * @param int $flow_id
     * @param string $status
     * @param string $keyword
     * @param int $size
     * @param int $typeId
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function history($page = 1, $flow_id = 0, $status = '', $keyword = '', $size = 20, $typeId = Flow::TYPE_ATTENTION)
    {
        //need page
        $authAuditor = new AuthUserShadowService();
        $uid = $authAuditor->id();


        $query = Entry::query();

        if ($flow_id) {
            $query->where('flow_id', '=', $flow_id);

            $flowObj = Flow::find($flow_id);
            $templateForm = $flowObj->template->template_form_show_in_todo;
        } else {
            $flows = Flow::query()
                ->where('type_id', '=', $typeId)
                ->where('is_publish', '=', Flow::PUBLISH_YES)
                ->get()->pluck('flow_name', 'id');
            $query->whereIn('flow_id', array_keys($flows->toArray()));
        }

        if ($status != '' && isset(Entry::$_status[$status])) {
            $query->where('status', '=', $status);
        }
        if ($keyword) {
            $query->where('title', 'like', '%' . $keyword . '%');
        }
        $entries = $query->where('user_id', '=', $uid)
            ->with(['user', 'entry_data'])->orderBy('created_at', 'desc')
            ->paginate($size, ['*'], 'page', $page);

        $arr = [];
        $entryItems = $entries->items();
        foreach ($entryItems as $entry) {
            if ($flow_id == 0) {
                /** @var Entry $entry */
                $flowObj = $entry->flow;
                if (empty($flowObj)) {
                    continue;
                }
            } else {
                $flowObj = Flow::find($flow_id);
            }
            $templateForm = $flowObj->template->template_form_show_in_todo;
            /** @var Entry $entry */
            $entry->info = $this->dealInfo($entry, $templateForm);
            $this->dealCreatedTime($entry);
            $entry->title = $entry->user->chinese_name . '提交的' . $entry->title;
            $entry->status_str = Entry::$_status[$entry->status];
            $entry->template_id = $flowObj->template->id;
            if ($flow_id) {
                if ($entry->created_at->isCurrentMonth()) {
                    $arr['cur_month']['items'][] = $entry;
                } else {
                    $arr['left_month']['items'][] = $entry;
                }
            } else {
                $arr['entries'][] = $entry;
            }
        }
        if ($arr) {
            $arr['filter_status'] = Entry::$_status;
            if (isset($arr['cur_month'])) {
                $arr['cur_month']['title'] = '本月';
            }
            if (isset($arr['left_month'])) {
                $arr['left_month']['title'] = '更早以前';
            }
            $arr['page'] = $entries->currentPage();
            $arr['total_page'] = $entries->total();
        }

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,
            ConstFile::API_RESPONSE_SUCCESS, $arr);
    }

    /**
     * @param Entry $entry
     * @param Template $templateForm
     * @return array
     */
    public function dealInfo($entry, $templateForm)
    {
        $info = [];
        /** @var Entry $entry */
        $entriesData = $entry->entry_data;
        foreach ($entriesData as $datum) {
            $arr = [];
            foreach ($templateForm as $form) {
                /** @var EntryData $datum */
                if ($datum->field_name == $form->field) {
                    $arr[$form->field] = $datum->field_value;
                    $arr[$form->field . '_str'] = $form->field_name;
                    $info[] = $arr;
                } else {
                    continue;
                }
            }
        }
        unset($entry->entry_data);
        return $info;
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


    public function processQuery(Request $request)
    {

        $flows = Flow::getFlowsOfNo(); // 流程列表        //我审批过的
        $authAuditor = new AuthUserShadowService();

        $tab = $request->get('tab', 'my_apply');
        if ($tab == 'my_audited') {
            $myAuditedSearchData = $request->except('tab');
            $entries = Proc::getUserAuditedByPage($authAuditor->id(), $myAuditedSearchData, 10);
            foreach ($entries as $proc) {
                $proc->statusMAP = Entry::STATUS_MAP[$proc->entry->status];
                $proc->url = route('api.positive.proc') . "?id=" . $proc->id;
            }
        } elseif ($tab == 'my_apply') {
            $myApplySearchData = $request->except('tab');
            //我提交过的
            $entries = Entry::getApplyEntries($authAuditor->id(), $myApplySearchData, 10);
            foreach ($entries as $entry) {
                $entry->statusDesc = $entry->getStatusDesc();
                foreach ($entry->getCurrentStepProcs() as $proc) {
                    if (empty($proc->authorizer_ids)) {
                        $entry->user_name = $proc->user_name;
                    }
                }
                $entry->url = route('api.positive.entryShow') . "?id=" . $entry->id;
            }
        } elseif ($tab == 'my_procs') {
            $myProcsSearchData = $request->except('tab');
            //待我审批
            $entries = Proc::getUserProcByPage($authAuditor->id(), $myProcsSearchData, 10);
            foreach ($entries as $proc) {
                if ($proc->entry->isInHand()) {
                    $proc->url = route('api.positive.proc') . "?id=" . $proc->id;
                }
            }
        }

        $this->data = compact('entries');
        return $this->returnApiJson();
    }

    /**
     * @deprecated 流程
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function entryShow(Request $request)
    {

        try {
            $id = $request->get('id');
            $entry = Entry::findOrFail($id);
            if ($entry->pid > 0) {
                $templateForm = $this->fetchEntryTemplate($entry->parent_entry)->template_form;
                $showData = $this->fetchShowData($entry->parent_entry->entry_data, $templateForm);

            } else {
                $templateForm = $this->fetchEntryTemplate($entry)->template_form;
                $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            }
            $this->data = [
                'show_data' => $showData,//申请内容
                'processes' => $this->fetchEntryProcess($entry),//审批记录
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function procShow(Request $request)
    {
        try {
            //获取信息
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $proc = Proc::findUserProcAllStatus($authAuditor->id(), $id);

            $entry = Entry::findOrFail($proc->entry_id);
            if ($entry->pid > 0) {
                $templateForm = $entry->parent_entry->flow->template->template_form;
                $showData = $this->fetchShowData($entry->parent_entry->entry_data, $templateForm);

            } else {
                $templateForm = $this->fetchEntryTemplate($entry)->template_form;
                $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            }


            $this->data = [
                'show_data' => $showData,//申请内容
                'processes' => $this->fetchEntryProcess($entry),//审批记录
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }


    public function contractShow(Request $request)
    {
        //员工id
        $userId = Auth::id();
        try {
            $otherContract = Contract::where('process_userId', 'like', "%&" . $userId . "&%")->where('status', 1)->orderBy('created_at', 'desc')->select('id', 'title', 'entry_id')->get();
            $myContract = Contract::where('user_id', $userId)->orderBy('created_at', 'desc')->where('status', 1)->select('id', 'title', 'entry_id')->get();
            $this->data = [
                'otherContract' => $otherContract,//其他合同
                'myContract' => $myContract,//我的合同
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }


    public function contractSearch(Request $request)
    {
        //接值
        $arr = $request->all();
        if (empty($arr)) {
            $this->message = '参数不可为空';
            $this->code = '-1';
        }
        $model = new Contract();
        try {
            //用户
            if (isset($arr['username'])) {
                $user = User::where('chinese_name', 'like', "%" . $arr['username'] . "%")
                    ->where('status', 1)
                    ->first(['id', 'chinese_name']);
                if (empty($user)) {
                    throw new Exception('员工不存在');
                }
                $userId = $user->id;
                $otherModel = $model->where('process_userId', 'like', "%&" . $userId . "&%");
                $myModel = $model->where('user_id', $userId);
            } else {
                $userId = Auth::id();
                $otherModel = $model->where('process_userId', 'like', "%&" . $userId . "&%");
                $myModel = $model->where('user_id', $userId);
            }
            //时间
            if (isset($arr['created_at'])) {
                $otherModel = $otherModel
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($arr['created_at'])));
                $myModel = $myModel
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($arr['created_at'])));
            }
            //部门
            if (isset($arr['primary_dept'])) {
                $otherModel = $otherModel
                    ->where('primary_dept',$arr['primary_dept']);
                $myModel = $myModel
                    ->where('primary_dept',$arr['primary_dept']);
            }
            //其他
            $otherContract = $otherModel->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->select('id', 'title', 'entry_id')->get();
            //我的
            $myContract = $myModel->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->select('id', 'title', 'entry_id')->get();
            $this->data = [
                'otherContract' => $otherContract,//其他合同
                'myContract' => $myContract,//我的合同
            ];

        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }


    public function workflowShows(Request $request)
    {
        try {
            $entry_id = $request->get('entry_id');
            $entry = Entry::findOrFail($entry_id);
            $templateForm = $this->fetchEntryTemplate($entry)->template_form;
            $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            $this->data = [
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry),
            ];


        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

}
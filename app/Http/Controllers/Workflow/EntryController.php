<?php

namespace App\Http\Controllers\Workflow;

use App\Models\Contract;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use DevFixException;
use Log;

class EntryController extends Controller
{

    public function index()
    {
        // 获取有审批权限的
        $authAuditor = new AuthUserShadowService();
        //我的申请
        $entries = Entry::getWithProProcess($authAuditor->id(), [Entry::STATUS_IN_HAND, Entry::STATUS_DRAFT], 15);

        //我的待办
        $procs = Proc::getUserProc($authAuditor->id(), 15);

        return view('workflow.entry.index')->with(compact("entries", "procs"));
    }

    public function todoList()
    {
        // 获取有审批权限的
        $authAuditor = new AuthUserShadowService();
        //我的申请
        $entries = Entry::getTodoEntry($authAuditor->id());
        $list    = [];

        foreach ($entries as $entry) {
            $entry->proc_id = $entry->procs ? $entry->procs[0]->id : 0;
            if ($entry->flow && !isset($list[$entry->flow->flow_name]['flow_no'])) {
                $list[$entry->flow->flow_name]['flow_no'] = $entry->flow->flow_no;
                //查询最新的模板
                $flow = Flow::findByFlowNo($entry->flow->flow_no);
                if (!$flow) {
                    $flow = Flow::findByFlowNoAnyWay($entry->flow->flow_no);
                }
                if (!$flow) {
                    $flow = Flow::findAllByFlowNo($entry->flow->flow_no);
                    $flow = $flow[0] ?? null;
                }
                if (!$flow) {
                    throw new DevFixException('流程数据出错', 400);
                }
                //控制显示的字段内容
                //标准处理字段
                $list[$entry->flow->flow_name]['template_show'] = [];
                foreach ($flow->template->template_form as $form) {
                    if ($form->show_in_todo) {
                        $list[$entry->flow->flow_name]['template_show'][$form->sort] = $form;
                    }
                }
                //特殊处理部门字段
                if ($primary_dept = $flow->template->template_form->where('field_type', 'primary_dept')->first()) {
                    $entry->user->primary_dept = $primary_dept->field_value ?: WorkflowUserService::fetchUserPrimaryDeptPath($entry->user_id);
                } else {
                    $entry->user->primary_dept = Q($entry,'user','status') == -1?null:WorkflowUserService::fetchUserPrimaryDeptPath($entry->user_id);
                }
                $list[$entry->flow->flow_name]['count'] = isset($list[$entry->flow->flow_name]['count']) ? $list[$entry->flow->flow_name]['count'] + 1 : 1;
                if (!isset($list[$entry->flow->flow_name]['data']) || count($list[$entry->flow->flow_name]['data']) < 10) {
                    $list[$entry->flow->flow_name]['data'][] = $entry;
                }
            }
        }

        return view('workflow.entry.todo')->with(compact("list"));
    }

    public function create(Request $request)
    {
        $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
        $flow_id       = $request->get('flow_id', 0);
        if ($flow_id > 0) {
            if (!$canSeeFlowIds->contains($flow_id)) {
                throw new DevFixException('当前流程不可用');
            }

            $flow         = Flow::publish()->findOrFail($flow_id);
            $user_id      = Auth::id();
            $form_html    = Workflow::generateHtml($flow->template, null, null, $user_id);
            $defaultTitle = $flow->flow_name . date('md');
            return view('workflow.entry.create')->with(compact("flow", "form_html", "user_id", "defaultTitle"));
        } else {
            $canSeeFlowIds = $canSeeFlowIds->toArray();
            $types         = FlowType::with('publish_flow')->get();
            return view('workflow.entry.create_list')->with(compact("types", "canSeeFlowIds"));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->update($request, 0);
    }

    public static function storeBySystem(Request $request, $flow_no, $title, $tpl = [])
    {
        $flow             = Flow::findByFlowNoAnyWay($flow_no);
        $data             = [
            'flow_id' => $flow->id,
            'title'   => $title,
            'tpl'     => $tpl,
        ];
        $request->request = new ParameterBag($data);
        $self             = new self();

        return $self->update($request, 0);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $authAuditor = new AuthUserShadowService();
        $entry       = Entry::findUserEntry($authAuditor->id(), $id);

        //还原申请表
        if ($entry->pid > 0) {
            $form_html = $entry->parent_entry->flow->template ?
                Workflow::generateHtml(
                    $entry->parent_entry->flow->template,
                    $entry->parent_entry,
                    null,
                    $authAuditor->id()
                ) : '';
        } else {
            $form_html = $entry->flow->template ? Workflow::generateHtml(
                $entry->flow->template,
                $entry,
                null,
                $authAuditor->id()
            ) : '';
        }
        //申请进程
        $processes      = (new Workflow())->getProcs($entry);
        $processes_html = Workflow::generateProcessHtml($processes, $entry);

        return view('workflow.entry.show')->with(compact('entry', 'form_html', 'processes_html'));
    }


    /**
     * 流程管理员查看流程详情
     * @param $id 流程id
     * @param $userId 员工id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function managerShow($id, $userId)
    {
        $entry = Entry::findUserEntry($userId, $id);

        if ($entry->status == Entry::STATUS_DRAFT) {
            abort(403, '非法操作');
        }

        //还原申请表
        if ($entry->pid > 0) {
            $form_html = $entry->parent_entry->flow->template ?
                Workflow::generateHtml(
                    $entry->parent_entry->flow->template,
                    $entry->parent_entry,
                    null,
                    $userId
                ) : '';
        } else {
            $form_html = $entry->flow->template ? Workflow::generateHtml(
                $entry->flow->template,
                $entry,
                null,
                $userId
            ) : '';
        }
        //申请进程
        $processes      = (new Workflow())->getProcs($entry);
        $processes_html = Workflow::generateProcessHtml($processes, $entry);

        return view('workflow.entry.show')->with(compact('entry', 'form_html', 'processes_html'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function hrShow($id)
    {
        $entry = Entry::findOrFail($id);;
        //还原申请表
        if ($entry->pid > 0) {
            $form_html = $entry->parent_entry->flow->template ?
                Workflow::generateHtml($entry->parent_entry->flow->template,
                    $entry->parent_entry) : '';
        } else {
            $form_html = $entry->flow->template ? Workflow::generateHtml($entry->flow->template, $entry) : '';
        }
        //申请进程
        $processes      = (new Workflow())->getProcs($entry);
        $processes_html = Workflow::generateProcessHtml($processes, $entry);

        return view('workflow.entry.show')->with(compact('entry', 'form_html', 'processes_html'));
    }


    public function edit($id)
    {
        $entry = Entry::findOrFail($id);
        $flow  = Flow::publish()->findOrFail($entry->flow_id);

        $user_id   = Auth::id();
        $form_html = Workflow::generateHtml($entry->flow->template, $entry, null, $user_id);
        return view('workflow.entry.edit')->with(compact("entry", "flow", "form_html", "user_id"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Log::info(json_encode($request->all()));
        try {
            DB::beginTransaction();

            $flow_id = $request->input('flow_id');
            $flow    = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义

            $entry = $this->updateOrCreateEntry($request, $id); // 创建或更新申请单
            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            $entry->save();
            DB::commit();

            return response()->json([
                'success' => 1,
                'msg'     => $entry->isInHand() ? '申请成功' : '保存成功',
                'data'    => [
                    'entry' => $entry->toArray(),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Workflow::errLog('EntryUpdate', $e->getMessage() . $e->getTraceAsString());
            return response()->json([
                'success' => -1,
                'msg'     => $e->getMessage(),
                'data'    => [
                    'entry' => null,
                ],
            ]);
        }
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
        $data = $request->all();

        if (!$id) {
            $authApplyer = new AuthUserShadowService(); // 以影子用户作为申请人
            $entry       = Entry::create([
                'title'            => $data['title'],
                'flow_id'          => $data['flow_id'],
                'user_id'          => $authApplyer->id(),
                'circle'           => 1,
                'status'           => Entry::STATUS_IN_HAND,
                'origin_auth_id'   => Auth::id(),
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
                'flow_id'     => $entry->flow_id,
                'field_value' => $val,
                'created_at'  => \Carbon\Carbon::now(),
                'updated_at'  => \Carbon\Carbon::now(),
            ]);
        }
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @author hurs
     */
    public function destroy($id)
    {
        $authAuditor = new AuthUserShadowService();
        $entry       = Entry::findUserEntryData($authAuditor->id(), $id);
        //合同申请撤销的时候同步更新合同表中对应记录的状态
        if ($entry->flow->flow_no == Entry::WORK_FLOW_NO_CONTRACT_APPLY) {
            Contract::updateContractStatusByEntryId($id, Contract::CONTRACT_STATUS_CANCEL);
        }

        $option = $entry->isDraft() ? '删除' : '撤销';
        if (Entry::deleteEntry($id)) {
            return Response::json(['code' => 0, 'status' => 'success', 'message' => $option . '成功']);
        } else {
            return Response::json(['code' => 0, 'status' => 'success', 'message' => $option . '失败']);
        }
    }

    public function resend(Request $request)
    {
        $entry_id = $request->input('entry_id', 0);

        try {
            DB::beginTransaction();
            $entry = Entry::where(['status' => -1])->findOrFail($entry_id);

            Flow::publish()->findOrFail($entry->flow_id);

            $flowlink = Flowlink::firstStepLink($entry->flow_id);

            $entry->circle = $entry->circle + 1;
            $entry->child  = 0;
            $entry->status = 0;
            $entry->save();

            //进程初始化
            (new Workflow())->setFirstProcessAuditor($entry, $flowlink);
            DB::commit();

            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollback();
            Workflow::errLog('EntryResend', $e->getMessage() . $e->getTraceAsString());

            return redirect()->back()->with(['success' => -1, 'message' => $e->getMessage()]);
        }

    }

    public function cancel(Request $request)
    {
        $entry_id = $request->input('entry_id', 0);

        try {
            DB::beginTransaction();
            $entry = Entry::where(['status' => Entry::STATUS_REJECTED])->findOrFail($entry_id);

            $entry->status = Entry::STATUS_CANCEL;

            $entry->save();
            DB::commit();

            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollback();
            Workflow::errLog('EntryCancel', $e->getMessage() . $e->getTraceAsString());

            return redirect()->back()->with(['success' => -1, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 我的申请单
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function myApply(Request $request)
    {
        // 获取有审批权限的
        $authAuditor = new AuthUserShadowService();
        //我的申请
        $entries = Entry::getApplyEntries($authAuditor->id(), $request->all());

        $flows          = Flow::getFlowsOfNo(); // 流程列表
        $entryStatusMap = Entry::STATUS_MAP; // 申请单状态map
        $searchData     = $request->all();

        return view('workflow.entry.my_apply')->with(compact('entries', 'flows', 'entryStatusMap', 'searchData'));
    }

    /**
     * 待我审批的
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function myProcs(Request $request)
    {
        // 获取有审批权限的
        $authAuditor = new AuthUserShadowService();
        $procs       = Proc::getUserProcByPage($authAuditor->id(), $request->all(), 20);

        $flows          = Flow::getFlowsOfNo(); // 流程列表
        $entryStatusMap = Entry::STATUS_MAP; // 申请单状态map
        $searchData     = $request->all();

        return view('workflow.entry.my_procs')->with(compact('procs', 'flows', 'entryStatusMap', 'searchData'));
    }

    /**
     * 我审批过的
     */
    public function myAudited(Request $request)
    {


        // 获取有审批权限的
        $authAuditor = new AuthUserShadowService();
        $procs       = Proc::getUserAuditedByPage($authAuditor->id(), $request->all(), 20);

        $flows = Flow::getFlowsOfNo(); // 流程列表

        // 我审批过的只需要处理中、结束、拒绝三种过滤状态
        $entryStatusMap = [
            Entry::STATUS_IN_HAND  => Entry::STATUS_MAP[Entry::STATUS_IN_HAND],
            Entry::STATUS_FINISHED => Entry::STATUS_MAP[Entry::STATUS_FINISHED],
            Entry::STATUS_REJECTED => Entry::STATUS_MAP[Entry::STATUS_REJECTED],
        ];

        return view('workflow.entry.my_audited')->with(compact('procs', 'flows', 'entryStatusMap', 'searchData'));
    }


    public function processQuery(Request $request)
    {

        $myAuditedSearchData = [];
        $myProcsSearchData   = [];
        $myApplySearchData   = [];
        $tab                 = $request->get('tab');
        if ($tab == 'my_audited') {
            $myAuditedSearchData = $request->except('tab');
        } elseif ($tab == 'my_procs') {
            $myProcsSearchData = $request->except('tab');
        } elseif ($tab == 'my_apply') {
            $myApplySearchData = $request->except('tab');
        }
        $flows = Flow::getFlowsOfNo(); // 流程列表
        //我审批过的
        $authAuditor                = new AuthUserShadowService();
        $procsAudited               = Proc::getUserAuditedByPage($authAuditor->id(), $myAuditedSearchData, 10);
        $myAuditedSearchData['tab'] = 'my_audited';
        // 我审批过的只需要处理中、结束、拒绝三种过滤状态
        $entryAuditorStatusMap = [
            Entry::STATUS_IN_HAND  => Entry::STATUS_MAP[Entry::STATUS_IN_HAND],
            Entry::STATUS_FINISHED => Entry::STATUS_MAP[Entry::STATUS_FINISHED],
            Entry::STATUS_REJECTED => Entry::STATUS_MAP[Entry::STATUS_REJECTED],
        ];

        //待我审批
        $procsProc                = Proc::getUserProcByPage($authAuditor->id(), $myProcsSearchData, 10);
        $entryProcStatusMap       = Entry::STATUS_MAP; // 申请单状态map
        $myProcsSearchData['tab'] = 'my_procs';

        //我提交过的
        $entries = Entry::getApplyEntries($authAuditor->id(), $myApplySearchData, 10);

        $entryEntriesStatusMap    = Entry::STATUS_MAP; // 申请单状态map
        $myApplySearchData['tab'] = 'my_apply';

        return view('workflow.entry.process_query',
            compact('procsAudited', 'flows', 'entryAuditorStatusMap', 'procsProc', 'entryProcStatusMap', 'entries', 'entryEntriesStatusMap',
                'myAuditedSearchData', 'myProcsSearchData', 'myApplySearchData'));
    }

    /**
     * 获取指定流程数据
     *
     * @param Request $request
     */
    public function EntryData(Request $request, $id)
    {
        $entryData = EntryData::where('entry_id', $id)->get()->toArray();
        $newData   = [];
        foreach ($entryData as $data) {
            $newData[$data['field_name']] = $data['field_value'];
        }

        return Response::json([
            'code'    => 0,
            'status'  => 'success',
            'message' => '成功',
            'data'    => $newData,
        ]);
    }
}


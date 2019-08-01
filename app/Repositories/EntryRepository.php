<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Models\Comments\TotalComment;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\Message\Message;
use App\Models\MyTask\MyTask;
use App\Models\Position\PositionDepartment;
use App\Models\Task\Task;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\Template;
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
use App\Models\Workflow\Flow;
use App\Models\User;
use App\Models\Basic\BasicSet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Auth;
use Hash;
use DB;
use JWTAuth;
use Cache;
use App\Http\Services\SmsTrait;

class EntryRepository extends ParentRepository
{
    use SmsTrait;

    public function model()
    {
        return Entry::class;
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
                    if (in_array(Q($e, 'flow', 'flow_no'), ['fee_expense', 'finance_loan', 'finance_repayment', 'finance_receivables', 'finance_payment'])) {
                        $entriesList[$k1]['url'] = route('api.finance.flow.show') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['positive_apply', 'positive_wage_apply'])) {
                        $entriesList[$k1]['url'] = route('api.positive.flow.show') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['official_contract'])) {
                        $entriesList[$k1]['url'] = route('api.administrative.contract.show') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['meeting_record_review'])) {//会议工作流
                        $entriesList[$k1]['url'] = route('meeting.meeingmeinfo') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['meeting_summary'])) {//会议工作流
                        $entriesList[$k1]['url'] = route('meeting.meeingmeinfo') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['pas_purchase'])) {//进销存采购单
                        $entriesList[$k1]['url'] = route('purchase.getinfone') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['pas_return_order'])) {//进销存退货单
                        $entriesList[$k1]['url'] = route('returnOrder.getweinfo') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['pas_payment_order'])) {//进销存入库单
                        $entriesList[$k1]['url'] = route('payment.getweinfo') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), Entry::CUSTOMIZE)) {
                        $entriesList[$k1]['url'] = route('api.flow.customize') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['intelligence_apply'])) {
                        $entriesList[$k1]['url'] = route('api.inte.flow.show') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['inspector_apply'])) {
                        $entriesList[$k1]['url'] = route('api.insp.flow.show') . "?id=" . $e['id'];
                    } else {
                        $entriesList[$k1]['url'] = route('api.flow.show') . "?id=" . $e['id'];
                    }
//toDo 硬编码改为常量
                }
            }

            if (!empty($process)) {
                foreach ($process as $k2 => $p) {
                    $processList[$k2]['title'] = $p->entry->title;
                    if (in_array(Q($p, 'flow', 'flow_no'), ['fee_expense', 'finance_loan', 'finance_repayment', 'finance_receivables', 'finance_payment'])) {
                        $processList[$k2]['url'] = route('api.finance.auditor_flow.show') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['positive_apply', 'positive_wage_apply'])) {
                        $processList[$k2]['url'] = route('api.positive.auditor_flow.show') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['official_contract'])) {
                        $processList[$k2]['url'] = route('api.administrative.contract.auditor_flow.show') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['intelligence_apply'])) {
                        $processList[$k2]['url'] = route('api.inte.auditor_flow.show') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['inspector_apply'])) {
                        $processList[$k2]['url'] = route('api.insp.auditor_flow.show') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), Entry::CUSTOMIZE)) {
                        $processList[$k2]['url'] = route('api.flow.customize.auditor.flow.show') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['meeting_record_review'])) {//会议工作流
                        $processList[$k2]['url'] = route('meeting.meetingreviewedinfo') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['meeting_summary'])) {//会议工作流
                        $processList[$k2]['url'] = route('meeting.meetingreviewedinfo') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['pas_purchase'])) {//进销存采购单
                        $processList[$k2]['url'] = route('purchase.gettrialinfo') . "?id=" . $p['id'];
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['pas_return_order'])) {//进销存退货单
                        $processList[$k2]['url'] = route('returnOrder.getinfotow') . "?id=" . $p['id'];

                    } elseif (in_array(Q($p, 'flow', 'flow_no'), ['pas_payment_order'])) {//进销存入库单
                        $processList[$k2]['url'] = route('payment.getinfotow') . "?id=" . $p['id'];
                    } else {
                        $processList[$k2]['url'] = route('api.auditor_flow.show') . "?id=" . $p['id'];
                    }

                }
            }

            $this->data = ['authAuditor' => $authAuditor, 'entries' => $entriesList, 'process' => $processList];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchWorkflowList()
    {
        try {
            $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
            $canSeeFlowIds = $canSeeFlowIds->toArray();
            $types         = FlowType::with(['publish_flow' => function ($query) {
                $query->where('is_abandon', Flow::ABANDON_NO)
                    ->select(['flow_no', 'id as flow_id', 'type_id', 'flow_name', 'route_url', 'icon_url']);
            }])->where("type_name", "行政相关")
                ->select('id', 'type_name')->orderBy('sortby', 'desc')->get();

            if (empty($types) || empty($canSeeFlowIds)) {
                return $this->returnApiJson();
            }

            $this->data = [];
            $arr1       = config('services.arr1');
            $arr2       = config('services.arr2');
            $arr3       = config('services.arr3');
            $arr4       = config('services.arr4');
            $arr5       = config('services.arr5');
            $arr6       = config('services.arr6');
            $arr7       = config('services.arr7');
            $arr8       = config('services.arr8');
            if ($types) {
                $temps = $types->toArray();
                foreach ($temps as $key => &$val) {
                    if ($val['type_name'] == '考勤相关') {
                        array_unshift($val['publish_flow'], $arr1);//打卡

                    }
                    if ($val['type_name'] == '行政相关') {
                        $val['publish_flow'] = [];
                        $val['type_name']    = '其他应用';
                        array_push($val['publish_flow'], $arr3);//行政
                        array_push($val['publish_flow'], $arr4);//行政
                        array_push($val['publish_flow'], $arr5);//行政
                        array_push($val['publish_flow'], $arr6);//行政
                        array_push($val['publish_flow'], $arr8);//行政
                    }
                    if ($val['type_name'] == '财务相关') {
                        array_push($val['publish_flow'], $arr7);//人事
                    }
                    if ($val['type_name'] == '人事相关') {
                        $tempA = [];
                        if ($val['publish_flow']) {
                            foreach ($val['publish_flow'] as &$v1) {
                                if (in_array($v1['flow_no'], ['positive_apply', 'active_leave_apply'])) {
                                    array_push($tempA, $v1);
                                }

                            }
                        }
                        array_push($tempA, $arr2);//人事
                        $val['publish_flow'] = $tempA;
                    }
                }
                $this->data = $temps;
            } else {
                $this->data = [];
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * 通用:创建流程
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function createWorkflow(Request $request)
    {
        try {
            $canSeeFlowIds = WorkflowUserService::fetchUserCanSeeWorkflowIds(Auth::id());
            $flow_id       = $request->get('flow_id', 0);
            $flow_id       = (int)$flow_id;
            if ($flow_id < 0) {
                throw new Exception(sprintf('无效的流程ID:%s', $flow_id));
            }

            if (!$canSeeFlowIds->contains($flow_id)) {
                throw new Exception('当前流程不可用');
            }

            $flow    = Flow::publish()->findOrFail($flow_id);
            $user_id = Auth::id();
            Workflow::generateHtml($flow->template, null, null, $user_id);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();

            $this->data = [
                'flow'          => $flow->template->toArray(),
                'user_id'       => $user_id,
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * 待入职人员申请单表单(这时他仍是非系统用户,user表里没有记录)
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function showPendingUsersForm(Request $request)
    {
        try {
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_ENTRY_APPlY);
            throw_if(!$flow, new Exception('流程不存在'));
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
                'flow_id'       => $flow->id,
                'template'      => $flow->template->toArray(),
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }

        return $this->returnApiJson();
    }

    /**
     * 保存提交的流程信息
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeWorkflow(Request $request)
    {
        try {
            $this->checkRequest($request);
            $this->updateFlow($request, 0);
            return $this->returnApiJson();
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            return $this->returnApiJson();
        }
    }

    /**
     * 展示流程详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function workflowShow(Request $request)
    {
        try {
            $this->workflowShowData($request);
            return $this->returnApiJson();
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
    }

    public function workflowShowData(Request $request)
    {
        try {
            $id          = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $entry       = Entry::find($id);
            $procs = $entry->procs;
            $checkIds = [];
            foreach ($procs as $k => $v){
                $checkIds[] = $v->user_id;
            }
            if (!$entry && (!in_array($entry->user_id , $checkIds) || $authAuditor->id != $entry->user_id)) {
                $this->message = '您没有访问权限!';
                $this->code    = ConstFile::API_RESPONSE_FAIL;
                return $this->returnApiJson();
            }
            $templateForm = $this->fetchEntryTemplate($entry)->template_form;
            $showData     = $this->fetchShowData($entry->entry_data, $templateForm);
            throw_if(!isset(Entry::$_status[$entry['status']]), new Exception(sprintf('不存在的状态:%s', $entry['status'])));

            //评论
            $comments = TotalComment::query()
                ->where('type', '=', TotalComment::TYPE_WORKFLOW)
                ->where('entry_id', '=', $id)
                ->with(['user'])
                ->get();

            $user     = User::with('fetchPrimaryDepartment')->findOrFail($entry->user_id);
            $userInfo = [
                'user_id'      => $user->id,
                'avatar'       => $user->fetchAvatar(),
                'name'         => $user->chinese_name,
                'employee_num' => $user->employee_num,
                'position'     => $user->position,
                'department'   => $user->fetchPrimaryDepartment->first()->name,
            ];

            $this->data = [
                'order_no'        => $entry->order_no,
                'entry_status'    => isset(Entry::$_status[$entry['status']]) ? Entry::$_status[$entry['status']] : '',
                'entry_status_no' => isset(Entry::$_status[$entry['status']]) ? $entry['status'] : '',
                'show_data'       => $showData,
                'processes'       => $this->fetchEntryProcess($entry),
                'procs_id'        => $entry->procsFirstNode()->id,
                'comment_for_end' => $comments,   // 整个流程完成之后的评论
                'user_info'       => $userInfo,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
    }

    //离职申请内容展示:审批人视角
    public function workflowShowLeaveFromAuditor(Request $request)
    {
        try {
            $id          = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $entry       = Entry::findUserEntry($authAuditor->id(), $id);
            $this->data  = [
                'order_no'       => $entry->order_no,
                'entry'          => $entry->entry_data->toArray(),
                'template'       => $this->fetchEntryTemplate($entry)->template_form->toArray(),
                'template_other' => $this->fetchEntryTemplateForLeave(),
                'processes'      => $this->fetchEntryProcess($entry)
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
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
            $entry->save();
            DB::commit();

            $this->data = ['entry' => $entry->toArray()];
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            Workflow::errLog('EntryUpdate', $e->getMessage() . $e->getTraceAsString());
        }
        return $entry->id;
    }

    /**
     * 创建入职流程
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function createPendingUserFlow(Request $request)
    {
        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');
            $flow    = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            //根据当前提交的信息生成用户并且自动登录
            $newUserId = $this->createUser($request);
            $entry     = $this->updateOrCreateEntry($request); // 创建或更新申请单

            if ($entry->isInHand()) {
                $flow_link = Flowlink::firstStepLink($entry->flow_id);
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
            $entry->save();
            //写入数据到快照表
            $quickShotData = [
                'apply_user_id' => $newUserId,
                'user_id'       => $newUserId,
                'status'        => ConstFile::WORKFLOW_USER_SYNC_STATUS_PENDING_ENTRY,
                'content_json'  => json_encode(['content' => '已提交入职申请,进入待入职状态'], JSON_UNESCAPED_UNICODE),
                'entry_id'      => $entry->id
            ];
            $res           = (new WorkflowUserSync())->fill($quickShotData)->save();
            throw_if(!$res, new Exception('同步待入职数据失败', ConstFile::API_RESPONSE_FAIL));
            DB::commit();

            $employeeNum = 100000 + $newUserId;//工号生成规则
            User::findOrFail($newUserId)->setAttribute('employee_num', $employeeNum)->save();
            $this->data['entry_id'] = $entry->id;
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
            $this->data    = [];
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
        $data                     = $request->all();
        $data['file_source_type'] = 'workflow';
        $data['file_source']      = 'entry_apply';
        $data['is_draft']         = null;
        $data['entry_id']         = null;

        if (!$id) {
            $authApply = new AuthUserShadowService(); // 以影子用户作为申请人
            $flow      = Flow::findOrFail($data['flow_id']);
            $entry     = Entry::create([
                'title'            => $data['title'],
                'flow_id'          => $data['flow_id'],
                'user_id'          => $authApply->id(),
                'circle'           => 1,
                'status'           => Entry::STATUS_IN_HAND,
                'origin_auth_id'   => Auth::id(),
                'origin_auth_name' => Auth::user()->name,
                'order_no'         => Entry::generateOrderNo($flow->flow_no),
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
        try{
            $this->updateTpl($entry, $data['tpl'] ?? []);
        }catch (DiyException  $exception){
            throw  $exception;
        }
        return $entry;
    }

    public function updateTpl(Entry $entry, $tpl = [])
    {
        $template = $entry->flow->template;
        $forms = $template->template_form;
        foreach ($forms as $form){
            if($form->required == 1){
                $filed = $form->field;
                \Log::debug('input_tpl:' . json_encode($tpl));
                \Log::info('tpl_form:' . empty($tpl[$filed]) . '_' . $filed);

                if(!isset($tpl[$filed]) || empty($tpl[$filed])){
                    throw new  DiyException('请填写' . $form->field_name, ConstFile::API_PARAM_ERROR);
                }
            }
        }
        foreach ($tpl as $k => $v) {
            $val = is_array($v) ? json_encode($v) : $v;
            $val = $val === null ? '' : $val;
            if ('password' == $k) {
                $val = Hash::make($val);
            }

            EntryData::updateOrCreate([
                'entry_id' => $entry->id,
                'field_name' => $k],
                [
                'flow_id'     => $entry->flow_id,
                'field_value' => $val,
                'created_at'  => \Carbon\Carbon::now(),
                'updated_at'  => \Carbon\Carbon::now(),
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
            $this->workflowAuthorityShowData($request);
            return $this->returnApiJson();
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
    }

    public function workflowAuthorityShowData(Request $request)
    {
        try {
            $id          = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $process     = Proc::findUserProcAllStatus($authAuditor->id(), $id);
            /** @var Entry $entry */
            $entry        = Entry::findOrFail($process->entry_id);
            $templateForm = $this->fetchEntryTemplate($entry)->template_form;
            $showData     = $this->fetchShowData($entry->entry_data, $templateForm);

            $comments = TotalComment::query()
                ->where('type', '=', TotalComment::TYPE_WORKFLOW)
                ->where('entry_id', '=', $id)
                ->with(['user'])
                ->get();

            $user       = User::with('fetchPrimaryDepartment')->findOrFail($entry->user_id);
            $userInfo   = [
                'user_id'      => $user->id,
                'avatar'       => $user->fetchAvatar(),
                'name'         => $user->chinese_name,
                'employee_num' => $user->employee_num,
                'position'     => $user->position,
                'department'   => $user->fetchPrimaryDepartment->first()->name,
            ];
            $this->data = [
                'order_no'        => $entry->order_no,
                'show_data'       => $showData,
                'processes'       => $this->fetchEntryProcess($entry),
                'proc'            => $process->toArray(),
                'procs_id'        => $entry->procsFirstNode()->id,
                'comment_for_end' => $comments,   // 整个流程完成之后的评论
                'user_info'       => $userInfo,
                'entry_status'    => $entry->status,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
    }

    public function passWorkflow(Request $request)
    {
        $procsId = $request->get('id');
        try {
            DB::transaction(function () use ($request, $procsId) {
                (new Workflow())->passWithNotify($procsId);
            });
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
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
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * 创建或者修改待入职员工信息
     * @param Request $request
     * @return int
     */
    public function createUser(Request $request)
    {
        $user = $request->get('tpl');
        //$checkEmail = User::where('email', '=', strtolower(trim($user['email'])))->lockForUpdate()->count();
        //throw_if($checkEmail, new Exception("邮箱已存在请重新填写！"));
        $checkMobile = User::where('mobile', '=', strtolower(trim($user['mobile'])))->lockForUpdate()->count();
        throw_if($checkMobile, new Exception("手机号已存在请重新填写！"));
        //验证码错误
        $code=isset($user['code'])?trim($user['code']):'';
        if(!$code){
            throw new Exception("短信验证码不能为空");
        }
        $res = $this->checkUsersCode(trim($user['mobile']), trim($user['code']));
        if (!$res) {
            throw new Exception("短信验证码错误");
        }
        throw_if(!in_array($user['work_type'], UsersDetailInfo::$staffTypeList), new Exception('不存在的工作类型'));

        $insertData['chinese_name'] = trim($user['username']);
        $insertData['mobile']       = trim($user['mobile']);
        //$insertData['email']                = trim($user['email']);
        $insertData['avatar']               = config('user.const.avatar');
        $insertData['password']             = Hash::make($user['password']);
        $insertData['gender']               = trim($user['gender']);
        $department_id                      = trim($user['department_id']);
        $insertData['position']             = trim($user['position']);
        $insertData['status']               = User::STATUS_PENDING_JOIN;
        $insertData['company_id']           = 1;
        $insertData['join_at']              = $user['entry_date'];
        $userDetailData['certificate_type'] = $user['certificate_type'];
        $userDetailData['id_number']        = $user['certificate_number'];
        $workTypeList                       = array_flip(UsersDetailInfo::$staffTypeList);
        $userDetailData['id_number']        = $user['certificate_number'];
        $userDetailData['user_status']      = $workTypeList[$user['work_type']];
        $pendingUsers                       = new User();
        $createUser                         = $pendingUsers->fill($insertData)->save();
        $grantUser                          = User::grantPlainUser($pendingUsers->id);//用户需要有默认的plain_user权限才能使用基本的申请功能
        $userDetailData['user_id']          = $pendingUsers->id;
        $userDetail                         = (new UsersDetailInfo())->fill($userDetailData)->save();
        $leaderCount                        = DepartUser::where('department_id', $department_id)->where('is_leader', DepartUser::DEPARTMENT_LEADER_YES)->count();

        if ($leaderCount && $user['is_leader']) {
            throw new Exception("部门主管已经存在！");
        }

        $departmentUser = (new DepartUser())->fill([
            'user_id'       => $pendingUsers->id,
            'department_id' => $department_id,
            'is_primary'    => DepartUser::DEPARTMENT_PRIMARY_YES,
            'is_leader'     => $user['is_leader'] > 0 ? DepartUser::DEPARTMENT_LEADER_YES : DepartUser::DEPARTMENT_LEADER_NO,
        ])->save();

        if (!$createUser || !$grantUser || !$userDetail || !$departmentUser) {
            throw new Exception("创建用户信息失败！");
        }
        //用户登录并取到token
        $payload = [
            'mobile'   => $user['mobile'],
            'password' => $user['password']
        ];


        if (!$token = JWTAuth::attempt($payload)) {
            throw new Exception('token_not_provided');
        }
        $this->data['token'] = $token;
        //清除缓存
        $key = 'users_' . date('YmdH') . '_' . trim($user['mobile']);
        $this->traitClearCache($key);
        return $pendingUsers->id;
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
            $proObj = $process->proc;
            if (empty($proObj)) {
                continue;
            }
            $temp['finish_time']      = $proObj->finish_at;
            $temp['process_name']     = $process->process_name;
            $temp['auditor_name']     = '';
            $temp['approval_content'] = $proObj ? $process->proc->content : '';

            if ($proObj && $proObj->auditor_name) {
                $temp['auditor_name'] = $proObj->auditor_name;
            } elseif ($proObj && $proObj->user_name) {
                $temp['auditor_name'] = $proObj->user_name;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            $temp['status']      = $proObj ? $process->proc->status : '';
            $temp['status_name'] = '';

            if ($proObj && $proObj->status == Proc::STATUS_REJECTED) {
                $temp['status_name'] = '驳回';
            } elseif ($proObj && $proObj->status == Proc::STATUS_PASSED) {
                $temp['status_name'] = '完成';
            } else {
                $temp['status_name'] = '待处理';
            }
            //所有的 procs 评论
            $comments          = TotalComment::query()
                ->whereIn('type', [TotalComment::TYPE_WORKFLOW, TotalComment::TYPE_AUDIT])//防止id 不唯一  or 走的不是工作流
                ->where('relation_id', $proObj->id)
                ->get();
            $temp['comments']  = $comments;
            $processAuditors[] = $temp;
        }

        if ($entry->status == Entry::STATUS_CANCEL && count($processes) == 1) {
            $temp['process_name']     = '我';
            $temp['auditor_name']     = '我';
            $temp['approval_content'] = '';
            $temp['status']           = Entry::STATUS_CANCEL;
            $temp['status_name']      = '已撤销';
            $processAuditors[]        = $temp;
        }

        return $processAuditors;
    }

    /**
     * @param int $page
     * @param int $flow_id
     * @param string $status
     * @param string $keyword
     * @param int $size
     * @param int $typeId
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function history($page = 1, $flow_id = 0, $status = '', $keyword = '', $size = 20, $typeId = Flow::TYPE_ATTENTION, $user_id = '')
    {
        //need page
        $authAuditor = new AuthUserShadowService();
        $uid         = $authAuditor->id();
        if ($user_id) {
            $uid = $user_id;
        }

        $query = Entry::query();

        if ($flow_id) {
            $query->where('flow_id', '=', $flow_id);

            $flowObj      = Flow::find($flow_id);
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

        $arr        = [];
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
            $entry->status_str  = Entry::$_status[$entry->status];
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
            if (isset($arr['cur_month'])) {
                $arr['cur_month']['title'] = '本月';
            }
            if (isset($arr['left_month'])) {
                $arr['left_month']['title'] = '更早以前';
            }
            $arr['page']       = $entries->currentPage();
            $arr['total_page'] = $entries->total();
        }
        $arr['filter_status'] = Entry::$_status;

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
                    $arr['value']          = $datum->field_value;
                    $arr['key'] = $form->field_name;
                    $info[]                     = $arr;
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
        $entry        = collect($entry->toArray());
        $templateForm = collect($templateForm);
        if ($entry->isEmpty() || $templateForm->isEmpty()) {
            return [];
        }

        $result = [];
        $templateForm->each(function ($item, $key) use ($entry, &$result) {
            $temp    = [];
            $collect = $entry->where('field_name', $item->field);
            if ('department_id' == $item->field) {
                $temp['title'] = $item['field_name'];
                $temp['value'] = Department::findOrFail(($collect->first())['field_value'])->name;
                $result[]      = $temp;
            }
            if (!in_array($item->field, ['div', 'email', 'password', 'department_id']) && $collect->isNotEmpty()) {
                $temp['title'] = $item['field_name'];
                $temp['value'] = ($collect->first())['field_value'];
                $result[]      = $temp;
            }
        });
        return $result;
    }


    public function processQuery(Request $request)
    {
        $flows       = Flow::getFlowsOfNo(); // 流程列表        //我审批过的
        $authAuditor = new AuthUserShadowService();

        $tab = $request->get('tab', 'my_apply');
        if ($tab == 'my_audited') {
            $myAuditedSearchData = $request->except('tab');
            $entries             = Proc::getUserAuditedByPage($authAuditor->id(), $myAuditedSearchData, 10);
            foreach ($entries as $proc) {
                $proc->statusMAP = Entry::STATUS_MAP[$proc->entry->status];
                $proc->url       = route('api.positive.proc') . "?id=" . $proc->id;
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
            $id    = $request->get('id');
            $entry = Entry::findOrFail($id);
            if ($entry->pid > 0) {
                $templateForm = $this->fetchEntryTemplate($entry->parent_entry)->template_form;
                $showData     = $this->fetchShowData($entry->parent_entry->entry_data, $templateForm);

            } else {
                $templateForm = $this->fetchEntryTemplate($entry)->template_form;
                $showData     = $this->fetchShowData($entry->entry_data, $templateForm);
            }
            $this->data = [
                'show_data' => $showData,//申请内容
                'processes' => $this->fetchEntryProcess($entry),//审批记录
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
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
            $id          = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $proc        = Proc::findUserProcAllStatus($authAuditor->id(), $id);

            $entry = Entry::findOrFail($proc->entry_id);
            if ($entry->pid > 0) {
                $templateForm = $entry->parent_entry->flow->template->template_form;
                $showData     = $this->fetchShowData($entry->parent_entry->entry_data, $templateForm);

            } else {
                $templateForm = $this->fetchEntryTemplate($entry)->template_form;
                $showData     = $this->fetchShowData($entry->entry_data, $templateForm);
            }


            $this->data = [
                'show_data' => $showData,//申请内容
                'processes' => $this->fetchEntryProcess($entry),//审批记录
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function fetchBasicInfo()
    {
        try{
            $info = BasicSet::all();
            if($info->count()){
                $this->data = $info->first();
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * @param $entry
     * @param $data
     * @param $mineTaskData
     */
    public function createTaskByEntry($entry, $receiver_ids, $data = [], $mineTaskData = []): void
    {
        $userId                 = Auth::id();
        $data['info']           = $entry->title ?? '';
        $data['create_user_id'] = $userId;
        $data['send_time']      = date('Y-m-d H:i:s', time());
        $task                   = Task::query()->create($data);
        foreach ($receiver_ids as $receiver_id) {
            $mineTaskData['tid']            = $task->id;
            $mineTaskData['create_user_id'] = $userId;
            $mineTaskData['status']         = 1;  //待确认
            $mineTaskData['uid']            = $receiver_id;
            $mineTaskData['pid']            = 0;
            $mineTaskData['type_name']      = User::find($receiver_id)->value('name');
            $mineTaskData['user_type']      = 1;  //接收人类型
            $mineTaskData['start_time']     = date('Y-m-d H:i:s', time());
            $mineTaskData['end_time']       = isset($data['deadline']) ?? date('Y-m-d H:i:s', time());
            $mineTaskData['created_at']     = date('Y-m-d H:i:s', time());
            MyTask::query()->insert($mineTaskData);
        }
    }

    public function fetchPosition(Request $request)
    {
        try {
            $departmentId = $request->get('department_id');
            throw_if(!$departmentId, new Exception('部门ID不能为空'));
            $leaderCount = DepartUser::where('department_id', $departmentId)->where('is_leader', DepartUser::DEPARTMENT_LEADER_YES)->count();
            $positions   = PositionDepartment::with('position')->where('department_id', $departmentId)->get();
            $result      = [];
            $positions->each(function ($item, $key) use (&$result) {
                $result[$key]['name']      = $item->position->name;
                $result[$key]['is_leader'] = $item->position->is_leader;
                $result[$key]['id']        = $item->position->id;
            });
            $this->data = [
                'has_leader'    => $leaderCount > 0 ? 1 : 0,
                'position_list' => $result,
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }

        return $this->returnApiJson();
    }

    protected function showTemplate($flowNo = null)
    {
        try {
            throw_if(!$flowNo, new Exception('流程编号不能为空'));
            $flow         = Flow::findByFlowNo($flowNo);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
                'flow_id'       => $flow->id,
                'template'      => $flow->template->toArray(),
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }

        return $this->returnApiJson();
    }

    //验证审批的请求参数
    public function checkRequest(Request $request)
    {
        $flowId = $request->get('flow_id', 0);
        throw_if(!$flowId, new Exception('缺少必要的参数:流程ID'));
        $flow = Flow::findById($flowId);
        if (Entry::WORK_FLOW_NO_LEAVE_HANDOVER_APPLY == $flow->flow_no) {
            $handOverPerson = $request->get('handover_person');
            $count          = count($handOverPerson);
            throw new Exception(!$count || ($count > 1), new Exception('交接人只能为1个'));
        }
    }
}

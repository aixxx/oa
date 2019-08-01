<?php

namespace App\Repositories\Assets;

use App\Constant\ConstFile;
use App\Constant\CorporateAssetsConstant;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsDepreciation;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Assets\CorporateAssetsValueadded;
use App\Models\Comments\TotalComment;
use App\Models\DepartUser;
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
use App\Repositories\ParentRepository;
use App\Repositories\UsersRepository;
use App\Services\AuthUserShadowService;
use App\Services\WorkflowUserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use DB;
use Hash;
use Auth;
use Tymon\JWTAuth\JWTAuth;

class CorporateAssetsDepreciationRepository extends ParentRepository
{

    public function model()
    {
        return CorporateAssetsValueadded::class;
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
                        $entriesList[$k1]['url'] = route('meeting.meetingreviewedinfo') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), Entry::CUSTOMIZE)) {
                        $entriesList[$k1]['url'] = route('api.flow.customize') . "?id=" . $e['id'];
                    } elseif (in_array(Q($e, 'flow', 'flow_no'), ['intelligence_apply'])) {
                        $entriesList[$k1]['url'] = route('api.inte.flow.show') . "?id=" . $e['id'];
                    } else {
                        $entriesList[$k1]['url'] = route('api.flow.show') . "?id=" . $e['id'];
                    }

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
                    } elseif (in_array(Q($p, 'flow', 'flow_no'), Entry::CUSTOMIZE)) {
                        $processList[$k2]['url'] = route('api.flow.customize.auditor.flow.show') . "?id=" . $p['id'];
                    } else {
                        $processList[$k2]['url'] = route('api.auditor_flow.show') . "?id=" . $p['id'];
                    }

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
            $types = FlowType::with('publish_flow')->where("type_name", "考勤相关")->get();

            if (empty($types) || empty($canSeeFlowIds)) {
                return $this->returnApiJson();
            }

            $this->data = $temp = [];
            foreach ($types as $type) {
                foreach ($type->valid_flow as $flow) {
                    if (in_array($flow->id, $canSeeFlowIds)) {
                        $temp['name'] = $flow->flow_name;
                        if (in_array(Q($flow, 'flow_no'), ['fee_expense', 'finance_loan', 'finance_repayment', 'finance_receivables', 'finance_payment'])) {
                            $temp['url'] = route('api.finance.flow.create', ['flow_id' => $flow->id]);
                        } elseif (in_array(Q($flow, 'flow_no'), ['positive_apply', 'positive_wage_apply'])) {
                            $temp['url'] = route('api.positive.flow.create', ['flow_id' => $flow->id]);
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
     * 待入职人员申请单表单(这时他仍是非系统用户,user表里没有记录)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function showCorporateAssetsDepreciationForm(Request $request)
    {
        try {

            $id = $request->get('id');
            $assets = CorporateAssets::whereIn('id', $id)->where('nature', CorporateAssetsConstant::NATURE_DEPRECIATION_ASSETS)->get();
            $list = $assets->toArray();
            foreach ($list as $key => $item) {
                if ($item['depreciation_method'] == CorporateAssetsConstant::DEPRECIATION_METHOD_LINEAR) {
                    $list[$key]['depreciation_price'] = $this->linear($item['price'], $item['depreciation_cycle'], $item['depreciation_interval'], $item['depreciation_interval']);
                } else {
                    $list[$key]['depreciation_price'] = $this->decrement($item['depreciation_cycle'], $item['buy_time'], $item['remaining_at'], $item['price'], $item['depreciation_interval']);
                }
            }
            $flow = Flow::findByFlowNo(Entry::CORPORATE_ASSETS_DEPRECIATION);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
                'flow_id' => $flow->id,
                'template' => $flow->template->toArray(),
                'default_title' => $defaultTitle,
                'list' => $list
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
        $this->updateFlow($request, 0);
        return $this->returnApiJson();
    }
//
//    public static function storeBySystem(Request $request, $flow_no, $title, $tpl = [])
//    {
//        $flow = Flow::findByFlowNoAnyWay($flow_no);
//        $data = [
//            'flow_id' => $flow->id,
//            'title' => $title,
//            'tpl' => $tpl,
//        ];
//        $request->request = new ParameterBag($data);
//        $self = new self();
//
//        return $self->update($request, 0);
//    }
//
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function workflowShow(Request $request)
    {
        $this->workflowShowData($request);
        return $this->returnApiJson();
    }

    public function workflowShowData(Request $request)
    {
        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $entry = Entry::findUserEntry($authAuditor->id(), $id);
            $templateForm = $this->fetchEntryTemplate($entry)->template_form;
            $showData = $this->fetchShowData($entry->entry_data, $templateForm);
            throw_if(!isset(Entry::$_status[$entry['status']]), new Exception(sprintf('不存在的状态:%s', $entry['status'])));

            $comments = TotalComment::query()
                ->where('type', '=', TotalComment::TYPE_WORKFLOW)
                ->where('entry_id', '=', $id)
                ->with(['user'])
                ->get();

            $this->data = [
                'entry_status' => isset(Entry::$_status[$entry['status']]) ? Entry::$_status[$entry['status']] : '',
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry),
                'procs_id' => $entry->procsFirstNode()->id,
                'comment_for_end' => $comments,   // 整个流程完成之后的评论
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
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

            $receiver_ids = Proc::query()
                ->where('entry_id', '=', $entry->id)
                ->where('status', '!=', Entry::STATUS_FINISHED)
                ->pluck('user_id')->toArray();
            $this->createTaskByEntry($entry, $receiver_ids);

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
        return $entry->id;
    }

    public function createCorporateAssetsDepreciationFlow(Request $request, $user)
    {
        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            $entry = $this->updateOrCreateEntry($request); // 创建或更新申请单
            $data = $request->all();
            $data['tpl']['entry_id'] = $entry->id;
            $corporateAssetsBorrow = $this->createCorporateAssetsDepreciation($data['tpl'], $entry, $user);

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
            $this->data = [];
            Workflow::errLog(' pendinguser EntryCreate', $e->getMessage() . $e->getTraceAsString());
        }
        return $this->returnApiJson();
    }

    /**
     * 更新或插入申请单
     * @param Request $request
     * @param int $id
     * @return Entry|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function updateOrCreateEntry(Request $request, $id = 0)
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

    public function updateTpl(Entry $entry, $tpl = [])
    {
        foreach ($tpl as $k => $v) {
            $val = is_array($v) ? json_encode($v) : $v;
            $val = $val === null ? '' : $val;
            if ('password' == $k) {
                $val = Hash::make($val);
            }

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
        $this->workflowAuthorityShowData($request);
        return $this->returnApiJson();
    }

    public function workflowAuthorityShowData(Request $request)
    {
        try {
            $id = $request->get('id');
            $authAuditor = new AuthUserShadowService();
            $process = Proc::findUserProcAllStatus($authAuditor->id(), $id);
            /** @var Entry $entry */
            $entry = Entry::findOrFail($process->entry_id);
            $templateForm = $this->fetchEntryTemplate($entry)->template_form;
            $showData = $this->fetchShowData($entry->entry_data, $templateForm);

            $comments = TotalComment::query()
                ->where('type', '=', TotalComment::TYPE_WORKFLOW)
                ->where('entry_id', '=', $id)
                ->with(['user'])
                ->get();
            $this->data = [
                'show_data' => $showData,
                'processes' => $this->fetchEntryProcess($entry),
                'proc' => $process->toArray(),
                'procs_id' => $entry->procsFirstNode()->id,
                'comment_for_end' => $comments,   // 整个流程完成之后的评论
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
    }

    public function passWorkflow(Request $request)
    {
        $procsId = $request->get('id');
        try {
            DB::transaction(function () use ($request, $procsId) {
                (new Workflow())->passWithNotify($procsId);
            });
            $proc = Proc::find($procsId);
            $entry = $proc->entry;
            $this->createTaskByEntry($entry, [$entry->user_id]);
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
            $procs = Proc::find($procsId);
            $entry = $procs->entry;
            $this->createTaskByEntry($entry, [$entry->user_id]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
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
        $checkUser = User::where('email', '=', strtolower(trim($user['email'])))->lockForUpdate()->count();
        if ($checkUser) {
            throw new Exception("邮箱已存在请重新填写！");
        }

        $insertData['chinese_name'] = trim($user['username']);
        $insertData['mobile'] = trim($user['mobile']);
        $insertData['email'] = trim($user['email']);
        $insertData['password'] = Hash::make($user['password']);
        $insertData['gender'] = trim($user['gender']);
        $department_id = trim($user['department_id']);

        $insertData['position'] = trim($user['position']);
        $insertData['status'] = User::STATUS_PENDING_JOIN;
        $insertData['work_type'] = $user['work_type'];
        $insertData['company_id'] = $user['company_id'];

        $userDetailData['certificate_type'] = $user['certificate_type'];
        $userDetailData['id_number'] = $user['certificate_number'];

        $pendingUsers = new User();
        $createUser = $pendingUsers->fill($insertData)->save();
        $grantUser = User::grantPlainUser($pendingUsers->id);//用户需要有默认的plain_user权限才能使用基本的申请功能

        $userDetailData['user_id'] = $pendingUsers->id;
        $userDetail = (new UsersDetailInfo())->fill($userDetailData)->save();
        $departmentUser = (new DepartUser())->fill([
            'user_id' => $pendingUsers->id,
            'department_id' => $department_id,
            'is_primary' => DepartUser::DEPARTMENT_PRIMARY_YES
        ])->save();

        if (!$createUser || !$grantUser || !$userDetail || !$departmentUser) {
            throw new Exception("创建用户信息失败！");
        }
        //用户登录并取到token
        $payload = [
            'email' => $user['email'],
            'password' => $user['password']
        ];

        if (!$token = JWTAuth::attempt($payload)) {
            throw new Exception('token_not_provided');
        }
        $this->data['token'] = $token;
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
            $temp['process_name'] = $process->process_name;
            $temp['auditor_name'] = '';
            $temp['approval_content'] = $proObj ? $process->proc->content : '';

            if ($proObj && $proObj->auditor_name) {
                $temp['auditor_name'] = $proObj->auditor_name;
            } elseif ($proObj && $proObj->user_name) {
                $temp['auditor_name'] = $proObj->user_name;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            $temp['status'] = $proObj ? $process->proc->status : '';
            $temp['status_name'] = '';

            if ($proObj && $proObj->status == Proc::STATUS_REJECTED) {
                $temp['status_name'] = '驳回';
            } elseif ($proObj && $proObj->status == Proc::STATUS_PASSED) {
                $temp['status_name'] = '完成';
            } else {
                $temp['status_name'] = '待处理';
            }
            //所有的 procs 评论
            $comments = TotalComment::query()
                ->whereIn('type', [TotalComment::TYPE_WORKFLOW, TotalComment::TYPE_AUDIT])//防止id 不唯一  or 走的不是工作流
                ->where('relation_id', $proObj->id)
                ->get();
            $temp['comments'] = $comments;
            $processAuditors[] = $temp;
        }

        if ($entry->status == Entry::STATUS_CANCEL && count($processes) == 1) {
            $temp['process_name'] = '我';
            $temp['auditor_name'] = '我';
            $temp['approval_content'] = '';
            $temp['status'] = Entry::STATUS_CANCEL;
            $temp['status_name'] = '已撤销';
            $processAuditors[] = $temp;
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
//            $entry->title = $entry->user->chinese_name . '提交的' . $entry->title;
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
        $arr['filter_status'] = Entry::$_status;
        if ($arr) {
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
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
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

    /**
     * @param $data
     * @param $entry
     * @param $user
     * @throws \Throwable
     */
    public function createCorporateAssetsDepreciation($data, $entry, $user)
    {

        $error = $this->checkData($data);
        if ($error) {
            throw new Exception(sprintf('请求参数错误：' . $error), ConstFile::API_RESPONSE_FAIL);
        }
        //这里是权限判断，先保留
        $usersRepository = app()->make(UsersRepository::class);
        $departmentInfo = $usersRepository->getCurrentDept($user);
        DB::transaction(function () use ($data, $user, $departmentInfo) {
            $data['apply_user_id'] = $user->id;
            $data['apply_department_id'] = $departmentInfo['id'];
            $event = CorporateAssetsDepreciation::create($data);
            $assets = CorporateAssets::whereIn('id', $data['assets_id'])->with(['hasOneCorporateAssetsSyncDepreciation'])->get();/**/
            foreach ($assets as $key => $val) {
                if (Q($val, 'status') == CorporateAssetsConstant::ASSETS_STATUS_SCRAPPED) {
                    throw new Exception(sprintf('名称为“%s”的资产已' . CorporateAssetsConstant::$assets_status[Q($val, 'status')], Q($val, 'name')), ConstFile::API_RESPONSE_FAIL);
                }
                if (Q($val, 'hasOneCorporateAssetsSyncDepreciation')) {
                    throw new Exception(sprintf('名称为“%s”的资产已被申请折旧', Q($val, 'name')), ConstFile::API_RESPONSE_FAIL);
                }
                if(Q($val,'depreciation_status') == CorporateAssetsConstant::ASSETS_DEPRECIATION_STATUS_NO){
                    throw new Exception(sprintf('资产“%s”不可折旧', Q($val, 'name')), ConstFile::API_RESPONSE_FAIL);
                }
            }
            //(new CorporateAssetsSync)->whereIn('assets_id', $data['assets_id'])->where('type', CorporateAssetsConstant::ASSETS_RELATION_TYPE_VALUEADDED)->delete();
            //CorporateAssets::query()->whereIn('id',$data['assets_id'])->update(['status'=>CorporateAssetsConstant::ASSETS_STATUS_USING]);
            Collect($data['assets_id'])->each(function ($item, $key) use ($data, $user, $event) {
                $assetsSyncData['apply_user_id'] = $user->id;
                $assetsSyncData['assets_id'] = $item;
                $assetsSyncData['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_DEPRECIATION;
                $assetsSyncData['content_json'] = json_encode(['content' => '资产折旧申请'], JSON_UNESCAPED_UNICODE);
                $assetsSyncData['entry_id'] = $data['entry_id'];
                (new CorporateAssetsSync())->fill($assetsSyncData)->save();
            });
        });
    }

    public function linear($price, $cycle, $interval, $depreciation_interval)
    {
        return sprintf("%.2f", (($price * 0.95) / $cycle / $interval) * $depreciation_interval);
    }

    public function decrement($cycle, $buyDate, $remainingDate, $price, $depreciation_interval)
    {

        $years = intval($cycle / 12);

        $annual_depreciation_rate = sprintf("%.6f", 2 / $years * 1);//年折旧率

        //$monthly_depreciation_rate = sprintf("%.6f", $annual_depreciation_rate / 12);//月折旧率

        $depreciation_amount = 0;//初始化折旧额

        $depreciation_amount_as = $price;//初始化折旧额

        $remainingTime = Carbon::createFromTimeString($remainingDate)->timestamp;

        $buyTime = Carbon::createFromTimeString($buyDate)->timestamp;

        $depreciation_years = intval($remainingTime - $buyTime) / 31536000;

        $depreciation_years = intval($depreciation_years);

        $year = 0;
        do {
            $amount = $depreciation_amount_as * $annual_depreciation_rate;
            $depreciation_amount = $depreciation_amount + $depreciation_amount_as * $annual_depreciation_rate;
            if ($years - $year > 2) {
                $depreciation_amount_as = $depreciation_amount_as - $depreciation_amount;
            } elseif ($years - $year <= 2) {
                $depreciation_amount_as = $depreciation_amount_as / 2;
                return sprintf("%.2f", sprintf("%.6f", $depreciation_amount_as / 12) * $depreciation_interval);
            }
            if ($depreciation_years == $year) {
                return sprintf("%.2f", sprintf("%.6f", $amount / 12) * $depreciation_interval);
            }
            $year++;
        } while ($year < $years);
    }


    public function checkData($data)
    {
        if (empty($data)) {
            return '请求数据不能为空';
        }
        if (!isset($data['num']) || empty($data['num'])) {
            return '折旧单号不能为空';
        }
        if (!isset($data['depreciation_at']) || empty($data['depreciation_at'])) {
            return '请选择折旧时间';
        }
        if (!isset($data['department_id']) || empty($data['department_id'])) {
            return '请选择折旧部门';
        }
        if (!isset($data['user_id']) || empty($data['user_id'])) {
            return '请选择折旧操作人';
        }
        if (!isset($data['assets_id']) || !is_array($data['assets_id']) || empty($data['assets_id'])) {
            return '请选择资产';
        }
        return null;
    }
}

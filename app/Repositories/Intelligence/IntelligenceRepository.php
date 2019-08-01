<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9
 * Time: 13:23
 */

namespace App\Repositories\Intelligence;

use App\Models\Comments\TotalComment;
use App\Models\Intelligence\Intelligence;
use App\Models\Intelligence\IntelligenceType;
use App\Models\Intelligence\IntelligenceUsers;
use App\Models\MyTask\MyTask;
use App\Models\Task\Task;
use App\Models\User;
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
use App\Repositories\ParentRepository;
use DB;

class IntelligenceRepository extends ParentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Intelligence::class;
    }

    public function intelligenceTypeAdd(Request $request)
    {
        try {
            $class = $request->get('name');
            if (empty($class)) {
                throw new Exception('请输入情报分类参数');
            }
            IntelligenceType::create(['class_name' => $class]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function intelligenceTypeEdit(Request $request)
    {
        try {
            $class = $request->get('class_name');
            $class_id = $request->get('class_id');
            if (empty($class_id) || empty($class)) {
                throw new Exception('情报分类参数不可为空');
            }
            IntelligenceType::where('class_id', $class_id)->update(['class_name' => $class]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function intelligenceTypeDelete(Request $request)
    {
        try {
            $class_id = $request->get('class_id');
            if (empty($class_id)) {
                throw new Exception('情报分类参数不可为空');
            }
            IntelligenceType::where('class_id', $class_id)->delete();
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function intelligenceTypeShow()
    {
        try {
            $arr = IntelligenceType::all();
            $this->data = [
                "type" => $arr
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }


    public function IntelligenceAdd(Request $request)
    {
        try {
            $arr = $request->all();
            $arr['user_id'] = Auth::id();
            if (empty($arr)) {
                throw new Exception('情报目标参数不可为空');
            }
            $Inte = Intelligence::create($arr);
            if (isset($arr['deaft']) && !empty($arr['deaft'])) {
                $arr['state'] = Intelligence::STATUS_DEAFT;
            }
            if ($arr['classified'] != 1 && $arr['userId']) {
                foreach ($arr['userId'] as $key => $val) {
                    $arr['inte_id'] = $Inte->id;
                    $arr['user_id'] = $val;
                    $arr['attribute'] = IntelligenceUsers::STATUS_ASSIGN;
                    $res = IntelligenceUsers::create($arr);
                }
                if ($res) {
                    Intelligence::where('id', $Inte->id)->update(['state' => 2]);
                }
                Intelligence::where('id', $Inte->id)->update(['userNum' => count($arr['userId'])]);
            }

        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function IntelligenceShow(Request $request)
    {
        try {
            //条件
            $where = [];
            $title = $request->get('title');
            $classified = $request->get('class', 1);
            $where['classified'] = $classified;
            $classId = $request->get('classId');
            if (!empty($classId) && $classId != 0) {
                $where['class_id'] = $classId;
            }
            $arr = Intelligence::with("hasOneType")
                ->where('state', "!=", '-1')
                ->where($where)
                ->where('title', 'like', '%' . $title . '%')
                ->select(['id', 'title', 'created_at', 'class_id', 'participation', 'state'])->get();
            //数组处理
            $array = $arr->toArray();
            foreach ($array as $key => &$val) {
                if (!empty($val['has_one_type'])) {
                    $val['class_name'] = $val['has_one_type']['class_name'];
                    unset($val['has_one_type']);
                }
            }
            $this->data = [
                "intelligence" => $array
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();

    }

    public function intelligenceAdminShow(Request $request)
    {
        try {
            $state = $request->get('states', 1);
            $arr = Intelligence::with("hasOneType")
                ->where("state", $state)
                ->select(['id', 'title', 'created_at', 'class_id', 'participation', 'state'])->get();
            //数组处理
            $array = $arr->toArray();
            foreach ($array as $key => &$val) {
                if (!empty($val['has_one_type'])) {
                    $val['class_name'] = $val['has_one_type']['class_name'];
                    unset($val['has_one_type']);
                }
            }
            $this->data = [
                "intelligence" => $array
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function intelligenceAdminDetails(Request $request)
    {
        try {
            $inte_id = $request->get('id');
            if (empty($inte_id)) {
                throw new Exception('情报目标参数不可为空');
            }
            $arr = Intelligence::with("hasOneType")
                ->where('id', $inte_id)->first();
            if ($arr->state == 3) {
                $arrUser = IntelligenceUsers::with('hasOneUser')
                    ->where('inte_id', $inte_id)
                    ->where('auditstate', '!=', '-1')->select(['id', 'user_id', 'entry_id', 'updated_at'])->get();
                $arrayUser = $arrUser->toArray();
                foreach ($arrayUser as $key => &$val) {
                    if (!empty($val['has_one_user'])) {
                        $val['name'] = $val['has_one_user']['chinese_name'];
                        unset($val['has_one_user']);
                    }
                }
            }
            //数组处理
            $array = $arr->toArray();
            if (!empty($array['has_one_type'])) {
                $array['class_name'] = $array['has_one_type']['class_name'];
                unset($array['has_one_type']);
            }
            $this->data = [
                "arrayUserd" => isset($arrayUser) ? $arrayUser : "",
                "intelligence" => $array
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function intelligenceMyShow(Request $request)
    {
        try {
            $auditstate = $request->get('states', 1);
            $arr = IntelligenceUsers::with('hasOneInte', 'hasOneInte')->where("user_id", Auth::id())
                ->where('auditstate', $auditstate)->get();
            //数组处理
            $array = $arr->toArray();
            $this->data = [
                "intelligence" => $array
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }

        return $this->returnApiJson();
    }

    public function intelligenceClaim(Request $request)
    {
        try {
            $inteId = $request->get('inteId');
            if (empty($inteId)) {
                throw new Exception('情报目标参数不可为空');
            }
            $data = Intelligence::find($inteId, ['id', 'participation', 'userNum']);
            if ($data->participation <= $data->userNum) {
                throw new Exception('领取人数已达标');
            }
            $arr['user_id'] = Auth::id();
            $arr['inte_id'] = $inteId;
            $arr['attribute'] = IntelligenceUsers::STATUS_CLAIM;
            $arr['state'] = IntelligenceUsers::STATUS_CLAIM;
            $res = IntelligenceUsers::create($arr);
            if ($res) {
                $userNum = ($data->userNum) + 1;
                Intelligence::where('id', $inteId)->update(['userNum' => $userNum]);
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function intelligenceConsent(Request $request)
    {
        try {
            $inteId = $request->get('inteId');
            if (empty($inteId)) {
                throw new Exception('参数不可为空');
            }
            $user_id = Auth::id();
            IntelligenceUsers::where('inte_id', $inteId)->where('user_id', $user_id)->update(['state' => 1]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function intelligenceRefused(Request $request)
    {
        try {
            $inteId = $request->get('inteId');
            $reason = $request->get('content');
            if (empty($inteId)) {
                throw new Exception('参数不可为空');
            }
            if (empty($reason)) {
                throw new Exception('拒绝理由不可为空');
            }
            $user_id = Auth::id();
            IntelligenceUsers::where('inte_id', $inteId)->where('user_id', $user_id)->update(['state' => 2, 'reason' => $reason]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    protected function intelligenceUserShow($inte_id)
    {
        try {
            $user_id = Auth::id();
            $arr = Intelligence::with("hasOneType")->where('id', $inte_id)->first();
            $array = $arr->toArray();
            if (!empty($array['has_one_type'])) {
                $array['class_name'] = $array['has_one_type']['class_name'];
                unset($array['has_one_type']);
            }
            $array['user_id'] = $user_id;

        } catch (Exception $e) {
            return false;
        }
        return $array;
    }

    public function judgeType($status)
    {
        if ($status == Intelligence::STATUS_DEAFT) {
            $userStatus = Intelligence::$type[Intelligence::STATUS_DEAFT];
        } elseif ($status == Intelligence::STATUS_COMPLETE) {
            $userStatus = Intelligence::$type[Intelligence::STATUS_COMPLETE];
        } else {
            $userStatus = Intelligence::$type[Intelligence::STATUS_ONGOING];
        }
        return $userStatus;
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
                        if (in_array(Q($flow, 'flow_no'), ['intelligence_apply'])) {
                            $temp['url'] = route('api.inte.create', ['flow_id' => $flow->id]);
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
    public function showPendingUsersForm(Request $request)
    {
        try {
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_ENTRY_APPlY);
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
            $inte_id = "";
            foreach ($entry->entry_data->toArray() as $value) {
                if ($value['field_name'] == "inte_id") {
                    $inte_id = $value['field_value'];
                }
            }
            isset($inte_id) && !empty($inte_id) ? $list = $this->intelligenceUserShow($inte_id) : $list = "";
            throw_if(!isset(Entry::$_status[$entry['status']]), new Exception(sprintf('不存在的状态:%s', $entry['status'])));
            $comments = TotalComment::query()
                ->where('type', '=', TotalComment::TYPE_WORKFLOW)
                ->where('entry_id', '=', $id)
                ->with(['user'])
                ->get();
            $this->data = [
                'intelligence' => isset($list) ? $list : '',
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
     * 待转正人员申请单表单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show(Request $request)
    {
        try {
            $inte_id = $request->get('id');
            //基础信息
            $list = $this->intelligenceUserShow($inte_id);
            $flow = Flow::findByFlowNo(Entry::WORK_FLOW_NO_INTELLIGENCE_APPLY);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);

            $this->data = [
                "intelligence" => $list,
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

    public function createPendingUserFlow(Request $request)
    {
        try {
            DB::beginTransaction();

            $flow_id = $request->get('flow_id');
            $flow = Flow::findById($flow_id);
            $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
            //根据当前提交的信息生成用户并且自动登录
            $newUserId = $this->createUser($request);
            $entry = $this->updateOrCreateEntry($request); // 创建或更新申请单
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
            //写入数据到快照表
            $quickShotData = [
                'apply_user_id' => $newUserId,
                'user_id' => $newUserId,
                'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_PENDING_ENTRY,
                'content_json' => json_encode(['content' => '已提交入职申请,进入待入职状态'], JSON_UNESCAPED_UNICODE),
                'entry_id' => $entry->id
            ];
            $res = (new WorkflowUserSync())->fill($quickShotData)->save();
            throw_if(!$res, new Exception('同步待入职数据失败', ConstFile::API_RESPONSE_FAIL));
            DB::commit();

            $employeeNum = 100000 + $newUserId;//工号生成规则
            User::findOrFail($newUserId)->setAttribute('employee_num', $employeeNum)->save();
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
        $data['file_source'] = 'intelligence_apply';
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
            /**************审批驳回， 发送通知******************/
            Message::addProc(Proc::find($procsId), Message::MESSAGE_TYPE_WORKFLOW_REJECT);

            
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

    private
    function fetchEntryTemplateForLeave()
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

    /**
     * @param $entry
     * @param $data
     * @param $mineTaskData
     */
    public function createTaskByEntry($entry, $receiver_ids, $data = [], $mineTaskData = []): void
    {
        $userId = Auth::id();
        $data['info'] = $entry->title ?? '';
        $data['create_user_id'] = $userId;
        $data['send_time'] = date('Y-m-d H:i:s', time());
        $task = Task::query()->create($data);
        foreach ($receiver_ids as $receiver_id) {
            $uname = User::find($receiver_id)->value('name');
            $mineTaskData['tid'] = $task->id;
            $mineTaskData['create_user_id'] = $userId;
            $mineTaskData['status'] = 1;  //待确认
            $mineTaskData['uid'] = $receiver_id;
            $mineTaskData['pid'] = 0;
            $mineTaskData['type_name'] = $uname;
            $mineTaskData['user_type'] = 1;  //接收人类型
            $mineTaskData['start_time'] = date('Y-m-d H:i:s', time());
            $mineTaskData['end_time'] = isset($data['deadline']) ?? date('Y-m-d H:i:s', time());
            $mineTaskData['created_at'] = date('Y-m-d H:i:s', time());
            MyTask::query()->insert($mineTaskData);
        }
    }


    protected function showTemplate($flowNo = null)
    {
        try {
            throw_if(!$flowNo, new Exception('流程编号不能为空'));
            $flow = Flow::findByFlowNo($flowNo);
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
}
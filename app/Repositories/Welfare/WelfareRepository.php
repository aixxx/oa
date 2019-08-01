<?php

namespace App\Repositories\Welfare;

use App\Models\Welfare;
use App\Models\WelfareReceiver;
use App\Models\Workflow\WorkflowRole;
use App\Repositories\ParentRepository;
use App\Repositories\UsersRepository;
use App\Services\Workflow\FlowCustomize;
use App\Services\WorkflowUserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\AuthUserShadowService;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\FlowType;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Models\Workflow\WorkflowUserSync;
use Laravel\Passport\Bridge\UserRepository;
use Prettus\Repository\Eloquent\BaseRepository;

use App\Constant\ConstFile;
use App\Models\User;
use Exception;
use Auth;
use DB;


/**
 * Class UsersRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WelfareRepository extends ParentRepository
{
    /**
     * @var UsersRepository
     */
    protected $userRespository;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Welfare::class;
    }

    public function __construct()
    {
        $this->userRespository = app()->make(UsersRepository::class);

    }

    /**
     *部长角色-福利列表
     */
    public function getList($uid, $perPage = 10)
    {
        $list = [];

        $this->data = Welfare::where('promoter', $uid)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return $this->returnApiJson();
    }

    /**
     *普通员工-福利列表
     */
    public function personList($uid, $perPage = 10)
    {
        $list = [];
        $ids = WelfareReceiver::where('user_id', $uid)->pluck('welfare_id')->toArray();
        $this->data = Welfare::where('status', Welfare::WELFARE_STATUS_COMPLETED)
            ->whereIn('id', $ids)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

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
            $user = Auth::user();

            //$user_id = Auth::id();
            $departs = $this->userRespository->getDeptAllChild($user, Q($user, 'departUserPrimary', 'department_id'));
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            $role = WorkflowRole::firstRoleByName('行政');
            if (Q($role, 'roleUser')) {
                $roleUsers = $role->roleUser->pluck('user_chinese_name', 'user_id')->toArray();
            }

            $this->data = [
                'roleUsers' => $roleUsers,
                'departs' => $departs,
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
    public function updateFlow(Request $request, $id = '')
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
            unset($entry->flow);
            unset($entry->user);
            unset($entry->procs);

            $this->data = ['entry' => $entry->toArray()];
        } catch (Exception $e) {
            DB::rollback();
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            Workflow::errLog('EntryUpdate', $e->getMessage() . $e->getTraceAsString());
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
        $data = $request->all();
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

        $data['tpl']['entry_id'] = $entry->id;
        $this->updateWelfare($data['tpl']);

        return $entry;
    }

    public function updateWelfare($data = [])
    {

        try {
            DB::transaction(function () use ($data) {
                $welfareData = [
                    'title' => $data['title'],
                    'promoter' => $data['promoter'],
                    'entries_id' => $data['entry_id'],
                    'content' => $data['content'],
                    'status' => Welfare::WELFARE_STATUS_PROCESSING ,
                    'condition_methods' => $data['condition_methods'],
                    'issuer' => $data['issuer'],
                    'startdate' => $data['startdate'],
                    'enddate' => $data['enddate'],
                ];

                $welfareReceiver = $data['users'];

                $res = Welfare::create($welfareData);

                $detailDeatail = Collect($welfareReceiver)->each(function ($item, $key) use ($res) {
                    WelfareReceiver::create([
                            'welfare_id' => $res->id,
                            'minister' => $res->promoter,
                            'user_id' => $item,
                            'status' => WelfareReceiver::WELFARE_RECEIVER_STATUS_PENDING_APPLY,
                            'is_draw' => WelfareReceiver::WELFARE_RECEIVER_YES_DRAW,
                        ]
                    );

                });

            });
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
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
            $entryData = Q($entry, 'entry_welfare');
            if ($entryData) {
                $entryData->promoter_chinese_name = Q($entryData, 'promoterUser', 'chinese_name');
                $entryData->promoter_dept = Q($entryData, 'promoterUser', 'primaryDepartUser', 'department', 'name');
                $entryData->issuer_chinese_name = Q($entryData, 'issuerUser', 'chinese_name');
                $entryData->issuer_dept = Q($entryData, 'issuerUser', 'primaryDepartUser', 'department', 'name');
                unset($entryData->promoterUser);
                unset($entryData->issuerUser);
                $entryData = $entryData->toArray();
            }
            $this->data = [
                'is_auditor' => 0,
                'entry' => $entryData,
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
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function personShow(Request $request)
    {
        try {
            $id = $request->get('id');
            $obj = Welfare::find($id);
            $objItem = WelfareReceiver::where(['welfare_id' => $id, 'user_id' => Auth::id()])->select('status', 'is_draw')->first();
            if ($obj) {
                $obj->promoter_chinese_name = Q($obj, 'promoterUser', 'chinese_name');
                $obj->promoter_dept = Q($obj, 'promoterUser', 'primaryDepartUser', 'department', 'name');
                $obj->issuer_chinese_name = Q($obj, 'issuerUser', 'chinese_name');
                $obj->issuer_dept = Q($obj, 'issuerUser', 'primaryDepartUser', 'department', 'name');
                $obj->receiver_status = Q($objItem, 'status');
                $obj->is_draw = Q($objItem, 'is_draw');
                unset($obj->promoterUser);
                unset($obj->issuerUser);
                $entryData = $obj->toArray();
            }
            $this->data = [
                'is_auditor' => 0,
                'entry' => $obj,
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
            $entryData = Q($entry, 'entry_welfare');
            if ($entryData) {
                $entryData->promoter_chinese_name = Q($entryData, 'promoterUser', 'chinese_name');
                $entryData->promoter_dept = Q($entryData, 'promoterUser', 'primaryDepartUser', 'department', 'name');
                $entryData->issuer_chinese_name = Q($entryData, 'issuerUser', 'chinese_name');
                $entryData->issuer_dept = Q($entryData, 'issuerUser', 'primaryDepartUser', 'department', 'name');
                unset($entryData->promoterUser);
                unset($entryData->issuerUser);
                $entryData = $entryData->toArray();
            }

            $this->data = [
                'is_auditor' => 1,
                'entry' => $entryData,
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
                $pro = Proc::find($request->get('id'));
                if ($pro) {//判断是否是最后一步，是则更新福利的表的状态为已完成
                    $flowlink = Flowlink::where(['flow_id' => $pro->flow_id, 'process_id' => $pro->process_id])->first();
                    if (Q($flowlink, 'next_process_id') == Flowlink::LAST_FLOW_LINK) {
                        Welfare::where(['status' => Welfare::WELFARE_STATUS_PROCESSING, 'entries_id' => $pro->entry_id])->update(['status' => Welfare::WELFARE_STATUS_COMPLETED]);
                    }
                }

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


    /**
     * 领取人列表
     */
    public function receiverList(Request $request, $perPage=10)
    {
        $list = [];
        $welfare_id = $request->input('welfare_id', '');
        $is_draw = $request->input('is_draw', ConstFile::WELFARE_RECEIVER_YES_DRAW);
        $minister=$request->input('promoter', '');
        $status=$request->input('status','');
        $build= WelfareReceiver::where([
            'welfare_id' => $welfare_id,
            'minister'=>$minister,
            'is_draw' => $is_draw
        ]);
        if($status){
            $build->where('status',$status);
        }
        $list=$build->where('status','<>',WelfareReceiver::WELFARE_RECEIVER_STATUS_PENDING_APPLY)->with('user')->orderBy('id', 'desc')->paginate($perPage);
        if($is_draw!=1){
            $statusList=[];
        }else{
            $statusList=[
                ConstFile::WELFARE_RECEIVER_STATUS_PROCESSING=> '申请中',
                ConstFile::WELFARE_RECEIVER_STATUS_COMPLETED=> '申请通过',
                ConstFile::WELFARE_RECEIVER_STATUS_REJECT=> '申请拒绝',
            ];
        }

        $this->data=[
            'list'=>$list,
            'statusList'=>$statusList
        ];
        return $this->returnApiJson();

    }
    public function apply(Request $request){

        try {
            DB::transaction(function () use ($request) {
                $welfare_id = $request->input('welfare_id', '');
                $user_id=Auth::id();
                WelfareReceiver::where([
                    'welfare_id'=>$welfare_id,
                    'user_id'=>$user_id,
                    'status'=>WelfareReceiver::WELFARE_RECEIVER_STATUS_PENDING_APPLY
                ])->update(['status'=>WelfareReceiver::WELFARE_RECEIVER_STATUS_PROCESSING]);
                $obj=WelfareReceiver::where([
                    'welfare_id'=>$welfare_id,
                    'user_id'=>$user_id,
                ])->first();
                if($obj){
                    $data=$obj->toArray();
                }else{
                    $data=[] ;
                }
                $this->data = [
                    'data' =>$data,
                ];
            });
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();

    }


}

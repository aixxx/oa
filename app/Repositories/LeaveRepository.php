<?php

namespace App\Repositories;

use App\Models\Workflow\WorkflowUserSync;
use Mockery\Exception;
use App\Models\Workflow\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Constant\ConstFile;
use App\Models\User;
use Auth;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Workflow;
use Illuminate\Container\Container as Application;

class LeaveRepository extends ParentRepository
{
    public function model()
    {
        return Entry::class;
    }

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function showFireUsersForm(Request $request)
    {
        try {
            $defaultTitle = '开除员工' . (new Carbon())->toDateString();

            $user_id = $request->get('id', 0);
            $user    = User::with('fetchPrimaryDepartment')->findOrFail($user_id);

            if ((empty($user->fetchPrimaryDepartment))) {
                throw new Exception('用户没有所属部门');
            }

            $template = [
                [
                    "field"       => "user_id",
                    "field_name"  => "用户ID",
                    "field_type"  => "div",
                    "field_value" => $user_id,
                ],
                [
                    "field"       => "user_name",
                    "field_name"  => "用户姓名",
                    "field_type"  => "div",
                    "field_value" => $user->chinese_name,
                ],
                [
                    "field"       => "main_department",
                    "field_name"  => "所属部门",
                    "field_type"  => "div",
                    "field_value" => $user->fetchPrimaryDepartment->first()->name,
                ],
                [
                    "field"       => "position",
                    "field_name"  => "职位",
                    "field_type"  => "div",
                    "field_value" => $user->position,
                ],
                [
                    "field"       => "employee_num",
                    "field_name"  => "工号",
                    "field_type"  => "div",
                    "field_value" => $user->employee_num,
                ],
                [
                    "field"       => "leave_date",
                    "field_name"  => "离职日期",
                    "field_type"  => "date",
                    "field_value" => '',
                ],
                [
                    "field"       => "leave_reason",
                    "field_name"  => "离职原因",
                    "field_type"  => "text",
                    "field_value" => '',
                ],
                [
                    "field"       => "leave_memo",
                    "field_name"  => "离职原因备注",
                    "field_type"  => "text",
                    "field_value" => '',
                ],
            ];

            $this->data = [
                'template'      => $template,
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }

        return $this->returnApiJson();
    }

    public function storeFireUserForm(Request $request)
    {
        try {
            $array = [
                'leave_date'   => $request->get('leave_date'),
                'leave_reason' => $request->get('leave_reason'),
                'leave_memo'   => $request->get('leave_memo')
            ];
            $data  = [
                'apply_user_id' => Auth::id(),
                'user_id'       => $request->get('user_id'),
                'status'        => ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_UNDER_HAND_OVER,
                'content_json'  => json_encode($array, JSON_UNESCAPED_UNICODE)
            ];
            $user  = User::findOrFail($request->get('user_id'));
            throw_if(!$user, new Exception(sprintf('ID为%s的用户不存在', $request->get('user_id'))));
            throw_if(User::STATUS_JOIN != $user->status, new Exception(sprintf('ID为%s的用户目前不是在职状态', $request->get('user_id'))));
            $result = (new WorkflowUserSync)->fill($data)->save();
            throw_if(!$result, new Exception('开除员工:同步数据失败'));
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }

        return $this->returnApiJson();
    }

    public function showLeaveHandOverForm()
    {
        try {
            $count = Workflow::fetchUserEntryByFlowNo(Auth::id(), [Entry::WORK_FLOW_NO_ACTIVE_LEAVE_APPlY], [Entry::STATUS_FINISHED]);
            throw_if(!$count, new Exception('请首先通过离职流程'));
            $countWait = Workflow::fetchUserEntryByFlowNo(Auth::id(), [Entry::WORK_FLOW_NO_LEAVE_HANDOVER_APPLY], [Entry::STATUS_FINISHED, Entry::STATUS_IN_HAND]);
            throw_if($countWait, new Exception('不能重复发起交接流程'));
            $flow         = Flow::findByFlowNo(Entry::WORK_FLOW_NO_LEAVE_HANDOVER_APPLY);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
                'flow_id'       => $flow->id,
                'template'      => $this->fetchTemplateWithCustomData($flow->template->toArray(), Workflow::getApplyerBasicInfo()),
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }

        return $this->returnApiJson();
    }

    public function showActiveLeaveForm()
    {
        try {
            $flow         = Flow::findByFlowNo(Entry::WORK_FLOW_NO_ACTIVE_LEAVE_APPlY);
            $defaultTitle = $flow->flow_name . (new Carbon())->toDateString();
            Workflow::generateHtmlForApi($flow->template);
            $this->data = [
                'flow_id'       => $flow->id,
                'template'      => $this->fetchTemplateWithCustomData($flow->template->toArray(), Workflow::getApplyerBasicInfo()),
                'default_title' => $defaultTitle
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }

        return $this->returnApiJson();
    }

    public function checkCanApplyEntry($userId)
    {
        try {
            $result     = Workflow::fetchUserEntryByFlowNo($userId);
            $this->data = ['status' => $result > 0 ? 1 : 0];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code    = $e->getCode();
        }

        return $this->returnApiJson();
    }
}
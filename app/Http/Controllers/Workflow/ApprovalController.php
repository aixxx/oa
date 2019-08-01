<?php
/**
 * Created by PhpStorm.
 * User: qsq_lipf
 * Date: 18/8/22
 * Time: 下午5:23
 */

namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Dh;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Workflow;
use App\Services\Attendance\WorkflowService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * 审核管理首页
     * @return $this
     */
    public function index()
    {
        //已发布，未废弃的签卡申请流程
        $published_workflows = [
            Entry::WORK_FLOW_NO_HOLIDAY,
            Entry::WORK_FLOW_NO_ATTENDANCE_OVERTIME,
            Entry::WORK_FLOW_NO_ATTENDANCE_BUSINESS_TRAVEL,
            Entry::WORK_FLOW_NO_ATTENDANCE_RETROACTIVE,
            Entry::WORK_FLOW_NO_ATTENDANCE_RESUMPTION,
        ];
        $flows = Workflow::whereIn('flow_no', $published_workflows)->where('is_publish', 1)->where('is_abandon', 0)->get();

        return view('workflow.approval.index')->with(compact('flows'));
    }


    /**
     * 审批列表
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return $this
     */
    public function flow(Request $request, $id)
    {
        $request = $request->all();

        if (!isset($request['id'])) {
            $request['id'] = $id;
        }
        //已发布，未废弃的签卡申请流程
        $flow = Workflow::findOrFail($request['id']);

        $flow_no  = $flow->flow_no;
        $begin_at = null;
        $end_at   = null;
        $status   = null;
        $entry_id = 0;
        $user_id  = 0;

        if (isset($request['start_at'])) {
            $begin_at = Dh::getDateStart($request['start_at'], false);
        }
        if (isset($request['end_at'])) {
            $end_at = Dh::getDateEnd($request['end_at'], false);
        }
        if (isset($request['status'])) {
            $status = $request['status'];
        }
        if (isset($request['chinese_name'])) {
            $user = User::where('chinese_name', trim($request['chinese_name']))->first();
            if ($user) {
                $user_id = $user->id;
            } else {
                $user_id = '空';
            }
        }
        if (isset($request['entry_id'])) {
            $entry_id = $request['entry_id'];
        }

        $data = Workflow::getFlowUserDataV1($flow_no, $begin_at, $end_at, $status, $entry_id, $user_id);
        arsort($data);
        return view('workflow.approval.flow')->with(compact('flow', 'data', 'request'));
    }

    /**
     * 审批列表
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return $this
     */
    public function flowExport(Request $request, $id)
    {
        set_time_limit(0);

        $request = $request->all();

        if (!isset($request['id'])) {
            $request['id'] = $id;
        }
        //已发布，未废弃的签卡申请流程
        $flow = Workflow::findOrFail($request['id']);

        $flow_no  = $flow->flow_no;
        $begin_at = null;
        $end_at   = null;
        $status   = null;
        $entry_id = 0;
        $user_id  = 0;

        if (isset($request['start_at'])) {
            $begin_at = Dh::getDateStart($request['start_at'], false);
        }
        if (isset($request['end_at'])) {
            $end_at = Dh::getDateEnd($request['end_at'], false);
        }
        if (isset($request['status'])) {
            $status = $request['status'];
        }
        if (isset($request['chinese_name'])) {
            $user = User::where('chinese_name', trim($request['chinese_name']))->first();
            if ($user) {
                $user_id = $user->id;
            } else {
                $user_id = '空';
            }
        }
        if (isset($request['entry_id'])) {
            $entry_id = $request['entry_id'];
        }

        $data = Workflow::getFlowUserDataV1($flow_no, $begin_at, $end_at, $status, $entry_id, $user_id);
        arsort($data);

        switch ($flow_no) {
            case Entry::WORK_FLOW_NO_HOLIDAY :  //请假
                WorkflowService::exportLeave($data);
                break;
            case Entry::WORK_FLOW_NO_ATTENDANCE_RETROACTIVE :  //签卡
                WorkflowService::exportRetroactive($data);
                break;
            case Entry::WORK_FLOW_NO_ATTENDANCE_BUSINESS_TRAVEL :  //出差
                WorkflowService::exportTravel($data);
                break;
            case Entry::WORK_FLOW_NO_ATTENDANCE_OVERTIME :  //加班
                WorkflowService::exportOvertime($data);
                break;
            case Entry::WORK_FLOW_NO_ATTENDANCE_RESUMPTION :  //销假
                WorkflowService::exportResumption($data);
                break;
        }
    }
}
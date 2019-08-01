<?php
namespace App\Services;

use App\Models\Workflow\WorkflowTask;
use Illuminate\Support\Facades\DB;

/**
 * 工作流相关任务模块服务
 * User: aike
 * Date: 2018/7/20
 * Time: 下午3:44
 */

class WorkflowTaskService
{
    const TASK_TYPE_EXPENSES_REIMBURSE = 'FEEEXP'; //费用报销类型
    const TASK_TYPE_HOLIDAY_APPLY = 'HOLIDAY'; //请假申请

    private $taskKey; // 任务唯一key
    private $workflowTask; // 工作流任务

    public function __construct($taskKey)
    {
        $this->taskKey = $taskKey;
        $this->workflowTask = WorkflowTask::where([
            'task_key' => $this->taskKey
        ])
        ->first();
    }

    /**
     * 设置任务为成功状态
     * @return bool
     */
    public function setWorkflowTaskSuccess($response)
    {
        return $this->workflowTask->update([
                'response'    => $response,
                'task_status' => WorkflowTask::STATUS_SUCCESS,
                'exec_times'  => DB::raw('exec_times + 1'),
                'updated_at'  => time()
            ]);
    }

    /**
     * 设置任务为失败状态
     * @return bool
     */
    public function setWorkflowTaskFail($response)
    {
        return $this->workflowTask->update([
                'response'    => $response,
                'task_status' => WorkflowTask::STATUS_FAIL,
                'exec_times'  => DB::raw('exec_times + 1'),
                'updated_at'  => time()
            ]);
    }

    /**
     * 执行任务
     * @param $execHook
     */
    public function exec($execHook)
    {
        $this->workflowTask->update([
            'task_status' => WorkflowTask::STATUS_IN_HAND, // 任务标记为处理中
        ]);

        // 执行逻辑
        $res = $execHook($this->workflowTask);

        // 设置任务为成功状态
        $this->setWorkflowTaskSuccess($res);
    }

    /**
     * 保存回调数据
     * @param $callbackRes
     */
    public function saveCallBackRes($callbackRes)
    {
        $this->workflowTask->update([
            'call_back_res' => $callbackRes, // 任务标记为处理中
        ]);
    }

    public function proc()
    {
        return $this->workflowTask->proc;
    }

    /**
     * 生成任务唯一key
     * @param $taskType
     * @param $flowId
     * @param $entryId
     * @param $procId
     */
    public static function generateTaskKey($taskType, $flowId, $entryId, $procId)
    {
        return sprintf('%s%06d%08d%08d', $taskType, $flowId, $entryId, $procId);
    }

    /**
     * 创建工作流的任务
     * @param int $execTimes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function createWorkflowTask($taskKey, $entryId, $procId, $execTimes = 1)
    {
        return WorkflowTask::create([
            'task_key'    => $taskKey,
            'entry_id'    => $entryId,
            'proc_id'     => $procId,
            'exec_times'  => $execTimes,
            'task_status' => WorkflowTask::STATUS_NEW, // 创建任务的时候初始化为新建
        ]);
    }
}

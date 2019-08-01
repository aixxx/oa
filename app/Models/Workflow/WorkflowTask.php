<?php
namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Workflow\WorkflowTask
 *
 * @property int $id
 * @property string $task_key 任务唯一编码
 * @property string|null $request 任务数据，Json格式
 * @property string $response 任务执行结果
 * @property int $entry_id 工作流单号
 * @property int $proc_id 审批流程节点id
 * @property int $task_status 当前状态,0:处理中;1:处理成功;2:处理失败;3:新建
 * @property int $exec_times 任务执行的次数
 * @property string $queue_name 推送的队列名
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereExecTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereProcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereQueueName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereTaskKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereTaskStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $call_back_res 回调数据记录
 * @property-read \App\Models\Workflow\Proc $proc
 * @property-read \App\Models\Workflow\Entry $entry
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowTask whereCallBackRes($value)
 */
class WorkflowTask extends Model
{
    const STATUS_IN_HAND           = 0; // 处理中
    const STATUS_SUCCESS           = 1; // 处理成功
    const STATUS_FAIL              = 2; // 处理失败
    const STATUS_NEW               = 3; // 新建
    const STATUS_REMARK_AS_SUCCESS = 4; // 标记为成功

    protected $table = "workflow_task";

    protected $fillable = ['task_key', 'request', 'response', 'entry_id', 'proc_id', 'task_status', 'exec_times', 'queue_name', 'call_back_res'];

    public function proc()
    {
        return $this->belongsTo('\App\Models\Workflow\Proc', 'proc_id', 'id');
    }

    public function entry()
    {
        return $this->belongsTo(\App\Models\Workflow\Entry::class, 'entry_id', 'id');
    }

    public function getStatusDesc()
    {
        switch ($this->task_status) {
            case self::STATUS_IN_HAND:
                return '处理中';
            case self::STATUS_SUCCESS:
                return '处理成功';
            case self::STATUS_REMARK_AS_SUCCESS:
                return '标记为成功';
            case self::STATUS_FAIL:
                return '处理失败';
            case self::STATUS_NEW:
                return '新建';
            default:
                return '未定义';
        }
    }

    public static function getListByStatus($status, $before_date)
    {
        if (!is_array($status)) {
            $status = [$status];
        }
        return self::whereIn('task_status', $status)->where('created_at', '<', $before_date)->orderBy('task_status')->paginate(20);
    }
}

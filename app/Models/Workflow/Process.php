<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Workflow\Process
 *
 * @property int $id
 * @property int $flow_id
 * @property string $process_name
 * @property int $limit_time 限定时间,单位秒
 * @property string $type 流程图显示操作框类型
 * @property string $icon 流程图显示图标
 * @property string $process_to
 * @property string $style
 * @property string $style_color
 * @property string $style_height
 * @property string $style_width
 * @property string $position_left
 * @property string $position_top
 * @property int $position 步骤位置
 * @property int $child_flow_id 子流程id
 * @property int $child_after 子流程结束后 1.同时结束父流程 2.返回父流程
 * @property int $child_back_process 子流程结束后返回父流程进程
 * @property string $description 步骤描述
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $can_merge 是否可以做节点合并，1:可以;0:不可以
 * @property string $pass_events 节点审批通过后触发的事件，以逗号分割
 * @property string $reject_events 节点审批拒绝后触发的事件，以逗号分割
 * @property-read \App\Models\Workflow\Flow $flow
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereCanMerge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereChildAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereChildBackProcess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereChildFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereLimitTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process wherePassEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process wherePositionLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process wherePositionTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereProcessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereProcessTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereRejectEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereStyleColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereStyleHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereStyleWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Process whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\ProcessVar[] $process_var
 */
class Process extends Model
{
    const CAN_MERGE_YES = 1; // 节点可以合并
    const CAN_MERGE_NO  = 0; // 节点不可以合并

    protected $table = "workflow_processes";

    protected $fillable = [
        'flow_id',
        'process_name',
        'limit_time',
        'type',
        'icon',
        'description',
        'style',
        'style_color',
        'style_height',
        'style_width',
        'position_left',
        'position_top',
        'position',
        'child_flow_id',
        'child_after',
        'child_back_process',
        'can_merge',
        'pass_events',
        'reject_events',
    ];

    public function flow()
    {
        return $this->belongsTo('App\Models\Workflow\Flow', 'flow_id');
    }

    public function process_var()
    {
        return $this->hasMany('App\Models\Workflow\ProcessVar', 'process_id');
    }

    /**
     * 节点审批通过需要触发的事件
     * @return array
     */
    public function getPassEvents()
    {
        return empty($this->pass_events) ? [] : explode(',', $this->pass_events);
    }

    /**
     * 拒绝申请触发的事件
     * @return array
     */
    public function getRejectEvents()
    {
        return empty($this->reject_events) ? [] : explode(',', $this->reject_events);
    }
}

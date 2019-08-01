<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Workflow\Flowlink
 *
 * @property int $id
 * @property int $flow_id
 * @property string $type
 * @property int $process_id
 * @property int $next_process_id
 * @property string $auditor
 * @property string $depands
 * @property string $expression
 * @property int $sort
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Workflow\Process $next_process
 * @property-read \App\Models\Workflow\Process $process
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereAuditor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereExpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereFlowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereNextProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\Flowlink whereDepands($value)
 */
class Flowlink extends Model
{
    const TYPE_SYS       = 'Sys'; // 按层级选择领导人审批
    const TYPE_CONDITION = 'Condition'; // 按照条件选择
    const TYPE_ROLE      = 'Role'; // 角色
    const TYPE_DEPT      = 'Dept'; //按部门
    const TYPE_EMP       = 'Emp'; // 指定人员

    const LAST_FLOW_LINK = -1;//最后一步

    protected $table = "workflow_flow_links";

    protected $fillable = ['flow_id', 'type', 'process_id', 'next_process_id', 'status', 'auditor', 'expression', 'sort', 'depands'];

    public function process()
    {
        return $this->belongsTo('App\Models\Workflow\Process', 'process_id');
    }

    public function next_process()
    {
        return $this->belongsTo('App\Models\Workflow\Process', 'next_process_id');
    }

    /**
     * 获取角色信息
     * @param $processId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function firstRoleLink($processId)
    {
        $roleLink = self::where([
            'process_id' => $processId,
            'type'       => self::TYPE_ROLE,
        ])->first();

        return $roleLink;
    }


    /**
     * 获取非condition信息
     * @param $processId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function firstNotConditionLink($processId)
    {
        $roleLink = self::where('type', '!=', Flowlink::TYPE_CONDITION)
            ->where('process_id', $processId)
            ->first();
        return $roleLink;
    }

    /**
     * 获取condition信息
     * @param $processId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function firstConditionLink($processId)
    {
        return self::where('type', Flowlink::TYPE_CONDITION)->where('process_id', $processId)->first();
    }

    /**
     * 获取condition信息
     * @param $processId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function countConditionLink($processId)
    {
        return self::where('type', Flowlink::TYPE_CONDITION)->where('process_id', $processId)->count();
    }

    /**
     * 获取所有condition信息
     * @param $processId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static[]
     */
    public static function getConditionLink($processId)
    {
        return self::where('type', Flowlink::TYPE_CONDITION)->where('process_id', $processId)->get();
    }

    /**
     * 查看是否自动选人
     * @param $process_id
     * @return Model|null|object|static
     * @author hurs
     */
    public static function firstSysLink($process_id)
    {
        return Flowlink::where('type', Flowlink::TYPE_SYS)->where('process_id', $process_id)->first();
    }

    /**
     * 查看是否指定人审批
     * @param $process_id
     * @return Model|null|object|static
     * @author hurs
     */
    public static function firstEmpLink($process_id)
    {
        return Flowlink::where('type', Flowlink::TYPE_EMP)->where('process_id', $process_id)->first();
    }

    /**
     * 是否按部门审批
     * @param $process_id
     * @return Model|null|object|static
     * @author hurs
     */
    public static function firstDeptLink($process_id)
    {
        return Flowlink::where('type', Flowlink::TYPE_DEPT)->where('process_id', $process_id)->first();
    }

    /**
     * 看第一步是否指定审核人
     * @param $flow_id
     * @return Model|null|object|static
     * @author hurs
     */
    public static function firstStepLink($flow_id)
    {
        return Flowlink::where(['flow_id' => $flow_id, 'type' => self::TYPE_CONDITION])->whereHas('process', function ($query) {
            $query->where('position', 0);
        })->orderBy("sort", "ASC")->first();

    }

    public function NestIsLast()
    {
        return $this->next_process_id == self::LAST_FLOW_LINK;
    }
}

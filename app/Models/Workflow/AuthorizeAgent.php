<?php

namespace App\Models\Workflow;

use App\Http\Helpers\Dh;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Workflow\AuthorizeAgent
 *
 * @property int $id
 * @property int $authorizer_user_id 授权人id
 * @property string $authorizer_user_name 授权人姓名
 * @property string|null $authorize_valid_begin 代理权限开始时间
 * @property string|null $authorize_valid_end 代理权限结束时间
 * @property string $flow_no 代理审批的流程
 * @property int $agent_user_id 代理人id
 * @property string $agent_user_name 代理人姓名
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $agent
 * @property-read \App\Models\Workflow\Flow $flow
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereAgentUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereAgentUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereAuthorizeValidBegin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereAuthorizeValidEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereAuthorizerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereAuthorizerUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereFlowNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\AuthorizeAgent onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\AuthorizeAgent whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\AuthorizeAgent withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\AuthorizeAgent withoutTrashed()
 */
class AuthorizeAgent extends Model
{
    use SoftDeletes;
    protected $table = "workflow_authorize_agent";
    const FULL_AGENT_FLOW_NO = '';//当是0时，说明授权是全流程可用

    protected $fillable = [
        'authorizer_user_id',
        'authorizer_user_name',
        'authorize_valid_begin',
        'authorize_valid_end',
        'flow_no',
        'agent_user_id',
        'agent_user_name',
    ];

    public function flow()
    {
        return $this->belongsTo('App\Models\Workflow\Flow', "flow_no", "flow_no");
    }

    public function agent()
    {
        return $this->belongsTo('App\Models\User', 'agent_user_id');
    }

    /**
     * 获取代理审批人代理信息
     * @param array $auditor_ids
     * @param       $flow_no
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getValidAgents(array $auditor_ids, $flow_no = self::FULL_AGENT_FLOW_NO)
    {
        $today = Dh::todayDate();
        return static::select(DB::raw('agent_user_id,agent_user_name,flow_no,GROUP_CONCAT(authorizer_user_id) authorizer_user_id,GROUP_CONCAT(authorizer_user_name) authorizer_user_name'))
            ->with('agent')
            ->whereIn('authorizer_user_id', $auditor_ids)
            ->whereNotIn('agent_user_id', $auditor_ids)// 这里过滤掉既有审批权,同时又代理当前审批人的信息
            ->where('authorize_valid_begin', '<=', $today)
            ->where('authorize_valid_end', '>=', $today)
            ->whereIn('flow_no', [$flow_no, self::FULL_AGENT_FLOW_NO])
            ->groupBy('agent_user_id', 'flow_no')
            ->get();
    }

    /**
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author hurs
     */
    public static function getUserAgents($user_id)
    {
        return static::with('flow')->where('authorizer_user_id', $user_id)->where('authorize_valid_end', '>=', now())->orderBy('id', 'desc')->get();
    }

    public static function deleteUserAgent($user_id, $id)
    {
        return AuthorizeAgent::where('authorizer_user_id', $user_id)->where('id', $id)->delete();
    }
}

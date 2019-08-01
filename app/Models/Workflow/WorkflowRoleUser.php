<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Workflow\WorkflowRoleUser
 *
 * @property int $id
 * @property int $creater_user_id 创建人id
 * @property string $creater_user_chinese_name 创建人用户名
 * @property int $role_id 角色id
 * @property int $user_id 用户id
 * @property string $user_chinese_name 用户中文名
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Workflow\WorkflowRole $role
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\WorkflowRoleUser onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereCreaterUserChineseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereCreaterUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereUserChineseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRoleUser whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\WorkflowRoleUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\WorkflowRoleUser withoutTrashed()
 * @mixin \Eloquent
 */
class WorkflowRoleUser extends Model
{
    use SoftDeletes; // 逻辑删

    protected $table="workflow_role_user";

    protected $fillable=['role_id', 'user_id', 'user_chinese_name', 'creater_user_id', 'creater_user_chinese_name'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', "user_id");
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Workflow\WorkflowRole', 'role_id');
    }

    public static function getRoleUsersByRole($roleId)
    {
        return self::where('role_id', $roleId)->get();
    }

    public static function getByRoleAndUser($roleId, $userId)
    {
        return self::where('role_id', $roleId)
            ->where('user_id',$userId)->first();
    }

    public static function deleteById($id)
    {
        self::where('id', $id)->first()->delete();
    }
}

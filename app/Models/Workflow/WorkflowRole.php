<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Workflow\WorkflowRole
 *
 * @property int $id
 * @property string $role_name 角色中文名
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $company_id 负责的公司,0:表示全部公司
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Workflow\WorkflowRoleUser[] $roleUser
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\WorkflowRole onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRole whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRole whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRole whereRoleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Workflow\WorkflowRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\WorkflowRole withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Workflow\WorkflowRole withoutTrashed()
 * @mixin \Eloquent
 */
class WorkflowRole extends Model
{
    const ROLE_SEAL_DEFAULT_MANAGE = '印章默认转出人';
    use SoftDeletes; // 逻辑删

    protected $table = "workflow_role";

    protected $fillable = ['role', 'role_name', 'company_id'];

    public function roleUser()
    {
        return $this->hasMany('App\Models\Workflow\WorkflowRoleUser', 'role_id');
    }

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public static function roleList()
    {
        return self::orderBy('id')->get();
    }

    /**
     * 获取所有角色信息
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getRoles()
    {
        return self::get();
    }

    /**
     * 删除角色以及对应的人员
     * @param $id
     */
    public static function deleteById($id)
    {
        DB::beginTransaction();
        $role = self::where('id', $id)->first();
        $role->roleUser()->delete();
        $role->delete();
        DB::commit();
    }

    public static function getRoleUserByIds($role_ids)
    {
        return WorkflowRole::with('roleUser')->whereIn('id', $role_ids)->get();
    }

    public static function firstRoleByName($name)
    {
        return WorkflowRole::where('role_name', $name)->firstOrFail();
    }

    /**
     * 按照公司id过滤角色,company_id为0表示适应所有公司
     * @param $companyId
     * @param $role_ids
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getCompanyRoleUserByIds($companyId, $role_ids)
    {
        $query = WorkflowRole::with('roleUser')
            ->where('company_id', 0)
            ->whereIn('id', $role_ids);

        return WorkflowRole::with('roleUser')
            ->whereRaw("find_in_set($companyId,company_id)")
            ->whereIn('id', $role_ids)
            ->union($query)
            ->get();
    }
}

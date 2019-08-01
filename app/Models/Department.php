<?php

namespace App\Models;

use App\Models\AttendanceApi\AttendanceApiDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use \Exception;
use DevFixException;

/**
 * App\Models\Department
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Department[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereUpdatedAt($value)
 * @property int|null $deepth
 * @property int $is_sync_wechat 是否要同步企业微信
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereDeepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereIsSyncWechat($value)
 * @property int $auto_id
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Department onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Department whereAutoId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Department withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Department withoutTrashed()
 */
class Department extends Model
{
    const SYNC_WECHAT_YES = 1; //要同步企业微信
    const SYNC_WECHAT_NO = 0; //不用同步企业微信
    const ROOT_DEPARTMENT_ID = 1; //艾克智能OA的部门ID
    const ROOT_DEPARTMENT_PARENT_ID = 0; //艾克智能OA的parent ID
    const FIRST_LEVEL_DEPARTMENT_PARENT_ID = 1; //一级部门parent ID
    const PRIMARY = 1; //主部门

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'auto_id';
    public $fillable = [
        'id',
        'name',
        'parent_id',
        'order',
        'is_sync_wechat',
        'deepth',
    ];

    public function children()
    {
        return $this->hasMany('App\Models\Department', 'parent_id', 'id');
    }


    public function users()
    {
        //return $this->belongsToMany('\App\Models\User')->withPivot('is_leader', 'is_primary');
        return $this->belongsToMany('\App\Models\User', 'department_user', 'department_id', 'user_id', 'id', 'id')->withPivot('is_leader', 'is_primary');
    }

    public function userInfo()
    {
        //return $this->belongsToMany('\App\Models\User')->withPivot('is_leader', 'is_primary');
        return $this->belongsToMany('\App\Models\User', 'department_user', 'department_id', 'user_id', 'id', 'id')->withPivot('is_leader', 'is_primary')
            ->where("status",User::STATUS_JOIN)
            ->select('users.id', 'chinese_name', 'avatar', 'employee_num', 'join_at', 'position');
    }


    public function userInfoPrimary()
    {
        //return $this->belongsToMany('\App\Models\User')->withPivot('is_leader', 'is_primary');
        return $this->belongsToMany('\App\Models\User', 'department_user', 'department_id', 'user_id', 'id', 'id')
            ->where('is_primary', self::PRIMARY)
            ->where("status",User::STATUS_JOIN)
            ->groupBy('users.id')
            ->select('users.id', 'chinese_name', 'avatar', 'employee_num', 'join_at', 'position');
    }

    public function tags()
    {
        //return $this->belongsToMany('App\Models\Tag');
        return $this->belongsToMany('App\Models\Tag', 'department_tag', 'department_id', 'tag_id', 'id', 'id');
    }

    /**
     * @param       $deptId
     * @param array $pathName
     *
     * @return string 获取部门全路径
     */
    public static function getDeptPath($deptId, &$pathName = [])
    {
        $deptInfo = Department::find($deptId);

        if (!$deptInfo) {
            return "";
        }
        array_unshift($pathName, $deptInfo->name);
        if ($deptInfo->parent_id != 0) {
            self::getDeptPath($deptInfo->parent_id, $pathName);
        }
        if ($pathName) {
            $path = implode('/', $pathName);
            unset($pathName);
        } else {
            $path = "";
        }
        return $path;
    }

    /**
     * 获取全部子部门编号
     *
     * @param integer $deptId
     * @param array $pathName
     *
     * @return string 获取部门全路径
     */
    public static function getAllChildrenDept($deptId, &$children = [])
    {
        array_push($children, $deptId);
        $departments = Department::where('parent_id', $deptId)->get(['id']);

        if ($departments) {
            foreach ($departments as $key => $department) {
                self::getAllChildrenDept($department->id, $children);
            }
        }

        return $children;
    }

    /**
     * 获取部门名称
     * @param $id
     * @return mixed|string
     */
    public static function getDepartmentName($id)
    {
        $departName = Department::select('name')->where('id', '=', $id)->first();
        return $departName ? $departName->name : "";
    }

    /**
     * @param        $departmentId
     * @param string $order
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function fetchDepartmentWithUsersTree($departmentId, $order = 'order')
    {
        return Department::with('users')->where('parent_id', $departmentId)->latest($order)->get();
    }

    public static function fetchLeafDepartmentsWithAllPath()
    {
        return self::all()->whereNotIn('parent_id', [self::ROOT_DEPARTMENT_PARENT_ID, self::FIRST_LEVEL_DEPARTMENT_PARENT_ID])->transform(
            function ($item, $key) {
                return ['id' => $item->id, 'name' => self::getDeptPath($item->id)];
            }
        )->pluck('name', 'id');
    }

    /**
     * 获取二级部门(即1级部门艾克智能OA的子部门)
     * @param $dateTime
     * @return array
     */
    public static function getSecondDepartment($dateTime = null)
    {
        return $dateTime ? self::withTrashed()
            ->where('parent_id', Department::FIRST_LEVEL_DEPARTMENT_PARENT_ID)
            ->where(function ($query) use ($dateTime) {
                $query->where(function ($query) use ($dateTime) {
                    $query->whereNull('deleted_at')->whereDate('created_at', '<=', $dateTime);
                })->orWhere(function ($query) use ($dateTime) {
                    $query->whereDate('deleted_at', '>=', $dateTime)->whereDate('created_at', '<=', $dateTime);
                });
            })->orderBy('order', 'desc')->get() : self::where('parent_id', self::FIRST_LEVEL_DEPARTMENT_PARENT_ID)
            ->get(['name', 'id'])->pluck('name', 'id');
    }

    public static function getByIds($departIds, $includeDel = true)
    {
        if ($includeDel) {
            return self::withTrashed()->whereIn('id', $departIds)->get();
        } else {
            return self::whereIn('id', $departIds)->get();
        }
    }

    /**
     * 获取一段时间的所有部门
     * @param $dateTime
     * @return mixed
     */
    public static function getAllDepartmentByTime($dateTime)
    {
        return self::withTrashed()->where(function ($query) use ($dateTime) {
            $query->where(function ($query) use ($dateTime) {
                $query->whereNull('deleted_at')->whereDate('created_at', '<=', $dateTime);
            })->orWhere(function ($query) use ($dateTime) {
                $query->whereDate('deleted_at', '>=', $dateTime)->whereDate('created_at', '<=', $dateTime);
            });
        })->get();

//        $sql = "select * from departments d right join (select max(auto_id) as aid from departments where created_at<='$dateTime' group by id) d1 on d1.aid = d.auto_id";
//        return DB::select($sql);
    }

    /**
     * 因为将原本的主键Id换成了auto_id,所以重写find方法，保持原来的代码不变
     * @param $depart_id
     * @return $this
     */
    public static function find($depart_id)
    {
        return Department::where('id', '=', $depart_id)->first();
    }

    public static function getByDepartId($depart_id)
    {
        return Department::withTrashed()->where('id', '=', $depart_id)->first();
    }

    public static function getByDepartAutoIds($departAutoIds)
    {
        return Department::withTrashed()->whereIn('auto_id', $departAutoIds)->get();
    }

    /**
     * 因为将原本的主键Id换成了auto_id,所以重写findOrFail方法，保持原来的代码不变
     * @param $depart_id
     * @return mixed
     * @throws Exception
     */
    public static function findOrFail($depart_id)
    {
        $department = Department::where('id', '=', $depart_id)->first();
        if (!$department) {
            throw new DevFixException('找不到该部门!');
        }
        return $department;
    }

    /**
     * 生成departId,原来部门Id是主键不用，现在要自己生成
     * 生成规则：查询所有记录最大的id(departId,包含deleted_at的记录)加1，为新建部门的Id
     */
    public static function generateDepartId()
    {
        return Department::withTrashed()->orderBy('id', 'desc')->first()->id + 1;
    }

    public static function isHaveChildren($departmentId, $chooseTime)
    {
        return Department::withTrashed()->where('parent_id', $departmentId)->where(function ($query) use ($chooseTime) {
            $query->where(function ($query) use ($chooseTime) {
                $query->whereNull('deleted_at')->whereDate('created_at', '<=', $chooseTime);
            })->orWhere(function ($query) use ($chooseTime) {
                $query->whereDate('deleted_at', '>=', $chooseTime)->whereDate('created_at', '<=', $chooseTime);
            });
        })->get();
    }

    public static function fetchUserDepartmentIdByTime($userId, $time)
    {
        $sql = "select department_id from department_user d right join (select max(id) as aid from department_user where created_at<= ? and user_id= ? and `is_primary`= ?  group by department_id order by created_at DESC) d1 on d1.aid = d.id";
        $department = DB::select($sql, [$time, $userId, DepartUser::DEPARTMENT_PRIMARY_YES]);
        return $department ? collect($department)->first()->department_id : 0;
    }

    public static function getChildrenIds($departmentId, $chooseTime)
    {
        return self::withTrashed()->where('parent_id', $departmentId)
            ->where(function ($query) use ($chooseTime) {
                $query->where(function ($query) use ($chooseTime) {
                    $query->whereNull('deleted_at')->whereDate('created_at', '<=', $chooseTime);
                })->orWhere(function ($query) use ($chooseTime) {
                    $query->whereDate('deleted_at', '>=', $chooseTime)->whereDate('created_at', '<=', $chooseTime);
                });
            })->get();
    }


    public static function findByName($name)
    {
        return self::where('name', $name)->orderByDesc('auto_id')->first();
    }

    /**
     * 无用户状态下获取部门列表
     * @param int $departmentId
     * @return \App\Models\Department[]|\Illuminate\Database\Eloquent\Collection
     */

    public static function fetchDepartmentList($departmentId = Department::ROOT_DEPARTMENT_ID)
    {
        return Department::find($departmentId)->children;
    }

    /**
     *   关联考勤组
     */
    public function attendanceDepartment()
    {
        return $this->hasOne(AttendanceApiDepartment::class, 'department_id', 'id')->select(['department_id', 'attendance_id']);
    }

    public function attendanceDepartmentUser()
    {
        return $this->hasMany(DepartUser::class, 'department_id', 'id')->select(['department_id', 'user_id']);
    }

    public static function fetchAllParentId()
    {
        return self::select(['parent_id'])->distinct()->get()->implode('parent_id',',');
    }
}

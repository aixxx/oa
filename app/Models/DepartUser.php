<?php

namespace App\Models;

use App\Http\Helpers\Dh;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\DepartUser
 *
 * @property int $id
 * @property int $department_id
 * @property int $user_id
 * @property int $is_leader
 * @property int|null $is_primary
 * @property-read \App\Models\Department $department
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Department[] $masterDeparts
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartUser whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartUser whereIsLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartUser whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartUser whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartUser whereDeletedAt($value)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DepartUser onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DepartUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DepartUser withoutTrashed()
 */
class DepartUser extends Model
{
    const DEPARTMENT_LEADER_YES = 1; //是否是部门领导－是
    const DEPARTMENT_LEADER_NO  = 0; //是否是部门领导－否

    const DEPARTMENT_PRIMARY_YES = 1; //是否是主部门－是
    const DEPARTMENT_PRIMARY_NO  = 0; //是否是主部门－否

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'department_user';

    public $fillable = ['department_id', 'user_id', 'is_leader', 'is_primary'];


    public function masterDeparts()
    {
        return $this->hasManyThrough('App\Models\Department', 'App\Models\User', 'pri_dept_id', 'id', 'user_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->where('status',User::STATUS_JOIN);
    }

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public static function createOrUpdateNewDepartUser($data, $departUser = false)
    {
        if (!$departUser) {
            $departUser = new DepartUser();
        }
        $departUser->fill($data);
        $departUser->save();
        return $departUser;
    }

    /**
     * 获取部门领导下的所有部门编号
     * @param $userId
     *
     * @return array
     */
    public static function getDepartmentsByLeader($userId)
    {
        $allDapartment = [];
        $dapartments   = DepartUser::join('departments', 'department_id', 'departments.id')
            ->where('user_id', '=', $userId)
            ->where('is_leader', '=', self::DEPARTMENT_LEADER_YES)
            ->get();

        collect($dapartments)->map(function ($entry) use (&$allDapartment, &$node) {

            $allDapartment[] = $entry->id;

            $allDapartment = self::getChildDepartment($allDapartment, $entry);
        });

        return $allDapartment;
    }

    private static function getChildDepartment($tree, $entry)
    {
        $dapartments = Department::where('parent_id', '=', $entry->id)->get();

        collect($dapartments)->map(function ($entry) use (&$tree, &$node) {
            $tree[] = $entry->id;

            $tree = self::getChildDepartment($tree, $entry);
        });

        return $tree;
    }

    /**
     * 如果查询不包含历史数据，则$chooseTime和$includeDel为false
     * @param $departId
     * @param $userId
     */
    public static function getByDepartIdAndUserId($departId, $userId, $chooseTime = 1, $includeDel = 1)
    {
        if ($includeDel && $chooseTime) {
            if ($chooseTime == 1) {//如果不传时间则默认当前时间
                $chooseTime = Dh::now();
            }
            $departUser = DepartUser::withTrashed()->where('department_id', $departId)
                ->where('user_id', $userId)
                ->where(function ($query) use ($chooseTime) {
                    $query->where(function ($query) use ($chooseTime) {
                        $query->whereNull('deleted_at')->where('created_at', '<=', $chooseTime);
                    })->orWhere(function ($query) use ($chooseTime) {
                        $query->where('deleted_at', '>=', $chooseTime)->where('created_at', '<=', $chooseTime);
                    });
                })->first();
        } else {
            $departUser = DepartUser::where('department_id', $departId)
                ->where('user_id', $userId)->first();
        }
        return $departUser;
    }


    /**
     * @param $departId
     */
    public static function getByDepartId($departId)
    {
        $departUsers = DepartUser::where('department_id', $departId)->get();
        return $departUsers;
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getByUserId($userId)
    {
        $departUsers = DepartUser::where('user_id', $userId)->get();
        return $departUsers;
    }

    /**
     * 包含软删记录
     * @param $departId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getByDepartIdIncludeHistory($departId)
    {
        $sql = "select * from department_user d right join (select max(id) as aid from department_user where department_id=$departId group by user_id) d1 on d1.aid = d.id";
        return DB::select($sql);
    }

    /**
     * 根据用户ID和时间获取历史记录(保含软删记录)
     * @param $userId
     * @param $time
     */
    public static function getDepartIdsByUserIdAndTime($userId = null, $time)
    {
        if ($userId == null) {
            $department = self::withTrashed()->where(function ($query) use ($time) {
                $query->where(function ($query) use ($time) {
                    $query->whereNull('deleted_at')->where('created_at', '<=', $time);
                })->orWhere(function ($query) use ($time) {
                    $query->where('deleted_at', '>=', $time)->where('created_at', '<=', $time);
                });
            })->get()->pluck('department_id');
        } else {
            $department = self::withTrashed()->where('user_id', $userId)->where(function ($query) use ($time) {
                $query->where(function ($query) use ($time) {
                    $query->whereNull('deleted_at')->where('created_at', '<=', $time);
                })->orWhere(function ($query) use ($time) {
                    $query->where('deleted_at', '>=', $time)->where('created_at', '<=', $time);
                });
            })->get()->pluck('department_id');
        }
        return $department;
    }

    /**
     * 获取指定时间的记录
     * @param null $userId
     * @param $time
     * @return mixed
     */
    public static function getDepartIdsByTime($time)
    {
        return self::withTrashed()->where(function ($query) use ($time) {
            $query->where(function ($query) use ($time) {
                $query->whereNull('deleted_at')->where('created_at', '<=', $time);
            })->orWhere(function ($query) use ($time) {
                $query->where('deleted_at', '>=', $time)->where('created_at', '<=', $time);
            });
        })->get();
    }

    public static function checkUserIsLeader($userId)
    {
        return DepartUser::where('user_id', $userId)->where('is_leader', self::DEPARTMENT_LEADER_YES)->first();
    }

    /**
     * 获取在职员工主部门
     */
    public function getPrimaryDepartmentA()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id')->select(['id','name']);
    }


    /*
     * 获取指定部门的子部门，包含自身
     * */
    public static function getDepartChild($departId, $type=0)
    {
        $allDapartment = [];
        if($type){
            //包含自身
            $dapartments = Department::whereIn('parent_id', $departId)->orWhereIn('id', $departId)->distinct()->get();
        }else{
            $dapartments = Department::whereIn('parent_id', $departId)->distinct()->get();
        }

        collect($dapartments)->map(function ($entry) use (&$allDapartment, &$node) {

            $allDapartment[] = $entry->id;

            $allDapartment = self::getChildDepartment($allDapartment, $entry);
        });

        return $allDapartment;
    }


    /*
     * 获取指定部门下的员工
     * */
    public static function getDepartUsers($departId, $uids=array()){
        $data = DepartUser::whereIn('department_id', $departId)->select('department_id','user_id')->distinct()->orderBy('user_id');

        if($uids){
            $data->orWhereIn('user_id',$uids);
        }

        return $data;
    }
    /**
     * 获取部门领导
     */
    public static function getPrimaryLeader($department_id){
        $lerderInfo=[];
        $departUser=DepartUser::where('department_id', '=', $department_id)
            ->where('is_leader', '=', self::DEPARTMENT_LEADER_YES)
            ->where('is_primary', '=', DepartUser::DEPARTMENT_PRIMARY_YES)
            ->first();
        if(Q($departUser,'user')){
            $lerderInfo=[
                'id'=>Q($departUser,'user','id'),
                'chinese_name'=>Q($departUser,'user','chinese_name'),
                'mobile'=>Q($departUser,'user','mobile'),
                'position'=>Q($departUser,'user','position'),
                'avatar'=>Q($departUser,'user','avatar'),
                'join_at'=>Q($departUser,'user','join_at'),
            ];

        }
        return $lerderInfo;
    }
}

<?php

namespace App\Models;

//use http\Env\Request;
use App\Http\Helpers\Dh;
use App\Models\Attendance\AttendanceWorkClass;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Models\Workflow\WorkflowRole;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Overtrue\Pinyin\Pinyin;
use PHPUnit\Framework\MockObject\Stub\Exception;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use UserFixException;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property int $id 内部系统uid
 * @property string $name 系统唯一账号名
 * @property int $employee_num 员工编号KNxxxxxx
 * @property string $chinese_name 中文名
 * @property string $english_name 英文名
 * @property string $email 邮箱
 * @property string $company_id 公司id
 * @property string $mobile 手机号
 * @property string $position 职位
 * @property string|null $avatar 头像
 * @property int $gender 性别(1.男;2.女;0.未知)
 * @property int $isleader 是否高管
 * @property string|null $telephone 固定电话
 * @property string|null $password 密码
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $join_at 入职时间
 * @property string|null $regular_at 转正时间
 * @property string|null $leave_at 离职时间
 * @property int $status 员工状态
 * @property \Carbon\Carbon|null $deleted_at
 * @property string|null $remember_token 唯一token
 * @property int $is_sync_wechat 是否要同步企业微信 0：不同步， 1：同步
 * @property string|null $work_address 工作地点
 * @property string|null $work_type 班值类型
 * @property string|null $work_title 班值代码(P01、P02等)
 * @property int $superior_leaders 上级领导
 * @property-read \Illuminate\Database\Eloquent\Collection|\Silber\Bouncer\Database\Ability[] $abilities
 * @property-read \App\Models\Certificate $certificate
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DepartUser[] $departUser
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Department[] $departments
 * @property-read \App\Models\UsersDetailInfo $detail
 * @property-read \App\Models\UserSpecific $user_specific
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Silber\Bouncer\Database\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserLog[] $userLog
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereChineseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmployeeNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEnglishName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIs($role)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsAll($role)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsNot($role)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsSyncWechat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsleader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereJoinAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLeaveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRegularAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSuperiorLeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereWorkAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withoutTrashed()
 * @mixin \Eloquent
 * @property string $id_card_no 身份证号
 * @property string $bank_card_no 银行卡号
 * @property-read \App\Models\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBankCardNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIdCardNo($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance\AttendanceSheet[] $attendance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance\AttendanceCheckinout[] $checkinout
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserBankCard[] $bankCard
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserFamily[] $family
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserUrgentContact[] $urgentUser
 * @property-read \App\Models\Attendance\AttendanceWhite $white
 * @property-read \App\Models\Attendance\AttendanceWorkClass $workClass
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereWorkTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereWorkType($value)
 * @property-read \App\Models\DepartUser $privateDepartUser
 * @property string|null $password_modified_at 密码修改日期
 * @property string|null $password_tips 密码提示
 * @property int $hrbp_id HRBP 员工id
 * @property-read \App\Models\DepartUser $primaryDepartUser
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereHrbpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePasswordModifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePasswordTips($value)
 * @property-read \App\Models\UserBankCard $mainBank
 * @property-read \App\Models\UserBankCard $viceBank
 * @property int|null $cumulative_length 累计工龄
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User findByEmail($email)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCumulativeLength($value)
 */
class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes, Notifiable, HasRolesAndAbilities, ValidatesRequests;

    const DEFAULT_TIME = '0000-00-00 00:00:00'; //默认时间
    const GENDER_MALE = 1;   //男
    const GENDER_FEMALE = 2;   //女
    const GENDER_UNKNOWN = 0;   //未知

    const STATUS_PENDING_JOIN = -1;   //待入职
    const STATUS_JOIN = 1;   //在职
    const STATUS_LEAVE = 9;   //离职

    const STATUS_ISNO_POSITIVE = 1;   //待转正
    const STATUS_IS_POSITIVE = 2;   //转正中

    const STATUS_ISNO_WAGE = 1;   //设置中
    const STATUS_IS_WAGE = 2;   //转正设置成功
    public static $growthrecord = [
        self::STATUS_JOIN => '新入职',
        self::STATUS_IS_WAGE => '转正',
    ];

    const IS_LEADER_YES = 1; //高管
    const IS_LEADER_NO = 0; //非高管


    const WORK_TYPE_SCHEDULE = 1; //客服排班制
    const WORK_TYPE_REGULAR = 2; //常日职能制
    const WORK_TYPE_ELASTIC = 3; //技术弹性制

//    //员工类型
//    const USER_TYPE_FULL_TIME = 'full-time'; //全职
//    const USER_TYPE_PART_TIME = 'part-time'; //兼职
//    const USER_TYPE_INTERNSHIP = 'internship'; //实习
//    const USER_TYPE_LABOR_DISPATCH = 'labor-dispatch'; //劳务派遣
//    const USER_TYPE_HIRE_RETIRED = 'hire-retired'; //退休返聘
//    const USER_TYPE_LABOR_OUTSOURCING = 'labor-outsourcing'; //劳务外包
//    const USER_TYPE_COUNSELOR = 'counselor'; //顾问

    //员工状态
    const USER_STATUE_REGULAR = 'regular'; //正式员工
    const USER_STATUE_UN_REGULAR = 'non-regular'; //非正式员工

    const PART_CHECK_PEOPLE_NUM = 1630; //部分员工不校验完整表单 ，之后用户进行表单校验

    //权限（ability）
    const ABILITY_USERS_SETTING_SHOW = 'users_setting_show';

    //角色(roles)
    const ROLES_ADMINISTRATOR = 'administrator';
    const ROLES_HR_MANAGER = 'HR_manager';

    //

    const ABILITY_USERS_SETTING_DELETE = 'users_setting_delete'; //用户信息删除


    /**
     * @deprecated 用户合同状态
     */
    const USERS_CONTRACT_STATUS_UNSIGNED = -1;//合同已签
    const USERS_CONTRACT_STATUS_SIGNED = 1;//合同未签
    public static $usersContractStatus = [
        self::USERS_CONTRACT_STATUS_UNSIGNED => '未签',
        self::USERS_CONTRACT_STATUS_SIGNED => '已签',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'chinese_name',
        'english_name',
        'mobile',
        'employee_num',
        'position',
        'gender',
        'telephone',
        'company_id',
        'status',
        'name',
        'is_sync_wechat',
        'join_at',
        'leave_at',
        'work_address',
        'isleader',
        'work_type',
        'work_title',
        'regular_at',
        'password_modified_at',
//        'superior_leaders',
        'is_positive',
        'is_person_perfect',
        'is_card_perfect',
        'is_edu_perfect',
        'is_pic_perfect',
        'is_family_perfect',
        'is_urgent_perfect',
        'work_name',
        'is_wage',
        'avatar'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['deleted_at'];

    public function departments()
    {
        return $this->belongsToMany('App\Models\Department', 'department_user', 'user_id', 'department_id', 'id', 'id')
            ->withPivot('is_leader');
    }

    public function growth()
    {
        return $this->hasMany('App\Models\GrowthRecode', 'user_id', 'id')
            ->orderBy('created_at', 'decs');
    }

    public function primaryDepartment()
    {
        return $this->belongsToMany('App\Models\Department', 'department_user', 'user_id', 'department_id', 'id', 'id')
            ->withPivot('is_leader');
    }

    public function fetchPrimaryDepartment()
    {
        return $this->belongsToMany('App\Models\Department', 'department_user', 'user_id', 'department_id', 'id', 'id')
            ->withPivot('is_primary')
            ->where('is_primary', '=', DepartUser::DEPARTMENT_PRIMARY_YES);
    }


    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag');
    }

    public function certificate()
    {
        return $this->hasOne('App\Models\Certificate');
    }


    /**
     * Hash password
     *
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function getRegularAtAttribute($value)
    {
        return $value ? date('Y-m-d', strtotime($value)) : '';
    }


    public function detail()
    {
        return $this->hasOne('App\Models\UsersDetailInfo', 'user_id', 'id');
    }

//->select([
//'user_id', 'id_name', 'gender', 'ethnic', 'born_time', 'id_card_number',
//'validity_certificate', 'id_address', 'id_detailed_address', 'address',
//'detailed_address', 'census_type', 'marital_status', 'politics_status',
//'first_work_time', 'per_social_account', 'per_fund_account', 'highest_education',
//'graduate_institutions', 'graduate_time', 'major', 'bank', 'branch_bank'
//]);

    public function describe()
    {
        return $this->hasOne('App\Models\Describe\Describe', 'user_id', 'id');
    }

    /**
     * 用户合同
     * @return $this
     */
    public function contract()
    {
        return $this->hasMany('App\Models\Contract\Contract', 'user_id', 'id')->orderBy('created_at', 'decs')->limit(1);
    }

    /**
     * 用户主卡
     * @return $this
     */
    public function mainBank()
    {
        return $this->hasOne('App\Models\UserBankCard', 'user_id', 'id')->where('bank_type', UserBankCard::BANK_CARD_TYPE_MAIN);
    }

    /**
     * 用户副卡
     * @return $this
     */
    public function viceBank()
    {
        return $this->hasOne('App\Models\UserBankCard', 'user_id', 'id')->where('bank_type', UserBankCard::BANK_CARD_TYPE_VICE);
    }


    public function user_specific()
    {
        return $this->hasOne(\App\Models\UserSpecific::class, 'user_id', 'id');
    }

    //    public function masterDepart()
    //    {
    //        return $this->hasOne('App\Models\Department','id','pri_dept_id');
    //    }

    public function departUser()
    {
        return $this->hasMany('App\Models\DepartUser', 'user_id', 'id');
    }

    public function primaryDepartUser()
    {
        return $this->hasOne('App\Models\DepartUser', 'user_id', 'id')
            ->where('is_primary', DepartUser::DEPARTMENT_PRIMARY_YES);
    }


    public function userLog()
    {
        return $this->hasMany('App\Models\UserLog', 'target_user_id', 'id');
    }

    /**
     * 打卡
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkinout()
    {
        return $this->hasMany('App\Models\Attendance\AttendanceCheckinout', 'target_user_id', 'employee_num');
    }

    /**
     * 考勤
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance()
    {
        return $this->hasMany('App\Models\Attendance\AttendanceSheet', 'attendance_user_id', 'id');
    }

    /**
     * 银行卡
     */
    public function bankCard()
    {
        return $this->hasMany('App\Models\UserBankCard', 'user_id', 'id')->orderBy('bank_type', 'asc');
    }

    /**
     * 紧急联系人
     */
    public function urgentUser()
    {
        return $this->hasMany('App\Models\UserUrgentContact', 'user_id', 'id');
    }

    /**
     * 家庭信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function family()
    {
        return $this->hasMany('App\Models\UserFamily', 'user_id', 'id');
    }

    /**
     * 排班信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function workClass()
    {
        return $this->hasOne('App\Models\Attendance\AttendanceWorkClass', 'class_title', 'work_title');
    }

    /**
     * 白名单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function white()
    {
        return $this->hasOne('App\Models\Attendance\AttendanceWhite', 'user_id', 'id');
    }

    public static function scopeFindByEmail($query, $email)
    {
        return $query->whereEmail($email);
    }

    /**
     * 获取员工证书
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getCert()
    {
        return $this->hasOne('App\Models\Certificate', 'user_id', 'id');
    }


    /**
     * 员工编号获取信息及关联关系
     *
     * @param $id
     * @param array $relation
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function findWithRelationById($id, array $relation = [])
    {
        if (is_array($relation) && $relation) {
            $withString = implode(',', $relation);
            return self::with($withString)->where('id', $id)->first();
        } else {
            return self::where('id', $id)->first();
        }
    }


    /**
     * 删除用户的所有权限，一般针对离职员工
     *
     * @param $userId
     *
     * @return bool
     */
    static public function deleteAbilities($userId)
    {
        $user = self::findOrFail($userId);

        foreach ($user->roles as $role) {
            $user->retract($role);
        }

        return true;
    }

    static public function grantPlainUser($userId)
    {
        $user = self::findOrFail($userId);
        return $user->assign('plain_user');
    }


    /**
     *获取连续唯一的员工编号
     *
     * @return string 返回
     */
    static public function genUniqueNum()
    {
        $maxNum = \DB::select("SELECT max(employee_num) as max_num FROM users");
        if (!$maxNum) {
            return 1;
        }

        $maxNum = $maxNum[0]->max_num;
        $newNum = intval($maxNum) + 1;
        $newNum = config('employee.employee_num_prefix') . str_pad($newNum, 5, '0', STR_PAD_LEFT);
        return $newNum;
    }

    /**
     * 获取员工编号
     * @param $employeeNum
     * @return string
     */
    public static function getEmployeeNum($employeeNum)
    {
        return config('employee.employee_num_prefix') . str_pad($employeeNum, 5, '0', STR_PAD_LEFT);
    }

    /**
     * 获取员工编号
     * @param $employeeNum
     * @return string
     */
    public static function clearEmployeeNum($employeeNum)
    {
        return sprintf('%d', substr($employeeNum, 2));
    }

    /**
     * 获取带前缀的员工编号
     * @return string
     * @author hurs
     */
    public function getPrefixEmployeeNum()
    {
        return self::getEmployeeNum($this->employee_num);
    }

    /**
     * 解析员工编号
     * @param $employeeNum
     * @return null|string|string[]
     * @throws \Exception
     */
    static public function parseEmployeeNum($employeeNum)
    {
        $prefix = config('employee.employee_num_prefix');
        $pattern1 = '/^' . $prefix . '[0-9]{5}$/i';
        preg_match($pattern1, $employeeNum, $matches);
        if (!$matches) {
            throw new UserFixException("员工编号填写格式不正确");
        }
        $pattern2 = '/^' . $prefix .'[0]*/i';
        $employeeNum = preg_replace($pattern2, '', $employeeNum);
        return $employeeNum;
    }


    /**
     * 字段与备注建立映射
     */
    static public function basicColMapComment()
    {
        $map = [
            'name' => '系统唯一账号名',
            'employee_num' => '员工编号',
            'chinese_name' => '中文名',
            'english_name' => '英文名',
            'email' => '邮箱',
            'company_id' => '公司',
            'mobile' => '手机号',
            'position' => '职位',
            'avatar' => '头像',
            'gender' => '性别',
            'isleader' => '是否高管',
            'telephone' => '固定电话',
            'password' => '密码',
            'join_at' => '入职时间',
            'leave_at' => '离职日期',
            'status' => '是否在职',
            'is_sync_wechat' => '是否同步企业微信',
            'work_address' => '工作地点',
            'superior_leaders' => '汇报领导',
            'regular_at' => '转正日期',
        ];

        return $map;
    }

    /**
     * 解析ORM中加密参数
     *
     * @param $object
     *
     * @return mixed
     */
    static public function decryptOrmData($object)
    {

        $needDecrypt = ['mobile'];
        $data = $object->toArray();
        foreach ($data as $key => &$value) {
            if (in_array($key, $needDecrypt)) {
                $value = decrypt($value);
            }
        }

        return $data;
    }


    static public function decryptUpdateData($data)
    {
        $needDecrypt = ['mobile'];

        foreach ($data as $key => &$value) {
            if (in_array($key, $needDecrypt)) {
                $value = decrypt($value);
            }
        }

        return $data;
    }


    /**
     * 获取所有员工信息
     *
     * @return static[]
     */
    static public function getAll()
    {
        $users = self::where('status', '<>', self::STATUS_LEAVE)->get();

        return $users;
    }

    /**
     * @param $surname 姓氏
     * @param $englishName 英文名
     * @param $uniqueName 系统唯一账号
     * @return bool
     */
    static public function validUniqueName($surname, $englishName, $uniqueName)
    {
        $surnamePinyin = Pinyin($surname);
        $concatString = $englishName . $surnamePinyin[0];
        return ($concatString === $uniqueName) ? true : false;
    }

    /**
     * @param $surname 姓氏
     * @param $englishName 英文名
     * @return bool
     */
    static public function validEnglishName($surname, $englishName)
    {
        $pinyin = new Pinyin();
        $surnamePinyin = $pinyin->name($surname);
        $surnamePinyinLength = strlen($surnamePinyin[0]);
        $subStrEnglishName = substr($englishName, -$surnamePinyinLength);

        return ($subStrEnglishName == $surnamePinyin[0]) ? true : false;
    }


    static public function getRoster()
    {
        $query = <<<EOF
SELECT u_name AS user_name, v.1_name AS level_1_name, v.2_name AS level_2_name, v.3_name AS level_3_name
    ,IF (locate(u_name, GROUP_CONCAT(u3.chinese_name)) > 0, 2_leader_name, GROUP_CONCAT(u3.chinese_name)) AS direct_leader
	,IF (u_name = 2_leader_name, '', 2_leader_name) AS department_leader
	,1_leader_name AS center_leader
	,company_name
FROM (
	SELECT v.*, GROUP_CONCAT(u2.chinese_name) AS 2_leader_name
	FROM (
		SELECT v.*, GROUP_CONCAT(u1.chinese_name) AS 1_leader_name
		FROM (
			SELECT u_id, u_name, company_name
				, CASE 
					WHEN deepth = 4 THEN v.d2_name
					WHEN deepth = 3 THEN v.d3_name
					WHEN deepth = 2 THEN v.d4_name
					ELSE v.d4_name
				END AS 1_name
				, CASE 
					WHEN deepth = 4 THEN v.d3_name
					WHEN deepth = 3 THEN v.d4_name
					ELSE ''
				END AS 2_name
				, CASE 
					WHEN deepth = 4 THEN v.d4_name
					ELSE ''
				END AS 3_name
				, CASE 
					WHEN deepth = 4 THEN v.d2_id
					WHEN deepth = 3 THEN v.d3_id
					WHEN deepth = 2 THEN v.d4_id
					ELSE v.d4_id
				END AS 1_id
				, CASE 
					WHEN deepth = 4 THEN v.d3_id
					WHEN deepth = 3 THEN v.d4_id
					ELSE ''
				END AS 2_id
				, CASE 
					WHEN deepth = 4 THEN v.d4_id
					ELSE ''
				END AS 3_id
				, CASE 
					WHEN deepth = 4 THEN v.d2_id
					WHEN deepth = 3 THEN v.d3_id
					WHEN deepth = 2 THEN v.d4_id
					ELSE v.d4_id
				END AS 1_depart_id
			FROM (
				SELECT users.id AS u_id, users.chinese_name AS u_name, companies.name AS company_name, d1.name AS d1_name, d2.name AS d2_name, d3.name AS d3_name
					, d4.name AS d4_name, d1.id AS d1_id, d2.id AS d2_id, d3.id AS d3_id, d4.id AS d4_id
					, CASE 
						WHEN d1.name IS NULL
						AND d2.name IS NULL
						AND d3.name IS NULL
						AND d4.name IS NOT NULL THEN 1
						WHEN d1.name IS NULL
						AND d2.name IS NULL
						AND d3.name IS NOT NULL THEN 2
						WHEN d1.name IS NULL
						AND d2.name IS NOT NULL THEN 3
						WHEN d1.name IS NOT NULL THEN 4
						ELSE 0
					END AS deepth
				FROM department_user
					INNER JOIN departments d4 ON department_user.department_id = d4.id
					LEFT JOIN departments d3 ON d4.parent_id = d3.id
					LEFT JOIN departments d2 ON d3.parent_id = d2.id
					LEFT JOIN departments d1 ON d2.parent_id = d1.id
					INNER JOIN users ON department_user.user_id = users.id AND users.status = 1
					LEFT JOIN companies ON users.company_id = companies.id
				WHERE is_primary = 1
			) v
			GROUP BY u_id
		) v
			LEFT JOIN department_user du1
			ON du1.department_id = v.1_id
				AND du1.is_leader = 1
			LEFT JOIN users u1
			ON du1.user_id = u1.id
				AND u1.status = 1
		GROUP BY u_id
	) v
		LEFT JOIN department_user du2
		ON du2.department_id = v.2_id
			AND du2.is_leader = 1
		LEFT JOIN users u2
		ON du2.user_id = u2.id
			AND u2.status = 1
	GROUP BY u_id
) v
	LEFT JOIN department_user du3
	ON du3.department_id = v.3_id
		AND du3.is_leader = 1
	LEFT JOIN users u3
	ON du3.user_id = u3.id
		AND u3.status = 1
GROUP BY u_id
ORDER BY v.1_depart_id ASC;
EOF;

        return \DB::select($query);
    }

    /**
     * 员工编辑校验
     * @param Request $request
     * @param $id
     */
    public function validUpdateData(Request $request, $id)
    {
        //更新时只验证基础信息
        if ($request->get('infotype') && $request->get('infotype') == 'basic') {
            //部分用户不做编辑表单完整校验
            $user = User::whereId($id)->first();
            $count = User::where("id", "<", $id)->count();

            if ($count > self::PART_CHECK_PEOPLE_NUM) {
                if ($user->status != User::STATUS_LEAVE) {
                    $this->validate($request, [
                        'chinese_name' => 'required',
                        'mobile' => 'required|regex:/^1[3456789]\d{9}$/',
                        'english_name' => 'required|min:3',
                        'employee_num' => 'required',
                        'company_id' => 'required',
                        'email' => 'required|regex:/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',
                        'position' => 'required',
                        'gender' => 'required',
                        'name' => 'required',
                        'is_sync_wechat' => 'required',
                        'join_at' => 'required',
                        'password' => 'confirmed',
                        'isleader' => 'required',
//                    'superior_leaders' => 'required',
                        'departments' => 'required',
                        'pri_dept_id' => 'required',
                    ], [
                        'chinese_name.required' => "中文名必填",
                        'mobile.required' => '手机号必填',
                        'mobile.regex' => '手机格式不正确',
                        'english_name.required' => '英文名必填',
                        'english_name.min' => '英文名最小长度是3个单词',
                        'employee_num.required' => '员工编号必填',
                        'email.required' => '邮箱必填',
                        'email.regex' => '邮箱格式不正确',
                        'position.required' => '职位必填',
                        'gender.required' => '性别必填',
                        'company_id.required' => '所属公司必填',
                        'name.required' => '系统唯一账号必填',
                        'is_sync_wechat.required' => '是否同步必填',
                        'join_at.required' => '入职时间必填',
                        'password.confirmed' => '两次密码不一致',
                        'isleader.required' => "是否高管必填",
//                    'superior_leaders'        => '上级领导必填',
                        'departments.required' => '所属部门必填',
                        "pri_dept_id.required" => "主部门必填",
                    ]);
                } else {
                    $this->validate($request, [
                        'chinese_name' => 'required',
                        'mobile' => 'required|regex:/^1[3456789]\d{9}$/',
                        'english_name' => 'required|min:3',
                        'employee_num' => 'required',
                        'company_id' => 'required',
                        'email' => 'required|regex:/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',
                        'position' => 'required',
                        'gender' => 'required',
                        'name' => 'required',
                        'is_sync_wechat' => 'required',
                        'join_at' => 'required',
                        'password' => 'confirmed',
                        'isleader' => 'required',
                    ], [
                        'chinese_name.required' => "中文名必填",
                        'mobile.required' => '手机号必填',
                        'mobile.regex' => '手机格式不正确',
                        'english_name.required' => '英文名必填',
                        'english_name.min' => '英文名最小长度是3个单词',
                        'employee_num.required' => '员工编号必填',
                        'email.required' => '邮箱必填',
                        'email.regex' => '邮箱格式不正确',
                        'position.required' => '职位必填',
                        'gender.required' => '性别必填',
                        'company_id.required' => '所属公司必填',
                        'name.required' => '系统唯一账号必填',
                        'is_sync_wechat.required' => '是否同步必填',
                        'join_at.required' => '入职时间必填',
                        'password.confirmed' => '两次密码不一致',
                        'isleader.required' => "是否高管必填",
                    ]);
                }
            } else {
                if ($user->status != User::STATUS_LEAVE) {
                    $this->validate($request, [
                        'password' => 'confirmed',
                        'departments' => 'required',
                        'pri_dept_id' => 'required',
                    ], [
                        'password.confirmed' => '两次密码不一致',
                        'departments.required' => '所属部门必填',
                        "pri_dept_id.required" => "主部门必填",
                    ]);
                }
            }
        }
    }

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    /**
     * 获取在职员工主部门
     */
    public function getPrimaryDepartment()
    {
        return $this->hasOne('App\Models\DepartUser', 'user_id', 'id')
            ->where('is_primary', DepartUser::DEPARTMENT_PRIMARY_YES);
    }

    /**
     * 获取在职员工主部门绩效中需要用
     */
    public function getDepartmentID()
    {
        return $this->hasOne('App\Models\DepartUser', 'user_id', 'id')
            ->where('is_primary', DepartUser::DEPARTMENT_PRIMARY_YES)->select(['user_id', 'department_id']);
    }

    /**
     * 获取离职员工最后的主部门
     */
    public function getLeaveStaffPrimaryDepartment()
    {
        $primaryDept = $this->hasOne('App\Models\DepartUser', 'user_id', 'id')
            ->withTrashed()->where('is_primary', DepartUser::DEPARTMENT_PRIMARY_YES)
            ->orderBy('department_user.created_at', 'desc')->first();
        return $primaryDept->department;
    }

    public static function findById($id)
    {
        return self::where('id', $id)->first();
    }

    /**
     * @param $ids
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @author hurs
     */
    public static function findByIds($ids)
    {
        return self::whereIn('id', $ids)->get();
    }

    public static function findByIdsIncludeHistory($ids)
    {
        return self::withTrashed()->whereIn('id', $ids)->get();
    }

    public static function findByName($name)
    {
        return $user = User::where('name', $name)->first();
    }

    public static function findByEmployeeNum($employeeNum)
    {
        return $user = User::where('employee_num', $employeeNum)->first();
    }

    public static function getUserInMonth($user_search = '', $department_id = 0, $month)
    {
        $nextMonthStart = Dh::calcNextMonthStart(strtotime($month), false);
        $users = User::with('user_specific', 'detail')->where(function ($q1) use ($month) {
            $q1->whereNull('leave_at')->orWhere('leave_at', '>=', $month);
        })->where(function ($q1) use ($nextMonthStart) {
            $q1->whereNull('join_at')->orWhere('join_at', '<', $nextMonthStart);
        });
        if ($user_search) {
            $users->where(function ($q2) use ($user_search) {
                $q2->where('chinese_name', $user_search)->orWhere('employee_num', self::clearEmployeeNum($user_search));
            });
        }

        if ($department_id) {
            $depts = Department::getAllChildrenDept($department_id);
            $users->whereHas('departUser', function ($q3) use ($depts) {
                $q3->whereIn('department_id', $depts);
            });
        }
        return $users->get();
    }

    public static function noBody()
    {
        $user = new static();
        $user->id = 0;
        $user->chinese_name = '';
        $user->name = '';
        $user->email = '';
        return $user;
    }

    /**
     * 导入全体员工排班excel
     * @param $objWorksheet
     * @param $highestRow
     * @param $hightestColumn
     *
     * @return array
     */
    public static function importWorkClass($objWorksheet, $highestRow)
    {
        $allData = [];

        //从第三行第三列读
        $columnA = 'A';
        $columnE = 'E';
        $rowDate = 2;
        for ($rowIndex = $rowDate; $rowIndex <= $highestRow; $rowIndex++) {
            $data = [];
            $employeeNum = trim($objWorksheet->getCell($columnA . $rowIndex)->getValue());
            $className = trim($objWorksheet->getCell($columnE . $rowIndex)->getValue());

            $FormatEmployeeNum = $num = sprintf('%d', substr($employeeNum, 2)); //KN00987 -> 987

            $user = User::where('employee_num', '=', $FormatEmployeeNum)->first();
            if (!$user) {
                continue;
            }

            $class = AttendanceWorkClass::where('class_name', '=', $className)->first();

            if ($user && $class) {
                $updateData = [
                    'work_type' => $class->type,
                    'work_title' => $class->type == User::WORK_TYPE_SCHEDULE ? '' : $class->class_title,
                ];

                $user->update($updateData);

                $data[$FormatEmployeeNum] = $updateData;
                $allData[] = $data;
            }
        }

        return $allData;
    }

    /**
     *  获取该部门所有的用户（包含软删）
     * @param $departId
     * @param $time
     */
    public static function getByDepartIdAndDatetime($departId, $time)
    {
        $time = $time ? date('Y-m-t H:i:s', strtotime($time)) : Dh::now();
        $departUserIds = DepartUser::withTrashed()->where('department_id', $departId)->where(function ($query) use ($time) {
            $query->where(function ($query) use ($time) {
                $query->whereNull('deleted_at')->whereDate('created_at', '<=', $time);
            })->orWhere(function ($query) use ($time) {
                $query->whereDate('deleted_at', '>=', $time)->whereDate('created_at', '<=', $time);
            });
        })->get()->pluck('user_id');

        return User::findByIdsIncludeHistory($departUserIds);
    }

    public static function isPrimary($userId, $departId)
    {
        return DepartUser::withTrashed()->where('user_id', $userId)->where('department_id', $departId)->first()->is_primary == 1;
    }

    public static function findUserByChineseName($chinese_name)
    {
        return User::where('chinese_name', $chinese_name)->get();
    }

    /**
     * 重写此方法(Passport会调用到)
     * 重写后支持使用手机号、邮箱、用户名登陆
     */
    public function findForPassport($username)
    {
        // 如果是手机号格式则调用手机号登陆
        if (validate_mobile_format($username)) {
            return $this->where('mobile', $username)->first();
        }

        // 如果是邮箱格式则调用邮箱登陆
        if (filter_var($username, FILTER_VALIDATE_EMAIL) !== false) {
            return $this->where('email', $username)->first();
        }

        // 最后尝试使用用户名匹配
        return $this->where('username', $username)->first();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function attendanceStaff()
    {
        return $this->hasOne(AttendanceApiStaff::class, 'user_id', 'id')->select(['id', 'user_id', 'attendance_id', 'is_attendance']);
    }

    /*
     *  获取用户主部门，只能有一个主部门
     * */
    public function departUserPrimary()
    {
        return $this->hasOne('App\Models\DepartUser', 'user_id', 'id')
            ->where('is_primary', 1)
            ->select(["id", "department_id", "user_id", "is_leader", "is_primary"]);
    }

    public function clock()
    {
        return $this->hasMany(AttendanceApiClock::class, 'user_id', 'id')
            ->select(['id', 'user_id', 'dates', 'datetimes', 'type', 'clock_address_type', 'clock_nums', 'classes_id']);
    }

    public function anomaly()
    {
        return $this->hasMany(AttendanceApiAnomaly::class, 'user_id', 'id')
            ->select(['id', 'user_id', 'dates', 'anomaly_type', 'anomaly_time']);
    }

    public function fetchAvatar()
    {
        return $this->avatar ?? config('user.const.avatar');
    }

    public function belongsToManyRoles()
    {
        return $this->belongsToMany(\App\Models\Power\Roles::class,'api_roles_users','user_id','role_id')->select(['role_id']);
    }
}

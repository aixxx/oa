<?php
namespace App\Models\AttendanceApi;

use App\Models\User;
use App\Services\AttendanceApi\AttendanceApiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AttendanceApiStaff extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_staff';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'attendance_id','user_id','is_attendance',
        'created_at','updated_at','deleted_at',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function userInfo(){
        return $this->hasOne(User::class, 'id', 'user_id')->select(['id','name','chinese_name']);
    }

    public function attendance(){
        return $this->hasOne(AttendanceApi::class, 'id', 'attendance_id');
    }

    /**
     *   根据用户ID获取考勤规则
     */
    public static function getUserAttendanceRule($user, $data = ''){
        //该用户没有设置部门。 查看该用户有没有关联考勤组
        $info = AttendanceApiStaff::query()->where('user_id', $user->id)->first();

        if($info){
            //该用户关联了考勤组 查询考勤组规则
            if($info->attendance){
                //考勤组数据正常
                //根据不同考勤模式，获取班次
                if($data)
                    $info = self::getClassesOvertimeRule($info, $user, $data);
            }else{
                //考勤组ID失效。 使用默认规则
                $info = collect(['attendance' => AttendanceApiService::getDefaultAttendance()]);
            }
        }else{
            //该用户没有关联考勤组 查看该用户所属部门是否关联考勤组
            if($user->departUserPrimary){
                //有主部门信息， 查看该部门有没有关联考勤组
                $info = AttendanceApiDepartment::query()->where('department_id',$user->departUserPrimary->department_id)->first();
                if($info){
                    //该部门关联了考勤组 查询考勤组规则
                    if($info->attendance){
                        //根据不同考勤模式，获取班次
                        if($data)
                            $info = self::getClassesOvertimeRule($info, $user, $data);
                    }else{
                        $info = collect(['attendance' => AttendanceApiService::getDefaultAttendance()]);
                    }
                }else{
                    //该部门没有关联考勤组 使用默认规则
                    $info = collect(['attendance' => AttendanceApiService::getDefaultAttendance()]);
                }
            }else{
                $info = collect(['attendance' => AttendanceApiService::getDefaultAttendance()]);
            }
        }
        return $info;
    }

    public static function getClassesOvertimeRule(&$info, $user, $data){
        if($info->attendance->system_type == AttendanceApiService::ATTENDANCE_SYTTEM_SORT){
            $classes_id = AttendanceApiScheduling::query()
                ->where(['user_id'=> $user->id,'dates'=> $data['dates']])
                ->where('take_effect_dates','<=',$data['dates'])
                ->orderBy('take_effect_dates','desc')
                ->orderBy('created_at', 'desc')
                ->first();
            //有数据， 根据班次ID查询班次信息
            if(!empty($classes_id) && $classes_id->classes_id > 0){
                $classes = AttendanceApiClasses::query()
                    ->where('id', $classes_id->classes_id)
                    ->first();
                if($classes){
                    unset($info->attendance->classes);
                    $info->attendance->classes = collect($classes);
                }else{
                    unset($info->attendance->classes);
                    $info->attendance->classes = collect(AttendanceApiService::getDefaultAttendanceClasses());
                }
            }else{
                //没有数据表示当天该员工休息
                $info->attendance->classes = null;
            }
            //加班规则为空，设置默认加班规则
            if(!$info->attendance->overtimeRule){
                unset($info->attendance->overtimeRule);
                $info->attendance->overtimeRule = AttendanceApiService::getDefaultAttendanceOvertimeRule();
            }
        }else{
            //固定制 班次为空， 设置默认班次
            if(!$info->attendance->classes){
                unset($info->attendance->classes);
                $info->attendance->classes = AttendanceApiService::getDefaultAttendanceClasses();
            }
            //加班规则为空，设置默认加班规则
            if(!$info->attendance->overtimeRule){
                unset($info->attendance->overtimeRule);
                $info->attendance->overtimeRule = AttendanceApiService::getDefaultAttendanceOvertimeRule();
            }
        }
        return $info;
    }
}

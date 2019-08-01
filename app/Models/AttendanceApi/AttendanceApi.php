<?php
namespace App\Models\AttendanceApi;

use App\Models\Department;
use App\Models\User;
use App\Services\AttendanceApi\AttendanceApiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceApi extends Model
{
    use SoftDeletes;
    
    protected $table = 'attendance_api';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'title','system_type','classes_id',
        'weeks','add_clock_num','address',
        'clock_range','wifi_title',
        'head_user_id','overtime_rule_id','is_getout_clock',
        'created_at','updated_at','deleted_at',
        'admin_id','cycle_id','clock_node','lng','lat',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
    
    protected $appends = [];


    public function attendanceDepartment(){
        return $this->hasMany(AttendanceApiDepartment::class, 'attendance_id', 'id');
    }


    public function department(){
        return $this->hasMany(Department::class, 'attendance_id', 'id');
    }

    public function classes(){
        return $this->hasOne(AttendanceApiClasses::class, 'id', 'classes_id');
    }

    public function overtimeRule(){
        return $this->hasOne(AttendanceApiOvertimeRule::class, 'id', 'overtime_rule_id');
    }
    public function staff(){
        return $this->hasMany(AttendanceApiStaff::class, 'attendance_id', 'id')
            ->where('is_attendance', AttendanceApiService::ATTENDANCE_STAFF_TRUE);
    }


    public function staffAll(){
        return $this->hasMany(AttendanceApiStaff::class, 'attendance_id', 'id');
    }

    public function isAttendanceTrue(){
        return $this->belongsToMany(User::class, 'attendance_api_staff', 'attendance_id', 'user_id')
            ->where('is_attendance', AttendanceApiService::ATTENDANCE_STAFF_TRUE)
            ->whereNull('attendance_api_staff.deleted_at')
            ->select(['chinese_name','users.id', 'users.avatar', 'attendance_api_staff.deleted_at']);
    }

    public function isAttendanceFalse(){
        return $this->belongsToMany(User::class, 'attendance_api_staff', 'attendance_id', 'user_id')
            ->where('is_attendance', AttendanceApiService::ATTENDANCE_STAFF_FALSE)
            ->whereNull('attendance_api_staff.deleted_at')
            ->select(['chinese_name','users.id', 'users.avatar', 'attendance_api_staff.deleted_at']);
    }

    public function headUser(){
        return $this->hasOne(User::class, 'id', 'head_user_id')
            ->select(['id','chinese_name','avatar']);
    }

}

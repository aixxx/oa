<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Attendance\AttendanceUserInfo
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id 不可编辑系统自动生成
 * @property int|null $badge_number 考勤号码
 * @property string|null $ssn 编号
 * @property string|null $name 中文姓名
 * @property int|null $employee_num 员工唯一编号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceUserInfo whereBadgeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceUserInfo whereEmployeeNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceUserInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceUserInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceUserInfo whereSsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceUserInfo whereUserId($value)
 */
class AttendanceUserInfo extends Model
{
    protected $table = 'attendance_user_info';

    protected $fillable = ['id','user_id','badge_number','ssn','name','employee_num'];

    /**
     *据考勤机编号和OA员工编号
     * @param $user_id
     * @param $employee_num
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function findByIdAndNum($user_id, $employee_num)
    {
        return static::where('user_id', $user_id)->where('employee_num', $employee_num)->get();
    }
}
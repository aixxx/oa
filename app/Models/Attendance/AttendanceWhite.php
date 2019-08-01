<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Attendance\AttendanceWhite
 *
 * @property int $id
 * @property int $employee_num 员工编号xxxxxx,不带KN
 * @property int $user_id 员工id
 * @property string $chinese_name 中文名
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWhite whereChineseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWhite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWhite whereEmployeeNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWhite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWhite whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttendanceWhite extends Model
{
    protected $table = 'attendance_white';

    protected $fillable = ['id','employee_num','chinese_name','created_at','updated_at', 'user_id'];


    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
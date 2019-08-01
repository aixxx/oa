<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Attendance\AttendanceWorkClass
 *
 * @mixin \Eloquent
 * @property int $class_id
 * @property string $class_title 班值代码
 * @property string $class_name 班值名称
 * @property string|null $class_begin_at 上班时间
 * @property string|null $class_end_at 下班时间
 * @property string|null $class_rest_begin_at 休息开始时间
 * @property string|null $class_rest_end_at 休息结束时间
 * @property int $class_times 一日几次班值
 * @property int $class_create_user_id 创建人
 * @property int $class_update_user_id 修改人
 * @property string|null $class_create_at 创建时间
 * @property string|null $class_update_at 修改时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassBeginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassCreateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassRestBeginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassRestEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassUpdateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereClassUpdateUserId($value)
 * @property int $type 所属类型(1.客服类;2.职能类;3.弹性类)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkClass whereType($value)
 */
class AttendanceWorkClass extends Model
{
    protected $table = 'attendance_work_classes';

    protected $primaryKey = 'class_id';

    public $timestamps = false;

    protected $fillable = [
        'class_title',
        'class_name',
        'class_begin_at',
        'class_end_at',
        'class_rest_begin_at',
        'class_rest_end_at',
        'class_times',
        'class_create_user_id',
        'class_update_user_id',
        'class_create_at',
        'class_update_at',
        'type',
    ];

    /**
     * 班值所属类型
     */
    const CLASS_TYPE = [
        '1' => '客服制',
        '2' => '职能制',
        '3' => '弹性制',
    ];

    /**
     * 班制映射
     */
    const CLASS_TYPE_MAP = [
        '常日班(09:00-18:00)'     => 2,
        '常日班(10:00-19:00)'     => 2,
        '常日班(09:00-18:30)'     => 2,
        '日常班(09:30-18:30)'     => 2,
        '钱牛牛客服早班(08:00-17:00)' => 1,
        '钱牛牛客服晚班(14:00-22:00)' => 1,
        '现金贷客服早班(09:00-18:00)' => 1,
        '现金贷客服早班(12:00-21:00)' => 1,
        '不定时班值'                => 3,
    ];

    public static function firstByTitle($work_title)
    {
        return AttendanceWorkClass::where('class_title', $work_title)->first();
    }
}
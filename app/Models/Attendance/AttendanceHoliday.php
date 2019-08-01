<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/7/25
 * Time: 下午6:22
 */

namespace App\Models\Attendance;


use App\Http\Helpers\Dh;
use App\Models\User;
use function GuzzleHttp\Psr7\str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Attendance\AttendanceHoliday
 *
 * @mixin \Eloquent
 * @property int $holiday_id 主键
 * @property string $holiday_date 时间
 * @property int $holiday_status 状态(0-工作，1-休息)
 * @property int $holiday_type 是否法定节假日(0-否，1-是,周末不是)
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceHoliday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceHoliday whereHolidayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceHoliday whereHolidayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceHoliday whereHolidayStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceHoliday whereHolidayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceHoliday whereUpdatedAt($value)
 */
class AttendanceHoliday extends Model
{
    protected $primaryKey = 'holiday_id';
    const HOLIDAY_STATUS_WORKING = 0;  //工作状态
    const HOLIDAY_STATUS_REST    = 1;  //休息状态

    const TYPE_IS_ANNUAL     = 1;  //法定假期
    const TYPE_IS_NOT_ANNUAL = 0;  //非法定假期

    public $fillable = [
        'holiday_date',
        'holiday_status',
        'holiday_type',
    ];


    /**
     * 获取节假日记录根据月份
     * @param $begin_time
     * @param $end_time
     */
    public static function getHolidayByMonth($begin_time, $end_time)
    {
        $holidays = AttendanceHoliday::where('holiday_date', '>=', $begin_time)
            ->where('holiday_date', '<', $end_time)->get();
        return $holidays;
    }

    /**
     * 某天是否休息
     * @param $date
     * @return bool
     * @author hurs
     */
    public static function isHoliday($date)
    {
        return self::where('holiday_date', $date)
            ->where('holiday_status', self::HOLIDAY_STATUS_REST)
            ->exists();
    }

    /**
     *
     * 某天是否上班
     * @param $date
     * @return bool
     * @author hurs
     */
    public static function isWorkDay($date)
    {
        return self::where('holiday_date', $date)
            ->where('holiday_status', self::HOLIDAY_STATUS_WORKING)
            ->exists();
    }

    /**
     * 某天是否节假日
     * @param $date
     * @return Model|null|object|static
     */
    public static function isFestival($date)
    {
        return self::where('holiday_date', $date)
            ->where('holiday_type', self::TYPE_IS_ANNUAL)->first();
    }

    /*
     * 获取时间段内应出勤天数
     */
    public static function findWorkingDays($startDate, $endDate)
    {
        return AttendanceHoliday::where(function ($query) {
            $query->where('holiday_status', AttendanceHoliday::HOLIDAY_STATUS_WORKING)
                ->orWhere('holiday_type', AttendanceHoliday::TYPE_IS_ANNUAL);
        })->where('holiday_date', '>=', $startDate)->where('holiday_date', '<', $endDate)
                ->get();
    }
}
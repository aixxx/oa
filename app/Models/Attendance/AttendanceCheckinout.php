<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Attendance\AttendanceCheckinout
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $user_id 考勤机员工id
 * @property string $check_time 签卡时间
 * @property int $sensor_id 考勤机编号
 * @property string|null $sn 考勤机序列号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceCheckinout whereCheckTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceCheckinout whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceCheckinout whereSensorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceCheckinout whereSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceCheckinout whereUserId($value)
 */
class AttendanceCheckinout extends Model
{
    protected $table = 'attendance_checkinout';

    protected $fillable = ['id','user_id','check_time','sensor_id','sn'];

    /**
     * 根据当月打卡记录获取所有考勤记录
     *
     * @param $startDate
     * @param $endDate
     *
     * @return array
     */
    public static function getAllCheckTime($user, $startDate, $endDate)
    {
        $employeeNum = $user->employee_num;
        $employeeId  = $user->id;

        $sql = <<<EOF
SELECT holiday_date, check_time.date AS date, IFNULL(user_id, $employeeId) AS user_id, hour
FROM attendance_holidays
	LEFT JOIN (
		 SELECT date, user_id, user_num, GROUP_CONCAT(hour ORDER BY check_time ASC SEPARATOR ',') AS hour
		FROM (
			SELECT DATE_FORMAT(attendance_checkinout.check_time, '%Y-%m-%d') AS date
                , attendance_checkinout.check_time AS hour
                , check_time
                , users.id AS user_id
                ,`users`.`employee_num` as user_num
			FROM users
				LEFT JOIN attendance_user_info ON `users`.`employee_num` = `attendance_user_info`.`employee_num`
				LEFT JOIN attendance_checkinout ON `attendance_user_info`.`user_id` = `attendance_checkinout`.`user_id`
			WHERE`users`.`employee_num` = $employeeNum
			    AND `check_time` >= '$startDate'
				AND `check_time` <= '$endDate'
			ORDER BY date ASC
		) all_time
		GROUP BY date, user_num
	) check_time
	ON check_time.date = holiday_date
WHERE holiday_date >= '$startDate'
	AND holiday_date <= '$endDate'
ORDER BY holiday_date ASC, user_id ASC;
EOF;

        return DB::select($sql);
    }
}
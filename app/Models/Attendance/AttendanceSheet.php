<?php

namespace App\Models\Attendance;

use App\Http\Helpers\Dh;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Workflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\VacationManageService;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SebastianBergmann\CodeCoverage\Report\PHP;
use UserFixException;
use DevFixException;
/**
 * App\Models\Attendance\AttendanceSheet
 *
 * @mixin \Eloquent
 * @property int $attendance_id 主键
 * @property string $attendance_date 日期
 * @property int|null $attendance_user_id 员工编号
 * @property int|null $attendance_work_id 班值类型
 * @property string|null $attendance_begin_at 实际考勤上班时间
 * @property string|null $attendance_end_at 实际考勤下班时间
 * @property string|null $attendance_time 打卡时间
 * @property int|null $attendance_is_abnormal 是否异常(0:否 1:是)
 * @property string|null $attendance_abnormal_note 异常说明
 * @property string|null $attendance_create_at 创建时间
 * @property string|null $attendance_update_at 修改时间
 * @property float|null $attendance_length
 * @property int $attendance_is_manual 是否手动处理(0:自动;1.手动)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceAbnormalNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceBeginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceCreateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceIsAbnormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceIsManual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceUpdateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceWorkId($value)
 * @property string $attendance_holiday_type 休假类型1
 * @property float $attendance_holiday_type_sub 休假时长1
 * @property int $attendance_holiday_entry_id 流程编号1
 * @property string $attendance_holiday_type_second 休假类型2
 * @property float $attendance_holiday_type_sub_second 休假时长2
 * @property int $attendance_holiday_entry_id_second 流程编号2
 * @property float $attendance_travel_interval 出差时长
 * @property int $attendance_travel_entry_id 出差流程编号
 * @property float $attendance_overtime_sub 加班时长
 * @property int $attendance_overtime_entry_id 加班流程编号
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceHolidayEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceHolidayEntryIdSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceHolidayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceHolidayTypeCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceHolidayTypeSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceHolidayTypeSecondCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceHolidayTypeSub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceHolidayTypeSubSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceOvertimeEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceOvertimeSub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceTravelEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceSheet whereAttendanceTravelInterval($value)
 */
class AttendanceSheet extends Model
{
    const STATUS_ABNORMAL = 1; //考勤异常
    const STATUS_NORMAL   = 0; //考勤不异常

    const IS_MANUAL_YES = 1; //人工更新
    const IS_MANUAL_NO  = 0; //自动更新

    const MEAL_ONCE     = 5; // 大于5算一次餐补
    const MEAL_DOUBLE   = 12; // 大于12算一次餐补

    const CRITICAL_TIME  = '06:00'; //早上六点是临界时刻
    const MORNING_TIME   = '10:00'; //上午上班开始时间
    const AFTERNOON_TIME = '15:00'; //下午上班开始时间
    const CHECKOUT_TIME  = '19:00'; //签出时间

    protected $table = 'attendance_sheets';

    protected $primaryKey = 'attendance_id';
    public $timestamps = false;

    protected $fillable = [
        'attendance_id',
        'attendance_date',
        'attendance_user_id',
        'attendance_work_id',
        'attendance_begin_at',
        'attendance_end_at',
        'attendance_time',
        'attendance_is_abnormal',
        'attendance_abnormal_note',
        'attendance_create_at',
        'attendance_update_at',
        'attendance_length',
        'attendance_is_manual',

        'attendance_holiday_type',
        'attendance_holiday_type_sub',
        'attendance_holiday_entry_id',
        'attendance_holiday_type_second',
        'attendance_holiday_type_sub_second',
        'attendance_holiday_entry_id_second',
        'attendance_travel_interval',
        'attendance_travel_entry_id',
        'attendance_overtime_sub',
        'attendance_overtime_entry_id',
    ];


    /**
     * 获取打卡的分钟时间
     * 1.若打卡记录过多，则显示前两次、后两次打卡
     * 2.tips完整显示
     *
     * @param $time
     * @param bool $short
     * @return string
     */
    public static function getHour($time, $short = true)
    {
        $result = '';
        if ($time)  {
            $timeArray = explode(',', $time);

            $cnt = count($timeArray);
            foreach ($timeArray as $key => $checkTime) {
                if ($cnt > 4 && $short) {
                    if ($key > 1 && $key < ($cnt - 2)) {
                        continue;
                    }
                }

                $result .= substr($checkTime, 11, 5) . ' ';
            }
        }

        return $result;
    }

    /**
     * 获取考勤列表
     *
     * @param $startDate
     * @param $endDate
     * @return $this
     */
    public static function findAttendanceList($startDate, $endDate)
    {
        $attendance     = User::join('attendance_sheets', 'id', '=', 'attendance_user_id')
            ->where('attendance_date', '>=', $startDate)
            ->where('attendance_date', '<', $endDate);

        return $attendance;
    }


    /**
     * 根据员工Id获取某天的考勤
     *
     * @param $userId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function getAttendanceByUserByDate($userId, $date)
    {
        return AttendanceSheet::where('attendance_user_id', '=', $userId)
            ->where('attendance_date', '=', $date)->first();
    }

    /**
     * 根据员工Id获取时间段的考勤
     *
     * @param $userId
     * @param $startDate
     * @param $endDate
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function getAttendanceByIdByRangDate($userId, $startDate, $endDate)
    {
        return AttendanceSheet::where('attendance_user_id', '=', $userId)
            ->where('attendance_date', '>=', $startDate)
            ->where('attendance_date', '<', $endDate)
            ->orderBy('attendance_date')
            ->get();
    }

    /**
     * 签卡申请补签入库
     *
     * $entries 转 $request
     * @param $request['checktime'] string 签卡时间
     * @param $request['user_id'] int  员工编号
     * @param $request['reason'] string  签卡原因
     * @param $request['retroactive_type'] string  上班/下班
     *
     * @return bool
     */
    public static function saveSupplement($entries = null)
    {
        $result = false;
        $request = [];

        foreach ($entries as $entry) {
            if (!isset($entry['form_data']['retroactive_type']['value']) ||
                !isset($entry['form_data']['retroactive_reason']['value']) ||
                !isset($entry['form_data']['retroactive_datatime']['value']) || !isset($entry['entry']['user_id'])) {
                throw new DevFixException('签卡流程处理,传入参数不完整');
            }

            $retroactiveType = $entry['form_data']['retroactive_type']['value'];
            $subStr = '签卡';
            if (!strstr($retroactiveType, $subStr)) {
                $retroactiveType .= $subStr;
            }

            $request = [
                'checktime' => $entry['form_data']['retroactive_datatime']['value'],
                'reason'    => $entry['form_data']['retroactive_reason']['value'],
                'type'      => $retroactiveType,
                'user_id'   => $entry['entry']['user_id'],
            ];
        }

        $result = self::notifyAttendanceByRetroactive($request);

        return $result;
    }

    /**
     * 补签入库
     *
     * @param $request
     * @return bool
     * @throws DevFixException
     */
    public static function notifyAttendanceByRetroactive($request)
    {
        $result = false;

        //待更新的日期考勤
        if ($request['reason'] != '跨天加班') {
            $attendance = AttendanceSheet::where('attendance_date', Dh::formatDate($request['checktime']))
                ->where('attendance_user_id', $request['user_id'])
                ->first();
        } else {
            $attendanceDate = Dh::subDays($request['checktime'], 1);
            $attendance = AttendanceSheet::where('attendance_date', $attendanceDate)
                ->where('attendance_user_id', $request['user_id'])
                ->first();
        }

        if ($attendance) {
            if ($request['type'] == '上班签卡' || $request['type'] == '上班') {
                $updateData['attendance_begin_at'] = $request['checktime'];
                $updateData['attendance_end_at']   = $attendance->attendance_end_at;

                if (strtotime($updateData['attendance_begin_at']) > strtotime($updateData['attendance_end_at'])) {
                    $updateData['attendance_end_at'] = null;
                }
            } else {
                $updateData['attendance_begin_at'] = $attendance->attendance_begin_at;
                $updateData['attendance_end_at']   = $request['checktime'];

                if (strtotime($updateData['attendance_begin_at']) > strtotime($updateData['attendance_end_at'])) {
                    $updateData['attendance_begin_at'] = null;
                }
            }

            if (strtotime($updateData['attendance_begin_at']) && strtotime($updateData['attendance_end_at'])) {
                $updateData['attendance_is_abnormal'] = self::STATUS_NORMAL;
                $updateData['attendance_length'] = round((strtotime($updateData['attendance_end_at']) - strtotime($updateData['attendance_begin_at'])) / 3600, 6);
            } else {
                $updateData['attendance_is_abnormal'] = self::STATUS_ABNORMAL;
                $updateData['attendance_length'] = 0;
            }

            $note = sprintf(
                "补签%s 原因：%s 时间:%s; ",
                $request['type'],
                $request['reason'],
                $request['checktime']
            );

            if (strstr($attendance->attendance_abnormal_note, $note) === false) {
                $updateData['attendance_abnormal_note'] = $note;
            }
            $result = $attendance->update($updateData);
            if ($result) {
                Log::info("签卡成功。员工【user_id:" . $attendance->attendance_user_id .
                    "】,【日期 :" . $attendance->attendance_date .
                    "】,【updateData:" . json_encode($updateData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
                    "】,【request:" . json_encode($request, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "]");
            } else {
                Log::info("签卡失败。员工【user_id:" . $attendance->attendance_user_id .
                    "】,【日期 :" . $attendance->attendance_date .
                    "】,【updateData:" . json_encode($updateData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
                    "】,【request:" . json_encode($request, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "]");

                throw new DevFixException(sprintf(
                    "员工【%s】日期【%s】签卡【%s】失败",
                    $attendance->attendance_user_id,
                    $attendance->attendance_date,
                    json_decode($updateData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ));
            }
        }

        return $result;
    }

    /**
     * HR补签入库
     * @param $request['begin'] string 签卡时间
     * @param $request['end'] string 签卡时间
     * @param $request['id'] int  自增id
     *
     * @return bool
     */
    public static function updateAttendance($request)
    {
        $result     = false;

        $attendance = AttendanceSheet::where('attendance_id' , '=',$request['id'])->first();
        $updateData['attendance_begin_at']      = $request['begin'];
        $updateData['attendance_end_at']        = $request['end'];
        $updateData['attendance_abnormal_note'] = $attendance->attendance_abnormal_note . ' 管理员更新';
        $updateData['attendance_is_abnormal']   = self::STATUS_NORMAL;    //考勤正常
        $updateData['attendance_is_manual']     = self::IS_MANUAL_YES;
        $updateData['attendance_length']        = round((strtotime($attendance->end) - strtotime($request['begin'])) / 3600, 6);

        if ($attendance->update($updateData)) {
            $result = true;
        }

        return $result;
    }


    /**
     * 通知考勤系统记录缺勤原因-请假
     * @param $userId string 员工编号
     * @param $data   array  请假流程完整信息
     */
    public static function notifyCauseOfAbsenceByLeave($userId, $data)
    {
        $holidayType = $data['holiday_type']['value'];
        $startTime   = $data['date_begin']['value'];
        $endTime     = $data['date_end']['value'];
        $note = $holidayType . $data['date_time_sub']['name'] . ':' . $data['date_time_sub']['value'] . ' ';

        $attendance = AttendanceSheet::where('attendance_date','>=', Dh::formatDate($startTime))
            ->where('attendance_date','<', $endTime)
            ->where('attendance_user_id', $userId)->get();

        if ($attendance->count()) {
            foreach ($attendance as $item) {
                $update['attendance_abnormal_note'] = $item->attendance_abnormal_note . $note;
                $update['attendance_is_abnormal']   = self::STATUS_NORMAL;
                $item->fill($update);
                $item->save();
            }
        }
    }

    /**
     * 通知考勤系统记录缺勤原因-出差
     * @param $data   array  出差流程完整信息
     */
    public static function notifyCauseOfAbsenceByBusiness($data)
    {
        $entry = reset($data);
        $userId = $entry['entry']['user_id'];

        $startTime   = $entry['form_data']['date_begin']['value'];
        $endTime     = $entry['form_data']['date_end']['value'];
        $note = $entry['form_data']['date_interval']['name'] . ':' . $entry['form_data']['date_interval']['value'] . ' ';

        $attendance = AttendanceSheet::where('attendance_date','>=', Dh::formatDate($startTime))
            ->where('attendance_date','<=', $endTime)
            ->where('attendance_user_id', $userId)->get();

        if ($attendance->count()) {
            foreach ($attendance as $item) {
                $update['attendance_abnormal_note'] = $item->attendance_abnormal_note . $note;
                $update['attendance_is_abnormal']   = self::STATUS_NORMAL;
                $item->fill($update);
                $item->save();
            }
        }
    }
}
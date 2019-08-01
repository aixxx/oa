<?php

namespace App\Services;

use App\Models\Attendance\AttendanceHoliday;
use App\Models\Attendance\AttendanceSheet;
use App\Models\User;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\Workflow\Entry;
use App\Models\Workflow\EntryData;
use App\Models\Workflow\Flow;
use App\Models\DepartmentMapCentre;
use App\Models\UserBankCard;
use App\Models\Workflow\Workflow;
use App\Http\Helpers\Dh;
use App\Services\Attendance\CalcTimeService;
use Illuminate\Support\Facades\Auth;
use UserFixException;
/**
 * 请假流程规则校验类
 *
 * Class WorkflowLeaveService
 * @package App\Services
 */
class WorkflowLeaveService
{
    /**
     * 校验请假时间是否有重叠
     *
     * @param $userId
     * @param $holidayStart
     * @param $holidayEnd
     * @return bool
     */
    public static function checkHolidayRecord($userId, $holidayStart, $holidayEnd)
    {
        $re      = Workflow::fetchUserWorkflowData($userId);
        $resumed = Workflow::fetchUserResumedWorkflowData($userId);
        for ($i = 0; $i < count($re) - 3; $i += 3) {//-3是为了过滤本次提交的记录
            //过滤已销假的流程
            $entry = $re[$i]['entry_id'];
            if (in_array($entry, $resumed)) {
                continue;
            }

            $begin            = strtotime($re[$i + 1]['field_value']);
            $end              = strtotime($re[$i + 2]['field_value']);
            $holidayStartTime = strtotime($holidayStart);
            $holidayEndTime   = strtotime($holidayEnd);
            for (; $holidayStartTime < $holidayEndTime; $holidayStartTime += 3600) {
                if ($holidayStartTime >= $begin && $holidayStartTime < $end) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 校验春节假是否日历年已经请
     *
     * @param $userId
     * @param $holidayStart
     * @param $holidayEnd
     * @return bool
     */
    public static function checkChineseSpringFestival($userId, $holidayStart, $holidayEnd)
    {
        $re      = Workflow::fetchUserWorkflowData($userId);
        $resumed = Workflow::fetchUserResumedWorkflowData($userId);
        for ($i = 0; $i < count($re) - 3; $i += 3) {//-2是为了过滤本次提交的记录
            //过滤已销假的流程
            $entry = $re[$i]['entry_id'];
            if (in_array($entry, $resumed)) {
                continue;
            }

            $holidayType      = $re[$i]['field_value'];
            $begin            = strtotime($re[$i + 1]['field_value']);
            $end              = strtotime($re[$i + 2]['field_value']);
            $holidayStartTime = strtotime($holidayStart);
            $holidayEndTime   = strtotime($holidayEnd);

            if (!$holidayType == '春节假') {
                continue;
            }

            if ($holidayStartTime <= $begin && $end <= $holidayEndTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * 校验同一天请假8小时
     *
     * @param $userId  integer
     * @param $holidayStart
     * @param $holidayEnd
     * @param $type
     * @return bool
     */
    public static function checkSameDateHolidayRecord($userId, $holidayStart, $holidayEnd, $type)
    {
        $re         = Workflow::fetchUserWorkflowData($userId);
        $resumed    = Workflow::fetchUserResumedWorkflowData($userId);  //已销假的请假entry_id数组

        $holidayLength = self::getOneDayLeaveLength($holidayStart, $holidayEnd, $type);

        foreach ($holidayLength as $date =>$length) {
            $consumedLength = 0;
            for ($i = 0; $i < count($re) - 3; $i += 3) {//-3是为了过滤本次提交的记录
                //过滤已销假的流程
                $entry = $re[$i]['entry_id'];
                if (in_array($entry, $resumed)) {
                    continue;
                }

                $holidayType      = $re[$i]['field_value'];
                $begin            = $re[$i + 1]['field_value'];
                $end              = $re[$i + 2]['field_value'];

                if (Dh::formatDate($end) < Dh::formatDate($holidayStart) || Dh::formatDate($holidayEnd) < Dh::formatDate($begin)) {
                    continue;
                }

                $calc           = self::getOneDayLeaveLength($begin, $end, $holidayType);

                $dateLength     = $calc[$date] ?? 0;
                $consumedLength += $dateLength;
            }

            if (($consumedLength + $length) > 8) {
                return false;
            }
        }

        return true;
    }

    /**
     * 计算时长
     *
     * @param $startDate
     * @param $endDate
     * @param $type
     * @param null $date
     * @return array
     * @throws \Exception
     */
    private static function getOneDayLeaveLength($startDate, $endDate, $type)
    {
        $result = [];
        $user = Auth::user();
        while (strtotime($startDate) < strtotime($endDate)) {
            if (Dh::formatDate($startDate) != Dh::formatDate($endDate)) {
                $today_end_time  = '22:00';
                $today_start_end = date("H:i", strtotime($startDate));
            } else {
                $today_end_time  = date("H:i", strtotime($endDate));
                $today_start_end = date("H:i", strtotime($startDate));
            }
            switch ($user->work_type) {
                case User::WORK_TYPE_SCHEDULE;
                    $today_calc_time = CalcTimeService::getCalcByDateByUserId(Dh::formatDate($startDate), $user, $type);
                    break;
                case User::WORK_TYPE_REGULAR:
                    if ($user->workClass) {
                        list($checkInTime) = explode(':', $user->workClass->class_begin_at);
                        $today_calc_time = [
                            'checkin_time'   => $user->workClass->class_begin_at,
                            'afternoon_time' => (intval($checkInTime) + 5) . ':00',
                            'checkout_time'  => $user->workClass->class_end_at,
                        ];
                    } else {
                        throw new UserFixException(sprintf("未查到班值代码【%s】的数据，请联系Hr处理", $user->work_title));
                    }
                    break;
                default:
                    $today_calc_time = [
                        'checkin_time'   => AttendanceSheet::MORNING_TIME,
                        'afternoon_time' => AttendanceSheet::AFTERNOON_TIME,
                        'checkout_time'  => AttendanceSheet::CHECKOUT_TIME,
                    ];
                    break;
            }
            if ($user->work_type != User::WORK_TYPE_SCHEDULE && !in_array($type, CalcTimeService::$consecutiveHolidays) && AttendanceHoliday::isHoliday(Dh::formatDate($startDate))) {
                $today_calc_time = null;
            }
            if ($user->work_type == User::WORK_TYPE_SCHEDULE &&
                in_array($type, CalcTimeService::$consecutiveHolidays) &&
                AttendanceHoliday::isHoliday(Dh::formatDate($startDate)) && !$today_calc_time
            ) {
                $today_calc_time = [
                    'checkin_time'   => AttendanceSheet::MORNING_TIME,
                    'afternoon_time' => AttendanceSheet::AFTERNOON_TIME,
                    'checkout_time'  => AttendanceSheet::CHECKOUT_TIME,
                ];
            }


            //
            $currentDate = Dh::formatDate($startDate);
            $result[$currentDate] = CalcTimeService::getLeaveHour($today_start_end, $today_end_time, $today_calc_time) * 8;


            $startDate = date("Y-m-d 08:00:00", strtotime($startDate . ' +1 day'));
        }

        return $result;
    }
}
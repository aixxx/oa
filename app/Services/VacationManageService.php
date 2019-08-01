<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/7/27
 * Time: 下午5:08
 */

namespace App\Services;


use App\Http\Helpers\Dh;
use App\Models\Attendance\AttendanceHoliday;
use App\Models\Attendance\AttendanceSheet;
use App\Models\Attendance\AttendanceVacation;
use App\Models\Attendance\AttendanceVacationChange;
use App\Models\User;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\Workflow;
use App\Models\Workflow\Entry;
use \Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DB;
use UserFixException;
use DevFixException;
class VacationManageService
{
    const DAYS_ONE_YEAR  = 365;
    const DAYS_ONE_MONTH = 30;
    const MONTH_ONE_YEAR = 12;

    public static $loadUserVacationColumn = [
        '员工姓名'   => 'user_name',
        '员工编号'   => 'employee_num',
        '法定年假'   => 'annual',
        '应发法定年假' => 'sum_annual',
        '实发法定年假' => 'actual_annual',
        '公司福利年假' => 'company_benefits',
        '应发福利年假' => 'sum_company_benefits',
        '实发福利年假' => 'actual_company_benefits',
        '全薪病假'   => 'full_pay_sick',
        '实发全薪病假' => 'actual_full_pay_sick',
        '调休'     => 'extra_day_off',
    ];

    private static function addVacationLog(
        $attendanceVacation,
        $user,
        $oldAnnual,
        $oldCompanyBenefits,
        $oldActualAnnual,
        $oldActualCompanyBenefits,
        $oldFullPaySick,
        $oldExtraDayOff
    ) {
        $updateUserId = auth()->id();
        if ($oldAnnual != $attendanceVacation->annual) {
            AttendanceVacationChange::createVacationChangeLog($user->id, AttendanceVacationChange::CHANGE_TYPE_ANNUAL, $oldAnnual,
                $attendanceVacation->annual, AttendanceVacationChange::getChangeAmount($oldAnnual, $attendanceVacation->annual),
                AttendanceVacation::REMAKE_HR_IMPORT_EXCEL, AttendanceVacation::VACATION_UNIT_HOUR, '', $updateUserId);
        }
        if ($oldCompanyBenefits != $attendanceVacation->company_benefits) {
            AttendanceVacationChange::createVacationChangeLog($user->id, AttendanceVacationChange::CHANGE_TYPE_COMPANY_BENEFITS, $oldCompanyBenefits,
                $attendanceVacation->company_benefits, AttendanceVacationChange::getChangeAmount($oldCompanyBenefits, $attendanceVacation->company_benefits),
                AttendanceVacation::REMAKE_HR_IMPORT_EXCEL, AttendanceVacation::VACATION_UNIT_HOUR, '', $updateUserId);
        }
        if ($oldActualAnnual != $attendanceVacation->actual_annual) {
            AttendanceVacationChange::createVacationChangeLog($user->id, AttendanceVacationChange::CHANGE_TYPE_ACTUAL_ANNUAL, $oldActualAnnual,
                $attendanceVacation->actual_annual, AttendanceVacationChange::getChangeAmount($oldActualAnnual, $attendanceVacation->actual_annual),
                AttendanceVacation::REMAKE_HR_IMPORT_EXCEL, AttendanceVacation::VACATION_UNIT_HOUR, '', $updateUserId);
        }
        if ($oldActualCompanyBenefits != $attendanceVacation->actual_company_benefits) {
            AttendanceVacationChange::createVacationChangeLog($user->id, AttendanceVacationChange::CHANGE_TYPE_ACTUAL_COMPANY_BENEFITS,
                $oldActualCompanyBenefits,
                $attendanceVacation->actual_company_benefits,
                AttendanceVacationChange::getChangeAmount($oldActualCompanyBenefits, $attendanceVacation->actual_company_benefits),
                AttendanceVacation::REMAKE_HR_IMPORT_EXCEL, AttendanceVacation::VACATION_UNIT_HOUR, '', $updateUserId);
        }
        if ($oldFullPaySick != $attendanceVacation->full_pay_sick) {
            AttendanceVacationChange::createVacationChangeLog($user->id, AttendanceVacationChange::CHANGE_TYPE_FULL_PAY_SICK, $oldFullPaySick,
                $attendanceVacation->full_pay_sick, AttendanceVacationChange::getChangeAmount($oldFullPaySick, $attendanceVacation->full_pay_sick),
                AttendanceVacation::REMAKE_HR_IMPORT_EXCEL, AttendanceVacation::VACATION_UNIT_HOUR, '', $updateUserId);
        }
        if ($oldExtraDayOff != $attendanceVacation->extra_day_off) {
            AttendanceVacationChange::createVacationChangeLog($user->id, AttendanceVacationChange::CHANGE_TYPE_EXTRA_DAY_OFF, $oldExtraDayOff,
                $attendanceVacation->extra_day_off, AttendanceVacationChange::getChangeAmount($oldExtraDayOff, $attendanceVacation->extra_day_off),
                AttendanceVacation::REMAKE_HR_IMPORT_EXCEL, AttendanceVacation::VACATION_UNIT_HOUR, '', $updateUserId);
        }

    }

    //转换读取excel日期的格式
    public static function excelDateFormat($date)
    {
        $n = intval(($date - 25569) * 3600 * 24);
        return gmdate('Y-m-d', $n);
    }

    public static function downloadRoster($oaLessThan)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $query       = $oaLessThan;

        $n = 1;
        $sheet->setCellValue('A' . $n, '员工错误信息');
        if (count($query)) {
            foreach ($query as $row) {
                $sheet->setCellValue('A' . ($n + 1), $row);
                $n++;
            }
        }

        $fileName = '员工假期数据处理冲突用户' . date("Ymd") . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
//        header('Content-Type:application/vnd.ms-excel');//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename=' . $fileName);//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

    /**
     *
     * @param Worksheet $objWorksheet
     * @return array
     * @throws Exception
     */
    public static function loadUserVacation(Worksheet $objWorksheet)
    {
        $allData    = [];
        $oaLessThan = [];
        $rows       = $objWorksheet->toArray();
        $header     = $rows[0];
        if (array_values($header) != array_keys(self::$loadUserVacationColumn)) {
            throw new UserFixException('表头不匹配，请根据下载的表格编辑');
        }
        for ($rowIndex = 1; $rowIndex < count($rows); $rowIndex++) {
            $data = [];
            foreach ($rows[$rowIndex] as $k => $v) {
                $data[self::$loadUserVacationColumn[$header[$k]]] = $v;
            }
            $employee_num = User::clearEmployeeNum($data['employee_num']);
            if (!$employee_num) {
                break;
            }
            $user = User::findByEmployeeNum($employee_num);
            if (!$user) {
                $oaLessThan[] = "员工编号为 $employee_num 的用户未找到";
                continue;
            }
            $vacation = AttendanceVacation::getVacation($user->id);
            if (!$vacation) {
                $vacation = new AttendanceVacation();
            }
            $oldAnnual                = $vacation->annual ?: 0;
            $oldBenefits              = $vacation->company_benefits ?: 0;
            $oldActualAnnual          = $vacation->actual_annual ?: 0;
            $oldActualCompanyBenefits = $vacation->actual_company_benefits ?: 0;
            $oldFullPaySick           = $vacation->full_pay_sick ?: 0;
            $oldExtraDayOff           = $vacation->extra_day_off ?: 0;
            //只允许修改法定和发力年假的当前余额和实际发放假期
            if (fmod($data['annual'] ?: 0, 4) != 0 || fmod($data['company_benefits'] ?: 0, 4) != 0 || fmod($data['full_pay_sick'] ?: 0, 4) != 0) {
                throw new UserFixException($user->chinese_name . '当前法定或者福利年假或全薪病假不是4的倍数');
            }
            if (fmod($data['actual_annual'] ?: 0, 8) != 0 || fmod($data['actual_company_benefits'] ?: 0, 8) != 0) {
                throw new UserFixException($user->chinese_name . '实际发放法定或者福利年假不是8的倍数');
            }
            $vacation->annual                  = $data['annual'];
            $vacation->company_benefits        = $data['company_benefits'];
            $vacation->actual_annual           = $data['actual_annual'];
            $vacation->actual_company_benefits = $data['actual_company_benefits'];
            $vacation->full_pay_sick           = $data['full_pay_sick'];
            $vacation->actual_full_pay_sick    = $data['actual_full_pay_sick'];
            $vacation->extra_day_off           = $data['extra_day_off'];
            if ($vacation->save()) {
                self::addVacationLog($vacation, $user, $oldAnnual, $oldBenefits, $oldActualAnnual, $oldActualCompanyBenefits, $oldFullPaySick, $oldExtraDayOff);
            }
            $allData[] = $vacation;
        }
        if (count($oaLessThan) > 0) {
            self::downloadRoster($oaLessThan);
        }
        return $allData;
    }

    /**
     * 获取以年度为单位的发放计划
     * @param int $firstWorkTime
     * @param int $joinTime
     * @param int $year
     * @return array
     * @author hurs
     */
    public static function getAnnualAndBenefitSimplePlan($firstWorkTime, $joinTime, $year = 0)
    {
        $firstWorkTime = $firstWorkTime ? strtotime($firstWorkTime) : null;
        $joinTime      = $joinTime ? strtotime($joinTime) : null;
        if (!$year) {
            $year = Dh::now('Y');
        }
        //拿年末尾计算工龄得出本年假期天数
        $yearBeginAt     = strtotime($year . '-01-01');//年初
        $yearEndAt       = strtotime($year . '-12-31');//年末
        $benefitStartDay = $joinTime ? strtotime('+1year', $joinTime) : null;

        $totalAnnual  = self::getTotalAnnual($firstWorkTime, $joinTime, $yearBeginAt, $yearEndAt, $endDay);
        $totalBenefit = self::getTotalBenefit($totalAnnual, $benefitStartDay, $yearBeginAt, $yearEndAt, $maxTotalBenefit);
        $plan         = [
            'totalAnnualHour'  => $totalAnnual * AttendanceVacation::VACATION_SEND_PER_EIGHT_HOURS,
            'totalAnnual'      => $totalAnnual,
            'totalBenefitHour' => $totalBenefit * AttendanceVacation::VACATION_SEND_PER_EIGHT_HOURS,
            'totalBenefit'     => $totalBenefit,
            'endDay'           => $endDay,
            'maxTotalBenefit'  => $maxTotalBenefit,
            'days'             => self::getVacationDaysThisYear($year),
        ];
        return $plan;
    }

    /**
     * 计算某年度，年假发放计划
     * @param int $firstWorkTime
     * @param int $joinTime
     * @param int $year
     * @return array
     * @author hurs
     */
    public static function getAnnualAndBenefitsPlan($firstWorkTime, $joinTime, $year = 0)
    {
        $firstWorkTime = $firstWorkTime ? strtotime($firstWorkTime) : null;
        $joinTime      = $joinTime ? strtotime($joinTime) : null;
        if (!$year) {
            $year = Dh::now('Y');
        }
        //拿年末尾计算工龄得出本年假期天数
        $yearBeginAt     = strtotime($year . '-01-01');//年初
        $yearEndAt       = strtotime($year . '-12-31');//年末
        $benefitStartDay = $joinTime ? strtotime('+1year', $joinTime) : null;

        $totalAnnual  = self::getTotalAnnual($firstWorkTime, $joinTime, $yearBeginAt, $yearEndAt, $endDay);
        $totalBenefit = self::getTotalBenefit($totalAnnual, $benefitStartDay, $yearBeginAt, $yearEndAt, $maxTotalBenefit);
        $plan         = [
            'totalAnnualHour'  => $totalAnnual * AttendanceVacation::VACATION_SEND_PER_EIGHT_HOURS,
            'totalAnnual'      => $totalAnnual,
            'totalBenefitHour' => $totalBenefit * AttendanceVacation::VACATION_SEND_PER_EIGHT_HOURS,
            'totalBenefit'     => $totalBenefit,
            'endDay'           => $endDay,
            'maxTotalBenefit'  => $maxTotalBenefit,
            'days'             => self::getVacationDaysThisYear($year),
            'planBenefit'      => 0,
        ];
        $sumAnnual    = 0;
        $sumBenefit   = 0;
        while ($yearBeginAt <= $yearEndAt) {
            $date    = date('Y-m-d', $yearBeginAt);
            $annual  = self::getTotalAnnual($firstWorkTime, $joinTime, $yearBeginAt, $yearBeginAt);
            $benefit = self::getTotalBenefit($annual, $benefitStartDay, $yearBeginAt, $yearBeginAt, $maxBenefit);
            if ($sumAnnual >= $plan['totalAnnual']) {
                $annual = 0;
            }
            //补差额
            if ($plan['endDay'] && !$plan['planBenefit'] && $maxTotalBenefit > $benefit && $maxBenefit > $benefit &&
                date('z', $yearBeginAt) + $plan['endDay'] >= $plan['days']
            ) {
                $plan['planBenefit'] = ($plan['totalBenefit'] - $sumBenefit) * $plan['days'] / $plan['endDay'];
            }
            $benefit = $plan['planBenefit'] ?: $benefit;
            $sumAnnual += $annual / $plan['days'];
            $sumBenefit += $benefit / $plan['days'];

            $plan['plan'][$date] = [
                'annual'     => $annual,
                'benefit'    => $benefit,
                'sumAnnual'  => round($sumAnnual, 5),
                'sumBenefit' => round($sumBenefit, 5),
                'maxBenefit' => $maxBenefit,
            ];
            $yearBeginAt += Dh::PERIOD_1DAY;
        }
        return $plan;
    }

    /**
     * 计算一段时间内的福利年假，未考虑补差额
     * @param int $totalAnnual 今年法定年假
     * @param int $benefitStartTime 开始计算福利年假时间
     * @param int $startTime
     * @param int $endTime
     * @param int $day 按公司年限福利年假
     * @return int 实际按上限15天福利年假
     * @author hurs
     */
    public static function getTotalBenefit($totalAnnual, $benefitStartTime, $startTime = 0, $endTime = 0, &$day = 0)
    {
        if (!$benefitStartTime) {
            $day = 0;
            return 0;
        }
        if (!$startTime) {
            $startTime = strtotime(date("Y-01-01"));
        }
        if (!$endTime) {
            $endTime = strtotime(date("Y-12-31"));
        }
        if ($benefitStartTime <= $endTime) {
            if ($benefitStartTime > $startTime) {
                $benefitDays = Dh::getDaysByInterestDate(date('Y-m-d', $benefitStartTime), date('Y-m-d', $endTime));
                $day         = floor(5 * $benefitDays / self::getVacationDaysThisYear(date("Y", $startTime)));
            } else {
                $day = Dh::compareTwoTimeDiffer(date('Y-m-d', $benefitStartTime), date('Y-m-d', $startTime))['year'] + 5;
            }
        } else {
            $day = 0;
        }
        return min(AttendanceVacation::SUM_ANNUAL_AND_COMPANY_BENEFITS - floor($totalAnnual), $day);
    }

    /**
     * 计算一段时间内，加权法定年假
     * @param int $firstWorkTime 首次工作时间
     * @param int $joinTime 加入公司时间
     * @param int $startTime
     * @param int $endTime
     * @param int $endDay 变化的后续日期的天数
     * @return int
     * @author hurs
     */
    public static function getTotalAnnual($firstWorkTime, $joinTime, $startTime = 0, $endTime = 0, &$endDay = 0)
    {
        if (!$firstWorkTime || !$joinTime) {
            $endDay = 0;
            return 0;
        }
        if (!$startTime) {
            $startTime = strtotime(date("Y-01-01"));
        }
        if (!$endTime) {
            $endTime = strtotime(date("Y-12-31"));
        }
        //工作不满1年=0天。工作满1年~10年 = 5天。工作10年~20年=10天，工作20+ =15天
        $config      = [
            AttendanceVacation::ANNUAL_COUNT_ZERO_DAYS  => $firstWorkTime,
            AttendanceVacation::ANNUAL_COUNT_FIVE_DAYS  => strtotime('+1year', $firstWorkTime),
            AttendanceVacation::ANNUAL_COUNT_TEN_DAYS   => strtotime('+10year', $firstWorkTime),
            AttendanceVacation::ANNUAL_COUNT_FIFTY_DAYS => strtotime('+20year', $firstWorkTime),
        ];
        $startAnnual = AttendanceVacation::ANNUAL_COUNT_ZERO_DAYS;
        $endAnnual   = AttendanceVacation::ANNUAL_COUNT_ZERO_DAYS;
        foreach ($config as $value => $time) {
            if ($startTime >= $time) {
                $startAnnual = $value;
            }
            if ($endTime >= $time) {
                $endAnnual = $value;
            }
        }
        if ($joinTime <= $startTime) {
            $beforeJoinDays = 0;
        } elseif ($joinTime > $endTime) {
            $endDay = 0;
            return 0;
        } else {
            $beforeJoinDays = Dh::getDaysByInterestDate(date("Y-m-d", $startTime), date('Y-m-d', $joinTime));
        }
        $year     = date('Y', $startTime);
        $endDay   = Dh::getDaysByInterestDate(date("$year-m-d", $firstWorkTime), date('Y-m-d', $endTime));
        $startDay = Dh::getDaysByInterestDate(date('Y-m-d', $startTime), date("$year-m-d", $firstWorkTime));
        $allDays  = $startDay + $endDay;
        $annual   = floor((max($startDay - $beforeJoinDays, 0) * $startAnnual + ($endDay + min($startDay - $beforeJoinDays, 0)) * $endAnnual) / $allDays);
        if ($year == date("Y", strtotime("+1year", $joinTime))) {
            $benefitBeginDay = Dh::getDaysByInterestDate(date("$year-m-d", $joinTime), date('Y-m-d', $endTime));
            $endDay          = min($benefitBeginDay, $endDay);
        }
        return $annual;
    }


    //计算员工全薪病假发放天数
    public static function getFullPaySick(User $user)
    {
        $regularAt = $user->regular_at;//转正日期
        $vacation  = AttendanceVacation::getVacation($user->id);
        if ($regularAt && $vacation && $vacation->actual_full_pay_sick == 0 && strtotime(Dh::now()) >= strtotime($regularAt)) {
            return 3 * AttendanceVacation::VACATION_SEND_PER_EIGHT_HOURS;
        } else {
            return 0;
        }
    }

    //加班申请流程处理
    static public function workOverTime($arr)
    {
        if ($arr && $arr['type'] && $arr['user_id'] && $arr['duration'] && $arr['entry_id'] && $arr['begin_time'] && $arr['end_time']) {
            if ($arr && $arr['type'] == '加班申请') {
                $holiday = AttendanceHoliday::isFestival(Dh::formatDate($arr['begin_time'])); //法定节假日加班申请不算调休
                if ($holiday) {
                    return false;
                }
                $userId   = $arr['user_id'];
                $duration = str_replace("小时", "", $arr['duration']);
                $entryId  = $arr['entry_id'];
                AttendanceVacation::addExtraDayOff($userId, $duration, $entryId, AttendanceVacation::WORK_OVERTIME_APPLY_ADD);
            }
        } else {
            throw new UserFixException('参数有误' . implode('--', $arr));
        }
    }

    //请假流程处理
    static public function leaveProcessHandle($entries = [])
    {
        foreach ($entries as $entry) {
            if (!isset($entry['form_data']['date_time_sub']['value']) || !isset($entry['form_data']['holiday_type']['value']) || !isset($entry['entry']['user_id'])) {
                throw new UserFixException('请假流程处理,传入参数不完整');
            }
            if ($entry['entry']['id']) {
                $entryId        = $entry['entry']['id'];
                $vacationChange = AttendanceVacationChange::where('change_entry_id', $entryId)->first();
                if ($vacationChange) {
                    throw new UserFixException('该流程已经执行过了！');
                }
            } else {
                throw new UserFixException('流程编号不存在');
            }
            $userId   = $entry['entry']['user_id'];
            $duration = intval(str_replace("", "小时", $entry['form_data']['date_time_sub']['value']));
            switch ($entry['form_data']['holiday_type']['value']) {
                case AttendanceVacationChange::CHANGE_TYPE_ANNUAL_STR:
                    AttendanceVacation::reduceAnnual($userId, $duration, $entryId);
                    break;
                case AttendanceVacationChange::CHANGE_TYPE_EXTRA_DAY_OFF_STR:
                    AttendanceVacation::reduceExtraDayOff($userId, $duration, $entryId);
                    break;
                case AttendanceVacationChange::CHANGE_TYPE_COMPANY_BENEFITS_STR:
                    AttendanceVacation::reduceCompanyBenefits($userId, $duration, $entryId);
                    break;
                case AttendanceVacationChange::CHANGE_TYPE_FULL_PAY_SICK_STR:
                    AttendanceVacation::reduceFullPaySick($userId, $duration, $entryId);
                    break;
//                default:
//                    throw new \Exception($entry['form_data']['holiday_type']['value'] . "不存在");
            }

            //通知考勤系统记录缺勤原因
            AttendanceSheet::notifyCauseOfAbsenceByLeave($userId, $entry['form_data']);

        }
    }

    /**
     * 销假流程之后恢复扣除的假期
     * @param $arr
     */
    static public function resumptionVacation($arr)
    {
        $userId      = $arr['user_id'];
        $entryId     = $arr['entry_id'];
        $holidayType = $arr['holiday_type'];
        $duration    = $arr['length'];
        if (!$arr || !$arr['user_id'] || !$arr['entry_id'] || !$arr['holiday_type'] || !$arr['length']) {
            throw new UserFixException('数据不完整！');
        }
        if ($duration < 0) {
            throw new UserFixException('销假数量小于零！');
        }
        $remark = AttendanceVacation::CONSOLE_VACATION_RECOVERY;
        switch ($holidayType) {
            case AttendanceVacationChange::CHANGE_TYPE_ANNUAL_STR:
                AttendanceVacation::addAnnual($userId, $duration, $entryId, $remark);
                break;
            case AttendanceVacationChange::CHANGE_TYPE_EXTRA_DAY_OFF_STR:
                AttendanceVacation::addExtraDayOff($userId, $duration, $entryId, $remark);
                break;
            case AttendanceVacationChange::CHANGE_TYPE_COMPANY_BENEFITS_STR:
                AttendanceVacation::addCompanyBenefits($userId, $duration, $entryId, $remark);
                break;
            case AttendanceVacationChange::CHANGE_TYPE_FULL_PAY_SICK_STR:
                AttendanceVacation::addFullPaySick($userId, $duration, $entryId, $remark);
                break;
        }
    }

    /**
     * 查询员工可用假期数据
     * @param $user_id
     * @return array
     */
    public static function formatVacation($user_id)
    {
        $workFlows      = Workflow::getFlowUserDataV1(Entry::WORK_FLOW_NO_HOLIDAY, null, null, Entry::STATUS_IN_HAND, 0, $user_id);
        $annual_handle  = 0;//处理中的请假流程,法定年假
        $extra_handle   = 0;//调休
        $company_handle = 0;//公司福利年假
        $sick_handle    = 0;//全薪病假
        foreach ($workFlows as $workFlow) {
            self::getHandleFlow($workFlow, $annual_handle, $extra_handle, $company_handle, $sick_handle);
        }
        $vacation         = AttendanceVacation::getVacation($user_id);
        $annual           = floor($vacation->annual - $annual_handle);
        $extra_day_off    = $vacation->extra_day_off - $extra_handle;
        $company_benefits = floor($vacation->company_benefits - $company_handle);
        $full_pay_sick    = floor($vacation->full_pay_sick - $sick_handle);

        //全薪病假和春节假只有过了试用期才能申请,无转正时间不可请两假,重置为零
        $user = User::where('id', $user_id)->first();

        $spring = [];
        if (!$user->regular_at || Dh::formatDate($user->regular_at) > Dh::todayDate()) {
            $full_pay_sick = 0;

            $spring = [
                'spring' => [
                    'name'    => '春节假',
                    'unit'    => '小时',
                    'can_use' => false,
                ],
            ];
        }

        $available_vacation = [
            'annual'              => [
                'name'    => AttendanceVacationChange::CHANGE_TYPE_ANNUAL_STR,
                'value'   => $annual,
                'unit'    => '小时',
                'can_use' => $annual >= 4,
            ],
            'extra_day_off'       => [
                'name'    => AttendanceVacationChange::CHANGE_TYPE_EXTRA_DAY_OFF_STR,
                'unit'    => '小时',
                'value'   => $extra_day_off,
                'can_use' => $annual < 4 && $extra_day_off >= 4,
            ],
            'company_benefits'    => [
                'name'    => AttendanceVacationChange::CHANGE_TYPE_COMPANY_BENEFITS_STR,
                'unit'    => '小时',
                'value'   => $company_benefits,
                'can_use' => $annual < 4 && $extra_day_off < 4 && $company_benefits >= 4,
            ],
            'compassionate_leave' => [
                'name'    => '事假',
                'unit'    => '小时',
                'can_use' => $annual < 4 && $company_benefits < 4 && $extra_day_off < 4,
            ],
            'full_pay_sick'       => [
                'name'    => AttendanceVacationChange::CHANGE_TYPE_FULL_PAY_SICK_STR,
                'unit'    => '小时',
                'value'   => $full_pay_sick,
                'can_use' => $full_pay_sick >= 4,
            ],
        ];

        return array_merge($available_vacation, $spring);
    }

    public static function getHandleFlow($workFlow, &$annual_handle = 0, &$extra_handle = 0, &$company_handle = 0, &$sick_handle = 0)
    {
        self::formatDateAmount($workFlow, $annual_handle, AttendanceVacationChange::CHANGE_TYPE_ANNUAL_STR);
        self::formatDateAmount($workFlow, $extra_handle, AttendanceVacationChange::CHANGE_TYPE_EXTRA_DAY_OFF_STR);
        self::formatDateAmount($workFlow, $company_handle, AttendanceVacationChange::CHANGE_TYPE_COMPANY_BENEFITS_STR);
        self::formatDateAmount($workFlow, $sick_handle, AttendanceVacationChange::CHANGE_TYPE_FULL_PAY_SICK_STR);
    }


    public static function formatDateAmount($workFlow, &$count, $type)
    {
        if (isset($workFlow['form_data']['holiday_type']['value']) && $workFlow['form_data']['holiday_type']['value'] == $type) {
            $value        = $workFlow['form_data']['date_time_sub']['value'] ?? 0;
            $format_value = str_replace("小时", "", $value);
            $count += $format_value;
        }
    }

    /**
     * 获取去年结余假期
     * @param AttendanceVacation $vacation
     * @return mixed
     */
    public static function getRestVacation($vacation)
    {
        if (!$vacation->user) {
            return [
                'restAnnual'     => 0,
                'restBenefit'    => 0,
                'annual'         => 0,
                'benefit'        => 0,
                'actualAnnual'   => 0,
                'actualBenefits' => 0,
                'totalAnnual'    => 0,
                'totalBenefit'   => 0,
            ];
        }
        $plan                   = static::getAnnualAndBenefitSimplePlan($vacation->user->detail->first_work_time, $vacation->user->join_at);
        $plan['restAnnual']     = $vacation->getLastYearAnnual();
        $plan['restBenefit']    = $vacation->getLastYearBenefit();
        $plan['annual']         = $vacation->annual;
        $plan['benefit']        = $vacation->company_benefits;
        $plan['actualAnnual']   = $vacation->actual_annual;
        $plan['actualBenefits'] = $vacation->actual_company_benefits;
        return $plan;
    }

    /**
     * 获取当年年假计算天数
     * @param $year
     * @return int
     * @author hurs
     */
    public static function getVacationDaysThisYear($year)
    {
        return Dh::getDaysByInterestDate("$year-01-01", "$year-12-31");
    }

    public static function checkHoliday(User $user, $date = '')
    {

        if (!$date) {
            $date = date('Y-m-d');
            $year = date('Y');
        } else {
            $year = date('Y', strtotime($date));
        }
        DB::beginTransaction();
        $vacation = AttendanceVacation::getVacation($user->id);
        if (!$vacation) {
            $vacation                   = new AttendanceVacation();
            $vacation->user_id          = $user->id;
            $vacation->annual           = 0;
            $vacation->company_benefits = 0;
            $vacation->full_pay_sick    = 0;
            $vacation->extra_day_off    = 0;
            $vacation->save();
        }
        $plan       = VacationManageService::getAnnualAndBenefitsPlan($user->detail->first_work_time, $user->join_at, $year);
        $today      = $plan['plan'][$date];
        $sumAnnual  = $today['sumAnnual'];
        $sumBenefit = $today['sumBenefit'];
        $desAnnual  = floor($sumAnnual) * AttendanceVacation::VACATION_SEND_PER_EIGHT_HOURS - $vacation->actual_annual;
        $desBenefit = floor($sumBenefit) * AttendanceVacation::VACATION_SEND_PER_EIGHT_HOURS - $vacation->actual_company_benefits;
        if ($desAnnual > 0) {
            $vacation->actual_annual += $desAnnual;
            $vacation->annual += $desAnnual;
        } else {
            $desAnnual = 0;
        }
        if ($desBenefit > 0) {
            $vacation->actual_company_benefits += $desBenefit;
            $vacation->company_benefits += $desBenefit;
        } else {
            $desBenefit = 0;
        }
        $sumFullPaySickInDay = VacationManageService::getFullPaySick($user);//一次性发放
        $vacation->full_pay_sick += $sumFullPaySickInDay;
        $vacation->actual_full_pay_sick += $sumFullPaySickInDay;
        if ($vacation->save()) {
            //添加假期变动记录
            AttendanceVacationChange::createAnnualChangeLog($user->id, $vacation, $desAnnual);
            AttendanceVacationChange::createBenefitChangeLog($user->id, $vacation, $desBenefit);
            AttendanceVacationChange::createFullPaySickChangeLog($user->id, $vacation, $sumFullPaySickInDay);
        } else {
            throw new DevFixException('按月分配假期，数据库更新失败');
        }
        DB::commit();
    }
}
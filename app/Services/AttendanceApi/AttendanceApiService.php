<?php
namespace App\Services\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Models\AttendanceApi\AttendanceApi;
use App\Models\AttendanceApi\AttendanceApiClasses;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\AttendanceApi\AttendanceApiNationalHolidays;
use App\Models\AttendanceApi\AttendanceApiOvertimeRule;
use App\Models\AttendanceApi\AttendanceApiScheduling;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class TravelsService
 *
 * @package App\Services\Attendance
 */
class AttendanceApiService
{
    const API_STATUS_NORMAL = 1;
    const API_STATUS_INVALID = 0;
    const CLOCK_ADDRESS_OUT = 2;    //外勤
    const CLOCK_ADDRESS_IN = 1;    //正常

    const DEFAULT_ID = 1; //默认ID

    const BEGIN_WORK = 1;   //上班打卡
    const END_WORK = 2;    //下班打卡

    const WORKING_TO_REST = 1;  //工作日转休息
    const REST_TO_WORKING = 2;  //休息转工作日


    const CLOCK_ANOMALY_NORMAL = 0;     //正常
    const CLOCK_ANOMALY_LATE = 1;     //迟到
    const CLOCK_ANOMALY_LEAVE_EARLY = 2;  //早退
    const CLOCK_ANOMALY_ADDWORK = 3;   //加班
    const CLOCK_ANOMALY_MISSING = 4;   //缺卡
    const CLOCK_ANOMALY_ABSENTEEISM = 5;   //旷工

    const SERIOUS_LATE = 1; //严重迟到
    const ABSENTEEISM = 1; //旷工

    const ATTENDANCE_SYTTEM_FIXED = 1;  //固定制

    const ATTENDANCE_SYTTEM_SORT = 2;  //排班制
    const ATTENDANCE_SYTTEM_SORT_CYCLE_ONE = 1; //做一休一
    const ATTENDANCE_SYTTEM_SORT_CYCLE_TWO = 2; //两班轮回
    const ATTENDANCE_SYTTEM_SORT_CYCLE_THR = 3; //三班倒

    const ATTENDANCE_SYTTEM_FREE = 3;  //自由制

    const ATTENDANCE_STAFF_TRUE = 1;    //参加考勤
    const ATTENDANCE_STAFF_FALSE = 2;   //不参加考勤

    const ATTENDANCE_CLASSES_SIESTA = 1;   //午休开启

    const ATTENDANCE_CLASSES_ONE = 1;   //一天 一次上下班
    const CLASSES_ONE_BEGIN_WORK_TIME1 = '09:00:00';  //默认上班时间
    const CLASSES_ONE_END_WORK_TIME1 = '18:00:00';    //默认下班时间
    const CLASSES_ONE_BEGIN_CLOCK_TIME1 = 480;  //默认上班最早打卡时间
    const CLASSES_ONE_END_CLOCK_TIME1 = 720;  //默认下班班最早打卡时间

    const ATTENDANCE_CLASSES_TWO = 2;   //一天 两次上下班
    const CLASSES_TWO_BEGIN_WORK_TIME1 = '09:00:00';  //默认上班时间
    const CLASSES_TWO_END_WORK_TIME1 = '12:00:00';    //默认下班时间
    const CLASSES_TWO_BEGIN_WORK_TIME2 = '14:00:00';  //默认上班时间
    const CLASSES_TWO_END_WORK_TIME2 = '18:00:00';    //默认下班时间

    const ATTENDANCE_CLASSES_THR = 3;   //一天 三次上下班
    const CLASSES_THR_BEGIN_WORK_TIME1 = '09:00:00';  //默认上班时间
    const CLASSES_THR_END_WORK_TIME1 = '11:00:00';    //默认下班时间
    const CLASSES_THR_BEGIN_WORK_TIME2 = '12:00:00';  //默认上班时间
    const CLASSES_THR_END_WORK_TIME2 = '15:00:00';    //默认下班时间
    const CLASSES_THR_BEGIN_WORK_TIME3 = '16:00:00';  //默认上班时间
    const CLASSES_THR_END_WORK_TIME3 = '18:00:00';    //默认下班时间

    const WORKING_OVERTIME_YES = 1; //工作允许加班
    const REST_OVERTIME_YES = 1;    //休息允许加班
    const IS_COUNT_YES = 1;     //已经统计

    const OVERTIME_TYPE_CHECK = 1;      //1-需审批，以审批单为准
    const OVERTIME_TYPE_CHECK_CLOCK = 2;    //2-需审批，以打卡为准，但不能超过审批时长。
    const OVERTIME_TYPE_CLOCK = 3;  //3-无需审批，根据打卡时间为准',

    const OVERTIME_DATE_WORKINGDAY = 1; //工作日
    const OVERTIME_DATE_WEEKEND = 2;    //周末
    const OVERTIME_DATE_HOLIDAYS = 3;   //节假日

    const GETOUT_NORMAL = 1;    //允许外勤打卡

    const DEFAULT_WORK_COUNT_TIME = 8; //默认每天工作时长


    /**
    *   获取用户上班时间
     */
    public static function getUserWorkTime($data, $user, $rules, $YmdHis){
        if(empty($rules)) {
            //没有设置考勤组， 返回默认考勤规则
            return self::getDefaultWorkTime($data, $YmdHis);
        }else{
            if(empty($rules->attendance)) {
                //考勤组ID失效， 返回默认考勤规则
                 return self::getDefaultWorkTime($data, $YmdHis);
            }
        }

        switch ($rules->attendance->system_type){
            case self::ATTENDANCE_SYTTEM_FIXED:
            case self::ATTENDANCE_SYTTEM_SORT:
                if(!$rules['attendance']['classes']){
                    return self::getDefaultWorkTime($data, $YmdHis);
                }else{
                    $classes = $rules['attendance']['classes'];
                    return self::getBeginEndWorkTimeBYClassesType($classes, $user, $data, $YmdHis);
                }
                break;
            case self::ATTENDANCE_SYTTEM_FREE:
                return self::getDefaultWorkTime($data, $YmdHis);
                break;
            default:
                break;
        }
    }

    /**
    *   根据班次类型， 获取上下班时间
     */
    public static function getBeginEndWorkTimeBYClassesType($classes, $user, $data, $YmdHis){
        $clock_count = AttendanceApiClock::query()->where([
            'user_id' => $user->id,
            'type' => $data['type'],
            'clock_nums' => self::ATTENDANCE_CLASSES_ONE,
            'dates' => $data['dates'],
        ])->count();
        if($classes['type'] == self::ATTENDANCE_CLASSES_ONE) {
            $clock = self::isWorkTimeByType($classes, self::ATTENDANCE_CLASSES_ONE, $data, $YmdHis);
            return $clock;
        }elseif ($classes['type'] == self::ATTENDANCE_CLASSES_TWO){
            for ($i = $clock_count; $i <= 1; $i++){
                $clock = self::isWorkTimeByType($classes, $i + 1, $data, $YmdHis);
                if($clock !== false) {
                    return $clock;
                }
            }
            return false;
        }elseif ($classes['type'] == self::ATTENDANCE_CLASSES_THR){
            for ($i = $clock_count; $i <= 2; $i++){
                $clock = self::isWorkTimeByType($classes, $i + 1, $data, $YmdHis);
                if($clock !== false) {
                    return $clock;
                }
            }
            return false;
        }
    }

    /**
    *   根据类型 判断是否满足上下班条件
     */
    public static function isWorkTimeByType($classes, $type, $data, $YmdHis){
        $end_dates = Carbon::parse($data['dates']);
        if($data['type'] == self::BEGIN_WORK){
            $clock_begin = $classes['clock_time_begin'.$type] ?: self::CLASSES_ONE_BEGIN_CLOCK_TIME1;
            $begin_time = Dh::calcSubTime($classes['work_time_begin'.$type], $clock_begin, $data['dates']);
            
            if(Dh::compare2Dates($begin_time, $YmdHis)) return false;
        }else{
            if(Dh::compare2Dates($classes['work_time_begin'.$type], $classes['work_time_end'.$type])){
                $end_dates->addDay();
            }
            $clock_end = $classes['clock_time_end'.$type] ? $classes['clock_time_end'.$type] : self::CLASSES_ONE_END_CLOCK_TIME1;
            $end_time = Dh::calcAddTime($classes['work_time_end'.$type], $clock_end, $end_dates->toDateString());
            if(Dh::compare2Dates($YmdHis, $end_time)) return false;
        }

        return [
            'begin_work_time' => $data['dates'] ." ". $classes['work_time_begin'.$type],
            'end_work_time' => $end_dates->toDateString() ." ". $classes['work_time_end'.$type],
            'clock_nums' => $type,
        ];
    }


    /**
    *   请假，加班，出差，外勤
     */
    public static function getVerifyInfo($user){
        //TODO 排除 请假，加班，出差，外勤 等代码同步过来后在添加
        return false;
    }


    /**
     * 判断今天是否是工作日
     * @param date    $date        需要判断时间
     * @param string $rule 工作日
     * @return boolean
     */
    public static function isWorkingDay($date, $rule) {
        //排班制员工 没有排班信息 不算工作日
        if(Q($rule, 'attendance', 'system_type') == self::ATTENDANCE_SYTTEM_SORT &&
            !Q($rule, 'attendance', 'classes')){
            return false;
        }
        //判断是否为国家法定节假日
        $holidays = AttendanceApiNationalHolidays::query()->where(['dates'=> $date])->first();
        if(empty($holidays)){
            $week = Dh::getWeek($date);
            $work_day = explode(',', Q($rule, 'attendance', 'weeks'));
            return in_array($week, $work_day) ? true : false;
        }else{
            if ($holidays['type'] == self::WORKING_TO_REST) {
                return false;
            }elseif ($holidays['type'] == self::REST_TO_WORKING){
                return true;
            }
        }
    }

    /**
     *   计算工作日加班时间
     */
    public static function getWorkOvertime($rules, $YmdHis, $check_info, $dates = ""){
    $overtime_rule = Q($rules, 'attendance', 'overtimeRule');
        switch ($overtime_rule['working_overtime_type']) {
            case AttendanceApiService::OVERTIME_TYPE_CHECK:
                //需要审核，  以审批单为准
                /**
                 *   直接审核通过的时候触发，不需要通过打卡计算
                 */
                return 0;
                break;
            case AttendanceApiService::OVERTIME_TYPE_CHECK_CLOCK:
                //需要审核，  以打卡为准，但是不能超过审批时间
                $overtime = self::countWorkOvertime($rules, $YmdHis, $dates);
                if($check_info === false) {
                    return $overtime;
                }else{
                    return $overtime >= $check_info->duration ? $check_info->duration : $overtime;
                }
                break;
            case AttendanceApiService::OVERTIME_TYPE_CLOCK:
                //不需要审核，  以打卡为准
                $overtime = self::countWorkOvertime($rules, $YmdHis, $dates);
                return $overtime;
                break;
            default:
                return 0;
                break;
        }
    }

    public static function countWorkOvertime($rules, $YmdHis, $dates = ""){
        $overtime_rule = Q($rules, 'attendance', 'overtimeRule');
        //工作日不允许加班。直接返回
        if ($overtime_rule['is_working_overtime'] !== AttendanceApiService::WORKING_OVERTIME_YES) return 0;
        //工作日允许加班
        //获取设置中最后一次下班时间
        $classes = Q($rules, 'attendance', 'classes');
        $type = $classes['type'];
        $last_clock_time = $classes['work_time_end'.$type];
        //加班开始时间 = 最后一次打卡时间 + 后台设置的加班开始时间
        //$overtime_begin_time = date('Y-m-d H:i:s', strtotime($last_clock_time) + $overtime_rule->working_begin_time * 60);
        $overtime_begin_time = Carbon::parse($dates." $last_clock_time")->addMinutes(Q($overtime_rule, 'working_begin_time'));
        //打卡超过加班时间
        if (Carbon::parse($YmdHis)->gte($overtime_begin_time)) {
            //计算加班时间
            $overtime = intval(Dh::timeDiff($overtime_begin_time->toDateTimeString(), $YmdHis) / $overtime_rule->working_min_overtime);

            $overtime = $overtime * $overtime_rule->working_min_overtime;
            return $overtime;
        }else{
            return 0;
        }
    }

    /**
     *   计算休息加班时间
     */
    public static function getRestOvertime($rules, $YmdHis, $check_info, $begin_time){
        $overtime_rule = Q($rules, 'attendance', 'overtimeRule');
        switch ($overtime_rule['rest_overtime_type']) {
            case AttendanceApiService::OVERTIME_TYPE_CHECK:
                //需要审核，  以审批单为准
                /**
                 *   直接审核通过的时候触发，不需要通过打卡计算
                 */
                return 0;
                break;
            case AttendanceApiService::OVERTIME_TYPE_CHECK_CLOCK:
                //需要审核，  以打卡为准，但是不能超过审批时间
                $overtime = self::countRestOvertime($rules, $YmdHis, $begin_time);
                if($check_info === false) {
                    return $overtime;
                }else{
                    return $overtime >= $check_info->duration ? $check_info->duration : $overtime;
                }
                break;
            case AttendanceApiService::OVERTIME_TYPE_CLOCK:
                //不需要审核，  以打卡为准
                $overtime = self::countRestOvertime($rules, $YmdHis, $begin_time);
                return $overtime;
                break;
            default:
                return 0;
                break;
        }
    }

    public static function countRestOvertime($rules, $YmdHis, $begin_time){
        $overtime_rule = Q($rules, 'attendance', 'overtimeRule');
        //休息日不允许加班。直接返回
        if ($overtime_rule['is_rest_overtime'] !== AttendanceApiService::WORKING_OVERTIME_YES) return 0;
        //休息日允许加班
        //加班时间 = 下班打卡时间 - 第一次上班打卡时间
        $overtime = intval(Dh::timeDiff($begin_time->datetimes, $YmdHis) / $overtime_rule->working_min_overtime);
        $overtime = $overtime * $overtime_rule->working_min_overtime;
        return $overtime;
    }

    /**
     *   获取默认上下班时间
     */
    public static function getDefaultWorkTime($data, $YmdHis){
        $begin_time = Dh::calcSubTime(self::CLASSES_ONE_BEGIN_WORK_TIME1, self::CLASSES_ONE_BEGIN_CLOCK_TIME1, $data['dates']);
        if(Dh::compare2Dates($begin_time, $YmdHis)) return false;
        $end_time = Dh::calcAddTime(self::CLASSES_ONE_END_WORK_TIME1, self::CLASSES_ONE_END_CLOCK_TIME1, $data['dates']);
        if(Dh::compare2Dates($YmdHis, $end_time)) return false;
        return [
            'begin_work_time' => self::CLASSES_ONE_BEGIN_WORK_TIME1,
            'end_work_time' => self::CLASSES_ONE_END_WORK_TIME1,
            'clock_nums' => self::ATTENDANCE_CLASSES_ONE,
        ];
    }


    /**
     *   获取默认考勤规则
     */
    public static function getDefaultAttendance(){
        return AttendanceApi::query()->with(['classes','overtimeRule'])->find(self::DEFAULT_ID);
    }

    /**
     *   获取默认班次
     */
    public static function getDefaultAttendanceClasses(){
        return AttendanceApiClasses::query()->find(self::DEFAULT_ID);
    }

    /**
     *   获取默认加班规则
     */
    public static function getDefaultAttendanceOvertimeRule(){
        return AttendanceApiOvertimeRule::query()->find(self::DEFAULT_ID);
    }

    /*
     * 获取一天的工作时间
     * */
    public static function getWorkTimeByDay($rules, $clock_info){
        if($rules["attendance"]["system_type"] == self::ATTENDANCE_SYTTEM_FREE){
            //自由制不计算工作时长
            return 0;
        }else{
            //固定制， 排版制 计算工作时长
            $work_time = 0;
            $type = $rules["attendance"]["classes"]
                ? $rules["attendance"]["classes"]["type"]
                : AttendanceApiService::ATTENDANCE_CLASSES_ONE;
            switch ($type){
                case AttendanceApiService::ATTENDANCE_CLASSES_ONE:
                    foreach ($clock_info as $k=>$v){
                        if($v['type'] == AttendanceApiService::BEGIN_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_ONE){
                            $begin1 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::END_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_ONE){
                            $end1 = strtotime($v["datetimes"]);
                        }
                    }
                    if(isset($begin1) && isset($end1)) {
                        $work_time += $end1 - $begin1;
                    }
                    break;
                case AttendanceApiService::ATTENDANCE_CLASSES_TWO:
                    foreach ($clock_info as $k=>$v){
                        if($v['type'] == AttendanceApiService::BEGIN_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_ONE){
                            $begin1 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::END_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_ONE){
                            $end1 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::BEGIN_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_TWO){
                            $begin2 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::END_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_TWO){
                            $end2 = strtotime($v["datetimes"]);
                        }
                    }
                    if(isset($begin1) && isset($end1)) $work_time += $end1 - $begin1;
                    if(isset($begin2) && isset($end2)) $work_time += $end2 - $begin2;
                    break;
                case AttendanceApiService::ATTENDANCE_CLASSES_THR:
                    foreach ($clock_info as $k=>$v){
                        if($v['type'] == AttendanceApiService::BEGIN_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_ONE){
                            $begin1 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::END_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_ONE){
                            $end1 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::BEGIN_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_TWO){
                            $begin2 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::END_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_TWO){
                            $end2 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::BEGIN_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_THR){
                            $begin3 = strtotime($v["datetimes"]);
                        }elseif($v['type'] == AttendanceApiService::END_WORK &&
                            $v["clock_nums"] == AttendanceApiService::ATTENDANCE_CLASSES_THR){
                            $end3 = strtotime($v["datetimes"]);
                        }
                    }
                    if(isset($begin1) && isset($end1)) $work_time += $end1 - $begin1;
                    if(isset($begin2) && isset($end2)) $work_time += $end2 - $begin2;
                    if(isset($begin3) && isset($end3)) $work_time += $end3 - $begin3;
                    break;
                default:
                    break;
            }
            return $work_time;
        }
    }

    /*
     *  获取工作时间
     * */
    public static function getWorkTime($rules, $working_date){
        $work_time = 0;
        if($rules["attendance"]["system_type"] == self::ATTENDANCE_SYTTEM_FREE) return $work_time;
        //未查询到班次
        foreach ($working_date as $wd_k=>$wd_v){
            $work_time += self::getWorkTimeByDay($rules, $wd_v);
        }
        return $work_time;
    }

    public static function getUserWorkBeginEndTime($rules, $user, $data){
        $siesta = [
            'begin' => 0,
            'end' => 0,
        ];
        switch (Q($rules, 'attendance', 'system_type')){
            case AttendanceApiService::ATTENDANCE_SYTTEM_FIXED:
                $classes = Q($rules, 'attendance', 'classes');
                $title = $classes["title"];
                if($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_ONE){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                }elseif($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_TWO){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                    $work_time .= " ".$classes["work_time_begin2"]."-".$classes["work_time_end2"];
                }elseif($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_THR){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                    $work_time .= " ".$classes["work_time_begin2"]."-".$classes["work_time_end2"];
                    $work_time .= " ".$classes["work_time_begin3"]."-".$classes["work_time_end3"];
                }
                if($classes['is_siesta'] == self::ATTENDANCE_CLASSES_SIESTA){
                    $siesta = [
                        'begin' => $classes["begin_siesta_time"],
                        'end' => $classes["end_siesta_time"],
                    ];
                }
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_SORT:
                $scheduling = AttendanceApiScheduling::getSchedulingInfo($user->id, $data['dates']);
                if(empty($classes)){
                    return [
                        'title' => "未排班",
                        'work_time' => "",
                    ];
                }
                $classes = $scheduling->classes;
                $title = $classes["title"];
                if($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_ONE){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                }elseif($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_TWO){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                    $work_time .= " ".$classes["work_time_begin2"]."-".$classes["work_time_end2"];
                }elseif($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_THR){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                    $work_time .= " ".$classes["work_time_begin2"]."-".$classes["work_time_end2"];
                    $work_time .= " ".$classes["work_time_begin3"]."-".$classes["work_time_end3"];
                }
                if($classes['is_siesta'] == self::ATTENDANCE_CLASSES_SIESTA){
                    $siesta = [
                        'begin' => $classes["begin_siesta_time"],
                        'end' => $classes["end_siesta_time"],
                    ];
                }
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_FREE:
                $title = "自由制";
                $work_time = $rules["attendance"]["clock_node"];
                break;
        }
        return [
            'title' => $title,
            'work_time' => $work_time,
            'siesta' => $siesta,
        ];
    }

    public static function getUserClockInfo($rules, $user, $data){
        switch ($rules["attendance"]["system_type"]){
            case AttendanceApiService::ATTENDANCE_SYTTEM_FIXED:
                $classes = $rules["attendance"]["classes"];
                $title = $classes["title"];
                if($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_ONE){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                }elseif($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_TWO){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                    $work_time .= " ".$classes["work_time_begin2"]."-".$classes["work_time_end2"];
                }elseif($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_THR){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                    $work_time .= " ".$classes["work_time_begin2"]."-".$classes["work_time_end2"];
                    $work_time .= " ".$classes["work_time_begin3"]."-".$classes["work_time_end3"];
                }
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_SORT:
                $scheduling = AttendanceApiScheduling::getSchedulingInfo($user->id, $data['dates']);
                if(empty($scheduling)){
                    $classes = AttendanceApiClasses::query()->find(self::DEFAULT_ID);
                }else{
                    $classes = AttendanceApiClasses::query()->find($scheduling->classes_id);
                }

                $title = $classes["title"];
                if($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_ONE){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                }elseif($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_TWO){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                    $work_time .= " ".$classes["work_time_begin2"]."-".$classes["work_time_end2"];
                }elseif($classes["type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_THR){
                    $work_time = $classes["work_time_begin1"]."-".$classes["work_time_end1"];
                    $work_time .= " ".$classes["work_time_begin2"]."-".$classes["work_time_end2"];
                    $work_time .= " ".$classes["work_time_begin3"]."-".$classes["work_time_end3"];
                }
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_FREE:
                $title = "自由制";
                $work_time = $rules["attendance"]["clock_node"];
                break;
        }
        return $work_time;
    }


    /**
     * 根据时间区间， 分别计算每天的出勤小时
     * @param $user_id
     * @param $begin
     * @param $end
     * @return array
     */
    public static function getWorkTimeByUserAndDate($user_id, $begin, $end){
        //获取总天数
        $days = Dh::getbetweenDay($begin, $end);
        $user = User::find($user_id);
        //获取用户考勤规则
        $rules = AttendanceApiStaff::getUserAttendanceRule($user);
        $res = [];
        $i = 1;
        foreach ($days as $k=>$v){
            //自由制， 直接返回 8 小时；
            if(Q($rules, 'attendance', 'system_type') == self::ATTENDANCE_SYTTEM_FREE){
                $res[] = [
                    'dates' => $v,
                    'times' => self::DEFAULT_WORK_COUNT_TIME,
                ];
            }else{
                //固定制 和 自由制
                $begin = Carbon::parse($begin);
                $end = Carbon::parse($end);

                $times = $siesta = 0;
                $data = ['dates'=> $v];
                $is_workday = self::isWorkingDay($v, $rules);
                //工作日，根据上下班时间 计算请假时长
                if($is_workday === true){
                    //获取上下班时间
                    $work_time = self::getUserWorkBeginEndTime($rules, $user, $data);
                    //午休时间
                    $siesta_begin = $work_time['siesta']['begin']
                        ? Carbon::parse($v.' '. $work_time['siesta']['begin'])
                        : 0;
                    $siesta_end = $work_time['siesta']['end']
                        ? Carbon::parse($v.' '. $work_time['siesta']['end'])
                        : 0;

                    //计算午休时间
                    if($siesta_begin && $siesta_end){
                        $siesta = $siesta_end->diffInMinutes($siesta_begin);
                        //请假第一天开始时间
                        if($i == 1 && $begin->gt($siesta_begin) && $begin->lt($siesta_end))
                            $siesta = $siesta_end->diffInMinutes($begin);
                        //请假最后一天结束时间
                        if ($i == count($days) && $end->gt($siesta_begin) && $end->lt($siesta_end))
                            $siesta = $end->diffInMinutes($siesta_begin);
                    }else{
                        $siesta = 0;
                    }
                    //计算请假时长
                    $e = explode(' ', $work_time['work_time']);
                    $begin_leave = $end_leave = 0;
                    foreach ($e as $v1){
                        $ex = explode('-', $v1);
                        if(count($ex) >= 2){
                            $ex_begin = Carbon::parse($v . ' '. $ex[0]);
                            $ex_end = Carbon::parse($v . ' '. $ex[1]);
                            //请假第一天开始时间
                            if($i == 1 && $begin->gt($ex_begin))
                                $begin_leave = $begin->diffInMinutes($ex_begin);
                            //请假最后一天结束时间
                            if ($i == count($days) && $ex_end->gt($end))
                                $end_leave = $ex_end->diffInMinutes($end);

                            $times += $ex_end->diffInMinutes($ex_begin);
                        }
                    }
                    $res[] = [
                        'dates' => $v,
                        //'times' => $times - $begin_leave - $end_leave - $work_time['siesta'],
                        'times' => $times - $begin_leave - $end_leave - $siesta,
                    ];
                }else{
                    $res[] = [
                        'dates' => $v,
                        'times' => 0,
                    ];
                }
            }
            $i++;
        }
        return $res;
    }
}

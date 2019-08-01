<?php
namespace App\Services\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\AttendanceApi\AttendanceApiNationalHolidays;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Models\User;
use App\Models\Vacations\VacationBusinessTripRecord;
use App\Models\Vacations\VacationLeaveRecord;
use App\Models\Vacations\VacationOutSideRecord;
use App\Models\Vacations\VacationTripRecord;
use App\Models\Workflow\Entry;
use App\Repositories\AttendanceApi\CountsRespository;
use Carbon\Carbon;
use Doctrine\DBAL\Driver\AbstractDB2Driver;
use Illuminate\Database\Query\Builder;

class CountsService
{
    const LEAVE_ANNUAL_LEAVE = 1;   //年假
    const LEAVE_ADJUST_REST = 2;    //调休
    const LEAVE_SICK_LEAVE = 3;     //病假
    const LEAVE_OF_ABSENCE = 7;     //事假

    public static $weeks = [
        '1'=> '星期一','2'=> '星期二','3'=> '星期三','4'=> '星期四',
        '5'=> '星期五','6'=> '星期六','7'=> '星期日',
    ];

    /*
     * 根据异常信息进行分类
     * */
    public static function getAnomalyInfo($anomaly, $rules, $user, &$list){
        foreach ($anomaly as $k=>$v){
            $work_time_info = AttendanceApiService::getUserClockInfo($rules, $user, $v);
            $work_time_ex = explode(" ",$work_time_info);
            $begin_end_time = [
                '0' => AttendanceApiService::CLASSES_ONE_BEGIN_WORK_TIME1,
                '1' => AttendanceApiService::CLASSES_ONE_END_WORK_TIME1,
            ];
            if(!empty($v->clock)){
                $type = $v->clock->clock_nums - 1;
                $begin_end_time = explode("-",$work_time_ex[$type]);
            }
            $is_serious_late = $v['is_serious_late'] == AttendanceApiService::SERIOUS_LATE
                ? "严重" : "";
            $hour = intval($v['anomaly_time'] / 60);
            $min = intval(intval($v['anomaly_time'] % 60));

            switch ($v['anomaly_type']){
                case AttendanceApiService::CLOCK_ANOMALY_NORMAL:
                    //正常考勤
                    $list['normal'][] = [
                        'dates'=> $v['dates'],
                        'weeks'=> self::$weeks[Dh::getWeek($v['dates'])],
                    ];
                    continue;
                case AttendanceApiService::CLOCK_ANOMALY_LATE:
                    //达到旷工标准
                    if($v['is_absenteeism'] == AttendanceApiService::ABSENTEEISM){
                        foreach ($list['working_date'] as $k1=>$v1){
                            if($v1['dates'] == $v['dates']){
                                unset($list['working_date'][$k1]);
                                $list['absenteeism'][] = [
                                    'dates'=> $v['dates'],
                                    'weeks'=> self::$weeks[Dh::getWeek($v['dates'])],
                                ];
                            }
                        }
                    }else{
                        $list['late'][] = [
                            'dates' => $v['dates'],
                            'anomaly_time' => $v['anomaly_time'],
                            'is_serious_late' => $v['is_serious_late'],
                            'weeks'=> self::$weeks[Dh::getWeek($v['dates'])],
                            'message'=> "上班{$is_serious_late}迟到: {$hour}小时{$min}分钟",
                            'begin'=> substr($begin_end_time[0],0,5),
                        ];
                        $list["late_nums"] = $list["late_nums"] ? $list["late_nums"] : 0 + $v['anomaly_time'];
                    }
                    continue;
                case AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY:
                    //早退
                    $list['leave'][] = [
                        'dates' => $v['dates'],
                        'anomaly_time' => $v['anomaly_time'],
                        'weeks'=> self::$weeks[Dh::getWeek($v['dates'])],
                        'message'=> "下班早退: {$hour}小时{$min}分钟",
                        'end'=> substr($begin_end_time[1],0,5),
                    ];
                    $list["leave_nums"] = $list["leave_nums"] ? $list["leave_nums"] : 0 + $v['anomaly_time'];

                    continue;
                case AttendanceApiService::CLOCK_ANOMALY_ADDWORK:
                    //加班时长
                    if($v['is_count'] == AttendanceApiService::IS_COUNT_YES){
                        $is_working_day = AttendanceApiService::isWorkingDay($v['dates'], $rules);
                        $overtime_rule = Q($rules, 'attendance', 'overtimeRule');
                        if($is_working_day === true){
                            $min_overtime = $overtime_rule["working_min_overtime"];
                            $clock_time = substr(Carbon::parse ($begin_end_time[1])->addMinutes($overtime_rule["working_begin_time"]),0,-3)
                                ." - ". substr($v->clock->datetimes,0,-3);
                        }else{
                            $min_overtime = $overtime_rule["rest_min_overtime"];
                            $clock_list = AttendanceApiClock::query()->where([
                                'dates'=> $v["dates"],
                                'user_id' => $user->id,
                                'clock_nums' => $v['clock_nums'],
                                'status'=> ConstFile::API_STATUS_NORMAL
                            ])->get();
                            $cl_dates = [];
                            foreach ($clock_list as $cl_key => $cl_val){
                                $cl_dates[] = Carbon::parse($cl_val['datetimes'])->format('Y-m');
                            }
                            $clock_time = implode('-',$cl_dates);
                        }

                        $list['overtime'][] = [
                            'dates' => $v['dates'],
                            'overtime_date_type'=>$v['overtime_date_type'],
                            'anomaly_time' => $v['anomaly_time'] / $min_overtime,
                            'weeks'=> self::$weeks[Dh::getWeek($v['dates'])],
                            'clock_time'=> $clock_time,
                        ];

                        $list["overtime_nums"] = ($list["overtime_nums"] ? $list["overtime_nums"] : 0) + ($v['anomaly_time'] / $min_overtime);
                    }
                    continue;
                case AttendanceApiService::CLOCK_ANOMALY_MISSING:
                    //缺卡
                    $list['missing'][] = [
                        'dates'=> $v['dates'],
                        'weeks'=> self::$weeks[Dh::getWeek($v['dates'])],
                    ];
                    continue;
                case AttendanceApiService::CLOCK_ANOMALY_ABSENTEEISM:
                    //旷工
                    $list['absenteeism'][] = [
                        'dates'=> $v['dates'],
                        'weeks'=> self::$weeks[Dh::getWeek($v['dates'])],
                    ];
                    continue;
                default:
                    break;
            }
        }
        return $list;
    }

    /*
     * 排除节假日
     * */
    public static function getListRemoveHolidays($att_false, $rules, $scheduling = ""){
        $rest = [];
        switch ($rules["attendance"]["system_type"]){
            //固定制
            case AttendanceApiService::ATTENDANCE_SYTTEM_FIXED:
            case AttendanceApiService::ATTENDANCE_SYTTEM_FREE:
                $holidays = AttendanceApiNationalHolidays::query()->get(['type','dates']);
                $work_day = explode(',', $rules["attendance"]["weeks"]);
                foreach ($att_false as $k=>$v){
                    //判断是否为考勤工作日
                    $week = Dh::getWeek($v);
                    if(in_array($week, $work_day)){
                        foreach ($holidays as $k1=>$v1){
                            if($v1['dates'] == $v && $v1['type'] == AttendanceApiService::WORKING_TO_REST){
                                $rest[$k] = $k;
                                unset($att_false[$k]);
                            }
                        }
                    }else{
                        $is_rest = true;
                        foreach ($holidays as $k1=>$v1){
                            if($v1['dates'] == $v && $v1['type'] == AttendanceApiService::REST_TO_WORKING){
                                $is_rest = false;
                            }
                        }
                        if($is_rest){
                            $rest[$k] = $k;
                            unset($att_false[$k]);
                        }
                    }
                }
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_SORT:
                //查询用户的排班信息
                foreach ($scheduling as $k=>$v){
                    if($v['classes_id'] == 0 && in_array($v['dates'], $att_false)){
                        $rest[$v['dates']] = $v['dates'];
                        unset($att_false[$v['dates']]);
                    }
                }
                break;
        }
        return [
            'att_false' => $att_false,
            'rest' => $rest,
        ];
    }

    /*
     * 计算平均工作时间
     * */
    public static function getAvgWorkTime($work_time, $month_count){
        if($month_count <= 0){
            return ["hour"=> 0, "min"=> 0];
        }
        $avg = ceil($work_time / $month_count);
        $hour = intval($avg / 3600);
        $min = intval(intval(($avg % 3600)) / 60);
        return ["hour"=> $hour, "min"=> $min];
    }

    /*
     * 根据类型获取异常总数
     * */
    public static function getAnomalyByTypeCount($type, $begin, $end, $user_id = ''){
        $map = [
            ['anomaly_type', '=', $type],
            ['dates', '>=', $begin],
            ['dates', '<=', $end],
        ];
        if($user_id){
            $map[] = ['user_id','=',$user_id];
        }
        return AttendanceApiAnomaly::query()->where($map)->groupBy('user_id')->get()->count();
    }

    /*
     * 根据类型获取异常
     * */
    public static function getAnomalyByType($type, $begin, $end, $user_id = ''){
        $map = [
            ['anomaly_type', '=', $type],
            ['dates', '>=', $begin],
            ['dates', '<=', $end],
        ];
        if($user_id){
            $map[] = ['user_id','=',$user_id];
        }
        return AttendanceApiAnomaly::query()->where($map)
            ->with(['user.primaryDepartUser.department'])->get();
    }

    /*
     * 计算时间范围内 审核通过的 外出总数
     * */
    public static function getOutSideCount($begin, $end){
        return VacationOutSideRecord::query()
            ->where(function($query) use ($begin, $end){
                $query->whereBetween('begin_time', [$begin." 00:00:00", $end." 23:59:59"])
                    ->orWhereBetween('end_time', [$begin." 00:00:00", $end." 23:59:59"]);
            })->get()->groupBy('uid')->count();
    }
    /*
     * 计算时间范围内 审核通过
     * */
    public static function getOutSide($begin, $end){
        return VacationOutSideRecord::query()
            ->where(function($query) use ($begin, $end){
                $query->whereBetween('begin_time', [$begin." 00:00:00", $end." 23:59:59"])
                    ->orWhereBetween('end_time', [$begin." 00:00:00", $end." 23:59:59"]);
            })->get();
    }

    /*
     * 计算时间范围内 请假总数
     * */
    public static function getLeaveCount($begin, $end, $type = ''){
        $res = VacationLeaveRecord::query();
        if($type) $res->where('vacation_type', $type);
        return $res->where(function($query) use ($begin, $end){
                $query->whereBetween('begin_time', [$begin." 00:00:00", $end." 23:59:59"])
                    ->orWhereBetween('end_time', [$begin." 00:00:00", $end." 23:59:59"]);
            })->get()->groupBy('uid')->count();
    }

    /*
     * 计算时间范围内 出差总数
     * */
    public static function getTripCount($begin, $end){
        $res = VacationBusinessTripRecord::query()
            ->with(['trip'=> function($query) use ($begin, $end){
                $query->whereBetween('fd_begin_time', [$begin." 00:00:00", $end." 23:59:59"])
                    ->orWhereBetween('fd_end_time', [$begin." 00:00:00", $end." 23:59:59"]);
            }])->get()->groupBy('uid')->count();

        return $res;
    }

    /*
     * 计算时间范围内 出差总数
     * */
    public static function getTrip($begin, $end){
        $res = VacationBusinessTripRecord::query()
            ->with(['trip'=> function($query) use ($begin, $end){
                $query->whereBetween('fd_begin_time', [$begin." 00:00:00", $end." 23:59:59"])
                    ->orWhereBetween('fd_end_time', [$begin." 00:00:00", $end." 23:59:59"]);
            }])->get();

        return $res;
    }

    /*
     * 根据类型获取请假
     * */
        public static function getLeaveByType($begin, $end, $type=''){
        $ex = explode('-',$type);
        $v_type = end($ex);
        $res = VacationLeaveRecord::query();
        if($type) $res->where('vacation_type', $v_type);
        $list = $res->where(function($query) use ($begin, $end){
                $query->whereBetween('begin_time', [$begin." 00:00:00", $end." 23:59:59"])
                    ->orWhereBetween('end_time', [$begin." 00:00:00", $end." 23:59:59"]);
            })->get()->groupBy('uid');
        //dd($list->toArray());
        return $list;
    }

    public static function getLeaveByTypeUid($begin, $end, $uid, $type=''){
        $list = VacationLeaveRecord::query()->where(function($query) use ($begin, $end){
            $query->whereBetween('begin_time', [$begin." 00:00:00", $end." 23:59:59"])
                ->orWhereBetween('end_time', [$begin." 00:00:00", $end." 23:59:59"]);
        })->where('uid', $uid);
        if($type) $list->where('vacation_type', $type);
        return $list->get();
    }

    /*
     * 根据月，统计用户的请加天数
     * */
    public static function getUserLeaveDaysCount($begin, $end, $uid, $type=''){
        $list = self::getLeaveByTypeUid($begin, $end, $uid, $type);
        $month_date = Dh::getbetweenDay($begin, $end);
        $all = [];
        foreach ($list as $k=>$v){
            $leave_date = Dh::getbetweenDay($v->begin_time, $v->end_time);
            $counts = array_intersect($leave_date, $month_date);
            foreach ($counts as $v1){
                $all[] = [
                    'dates'=> $v1,
                    'number'=>8,//小时
                    'weeks'=> self::$weeks[Dh::getWeek($v1)],
                ];
            }
        }
        return $all;
    }


    /*
     * 薪资计算时， 一个人 ， 一个月 统计考勤
     * $user_id
     * $dates 月份 Y-m
     * */
    public static function oneMonthCountForHr($user_id, $dates){
        $res = app()->make(CountsRespository::class)->oneMonthForHr(User::find($user_id), ['dates'=> $dates]);
        $res = json_decode($res->getContent(), true)['data'];
        $list['should_arrive'] = count($res['working_date']) + count($res['absenteeism']);
        $list['reality_arrive'] = count($res['working_date']);
        $list['leave_of_absence'] = count($res['leave_of_absence']);
        $list['leave_sick_leave'] = count($res['leave_sick_leave']);
        $list['overtime_nums'] = $res['overtime_nums'];
        return $list;
    }

    /*
     * 根据月份获取参与考勤的用户
     * $begin 月份第一天
     * */
    public static function getAttendanceUserByMonth($begin){
        return User::query()
            ->whereNotIn('id',function ($query){
                /** @var Builder $query */
                $query->select('user_id')
                    ->from('attendance_api_staff')
                    ->where('is_attendance', AttendanceApiService::ATTENDANCE_STAFF_FALSE)
                    ->rightJoin('attendance_api as aa','aa.id', '=','attendance_id')
                    ->whereNull('attendance_api_staff.deleted_at')
                    ->whereNull('aa.deleted_at');
            })->where( function($query) use ($begin) {
                $query->whereNull('leave_at')->orWhere('leave_at', '>=', $begin);
            })->get();
    }
}
<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Http\Requests\AttendanceApi\AttendanceApiCountRequest;
use App\Models\AttendanceApi\AttendanceApi;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClasses;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\AttendanceApi\AttendanceApiScheduling;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Models\Department;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Repositories\Repository;
use App\Services\AttendanceApi\AttendanceApiService;
use App\Services\AttendanceApi\CountsService;
use Carbon\Carbon;
use Validator;

class CountsRespository extends Repository {


    public function model() {
        return AttendanceApi::class;
    }

    /*
     * 个人 - 一天 - 考勤统计
     * */
    public function oneDayForHr($user, $data = ''){
        //参数验证
        if (empty($user)) return returnJson('用户不存在', ConstFile::API_RESPONSE_FAIL);

        $validator = Validator::make($data,['dates' => 'required|date_format:Y-m-d']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(),
                ConstFile::API_RESPONSE_FAIL);
        }
        //获取用户考勤规则
        $rules = AttendanceApiStaff::getUserAttendanceRule($user, $data);
        $list["classes"] = AttendanceApiService::getUserWorkBeginEndTime($rules, $user, $data);
        $clock_num = AttendanceApiClock::query()->where([
            'user_id'=> $user->id,
            'dates'=> $data['dates'],
            'status'=> ConstFile::API_STATUS_NORMAL
        ])->with("anomaly")->get();

        $work_time = AttendanceApiService::getWorkTimeByDay($rules, $clock_num);
        $hour = intval($work_time / 3600);
        $min = intval(intval(($work_time % 3600)) / 60);
        $list["clock"]['work_time'] = [
            'hour' => $hour,
            'min' => $min,
        ];
        $list["clock"]['nums'] = count($clock_num);
        $list["clock"]['info'] = $clock_num->toArray();
        return returnJson("",ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    /*
     * 个人 - 一个月 - 考勤统计
     * */
    public function oneMonthForHr($user, $data = ''){
        //参数验证
        if (empty($user)) return returnJson('用户不存在', ConstFile::API_RESPONSE_FAIL);

        $validator = Validator::make($data,['dates' => 'required|date_format:Y-m']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(),
                ConstFile::API_RESPONSE_FAIL);
        }

        $t = Dh::getBeginEndByMonth($data['dates']);

        //所在考勤组 规则
        $rules = AttendanceApiStaff::getUserAttendanceRule($user);
        if(Q($rules, 'is_attendance') && Q($rules, 'is_attendance') !== AttendanceApiService::ATTENDANCE_STAFF_TRUE)
            return returnJson('不参与考勤，没有记录', ConstFile::API_RESPONSE_FAIL);

        $month_start = $user->join_at > $t['month_start'] ? $user->join_at : $t['month_start'];
        $month_end = $t['month_end'] > Carbon::now()->toDateString()
            ? Carbon::now()->toDateString() : $t['month_end'] ;
        //排班表
        $scheduling = "";
        if(Q($rules, "attendance", "system_type") == AttendanceApiService::ATTENDANCE_SYTTEM_SORT) {
            $scheduling = AttendanceApiScheduling::getSchedulingList($user->id, $month_start, $month_end);
        }
        //根据打卡记录获取出勤天数
        //出勤天数
        $working_date = AttendanceApiClock::getClockList($user->id, $month_start, $month_end);

        //初始化数据
        $month_date = $classes_count = $att_true  = [];
        //出勤班次
        $list = [
            'working_date' => [], 'classes' => [], 'out' => [], 'missing' => [],
            'rest' => [], 'late' => [], 'leave' => [], 'overtime' => [],
            'late_nums' => 0, 'leave_nums' => 0, 'overtime_nums' => 0, 'absenteeism' => [],
        ];
        foreach ($working_date as $k=>$v)
        {
            $att_true[$k] = $k;
            //出勤
            $info = $v[0]->toArray();
            $info['weeks'] = CountsService::$weeks[Dh::getWeek($info['dates'])];
            $list['working_date'][] = $info;
            //出勤班次
            $classes_count[$v[0]['classes']['id']][] = [
                'title' => $v[0]['classes']['title'],
                'code'=> $v[0]['classes']['code'],
            ];
            foreach ($v as $k1=>$v1){
                //外勤
                if($v1['clock_address_type'] == AttendanceApiService::CLOCK_ADDRESS_OUT){
                    $list['out'][] = [
                        'dates' => $v1['dates'],
                        'weeks'=> CountsService::$weeks[Dh::getWeek($v1['dates'])],
                    ];
                    break;
                }
            }
        }

        //本月日期
        $month_date = Dh::getbetweenDay($month_start, $month_end);
        //事假
        $list['leave_of_absence'] = CountsService::getUserLeaveDaysCount($month_start, $month_end, $user->id, CountsService::LEAVE_OF_ABSENCE);
        //病假
        $list['leave_sick_leave'] = CountsService::getUserLeaveDaysCount($month_start, $month_end, $user->id, CountsService::LEAVE_SICK_LEAVE);
        //调休
        $list['adjust_rest'] = CountsService::getUserLeaveDaysCount($month_start, $month_end, $user->id, CountsService::LEAVE_ADJUST_REST);
        //年假
        $list['annual_leave'] = CountsService::getUserLeaveDaysCount($month_start, $month_end, $user->id, CountsService::LEAVE_ANNUAL_LEAVE);
        //所有请假
        $list['leave_all'] = CountsService::getUserLeaveDaysCount($month_start, $month_end, $user->id);

        //和出勤对比key。 获取未出勤天数
        $att_false = array_diff_key($month_date,$att_true);
        // 排除未出勤天数中  请假 的情况
        foreach ($att_false as $k=>$v){
            $res = AttendanceApiClock::getWorkflowInfo(Entry::VACATION_TYPE_LEAVE,['dates'=>$v], $user);
            if($res !== false){
                unset($att_false[$k]);
            }
        }

        //排除放假
        $res = CountsService::getListRemoveHolidays($att_false, $rules, $scheduling);
        $att_false = $res['att_false'];
        //休息天数
        foreach ($res['rest'] as $k=>$v){
            $list['rest'][] = [
                'dates'=> $v,
                'weeks'=> CountsService::$weeks[Dh::getWeek($v)],
            ];
        }
        $list['classes_num'] = 0;
        $list["avg"] = ['hour'=> 0, 'min'=> 0];
        //迟到 早退 加班 缺卡 旷工 排除自由制
        if($rules["attendance"]["system_type"] !== AttendanceApiService::ATTENDANCE_SYTTEM_FREE){
            $anomaly = AttendanceApiAnomaly::getAnomalyList($user->id, $month_start, $month_end);
            $list = CountsService::getAnomalyInfo($anomaly, $rules, $user, $list);
            //工作时长
            $work_time = $rules["attendance"]["classes"]
                ? AttendanceApiService::getWorkTime($rules["attendance"]["classes"]["type"], $working_date)
                : 0;
            //平均工作时间
            $count_month = Carbon::parse($data['dates'])->endOfMonth()->toDateString()
                            <= Carbon::now()->toDateString()
                ? Carbon::parse($data['dates'])->endOfMonth()->day
                : Carbon::now()->day;
            $month_count = $count_month - count($list["rest"]);
            $list["avg"] = CountsService::getAvgWorkTime($work_time, $month_count);

            $list['classes_num'] = 0;
            foreach ($classes_count as $k=>$v){
                $list['classes'][] = [
                    'class'=> $v[0]['title'],
                    'num'=> count($v),
                ];
                $list['classes_num'] += count($v);
            }
        }
        return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    /**
     *   按天统计所有人 实时查询
     */
    public function countByDay($data){
        //应到人数
        $arrive = [];
        //实到人数
        $reality_arrive = 0;
        //在职员工，并且不用参与考勤员工
        $users = User::whereNotIn('id',function ($query){
            $model = new AttendanceApiStaff();
            $query->select('user_id')->from($model->getTable())->where('is_attendance', AttendanceApiService::ATTENDANCE_STAFF_FALSE);
        })->where('status',ConstFile::API_STATUS_NORMAL)
            ->with(["departUserPrimary.department",'clock','anomaly'])
            ->get(["id","avatar","chinese_name"]);

        $result = [
            'normal' => [],
            'absenteeism' => [],
            'late' => [],
            'missing' => [],
            'clock' => [],
            'siteout' => [],
        ];
        //dd($users->toArray());
        foreach ($users as $user_k => $user_v){
            //获取考勤规则
            $rules = AttendanceApiStaff::getUserAttendanceRule($user_v, $data);
            $is_work_day = AttendanceApiService::isWorkingDay($data['dates'], $rules);
            if($is_work_day === true){
                $user_v = $user_v->toArray();
                $user_info = [
                    'user_id' => $user_v["id"],
                    'avatar' => $user_v["avatar"],
                    'chinese_name' => $user_v["chinese_name"],
                    'department_name' => $user_v["depart_user_primary"]["department"]["name"],
                    'dates' => $data['dates'],
                ];
                //根据时间获取打卡记录
                $clock = AttendanceApiClock::query()->where([
                    'user_id'=> $user_v["id"],
                    'dates'=> $data["dates"],
                    'status'=> ConstFile::API_STATUS_NORMAL,
                ])->get();
                $late = AttendanceApiAnomaly::query()->where([
                    'user_id'=> $user_v["id"],
                    'dates'=> $data["dates"],
                    'anomaly_type'=> AttendanceApiService::CLOCK_ANOMALY_LATE,
                ])->first();
                if(!empty($late)){
                    $result['late'][] = $user_info;
                }
                if(empty($rules["attendance"]["classes"])){
                    //自由制
                    if($clock->isEmpty()){
                        $user_info['clock_nums'] = 0;
                        $result['missing'][] = $user_info;
                    }
                }else{
                    //固定制 和 排班制
                    if($rules["attendance"]["system_type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_ONE){
                        $class_type = $rules["attendance"]["classes"]["type"];
                    }elseif($rules["attendance"]["system_type"] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT_CYCLE_TWO){
                        $classes_info = AttendanceApiScheduling::query()
                            ->where('user_id', $user_v["id"])
                            ->where('dates', $data["dates"])
                            ->first();
                        $class_type = 0;
                        if(!empty($classes_info)){
                            $class_type = Q($classes_info, 'classes', 'type');
                        }
                    }

                    for ($i = 1; $i <= $class_type; $i++) {
                        $counts = 0;
                        foreach ($clock as $clock_k=>$clock_v){
                            if($clock_v['clock_nums'] == $i)
                                $counts++;
                        }

                        if($counts == 0){
                            $user_info['clock_nums'] = $i;
                            $result['absenteeism'][] =  $user_info;
                        }elseif ($counts < 2){
                            $user_info['clock_nums'] = $i;
                            $result['missing'][] =  $user_info;
                        }else{
                            $info = AttendanceApiAnomaly::query()
                                ->where('user_id', $user_v["id"])
                                ->where('dates', $data["dates"])
                                ->where('clock_nums', $i)
                                ->whereIn('anomaly_type', [
                                    AttendanceApiService::CLOCK_ANOMALY_LATE,
                                    AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY,
                                ])->first();
                            if(empty($info)){
                                $user_info['clock_nums'] = $i;
                                $result['normal'][] =  $user_info;
                            }

                        }
                    }
                }
            }else{
                unset($users[$user_k]);
            }
        }
        //打卡人数
        $result["clock"] = count($result['missing']) + count($result['normal']);
        //外勤
        $result['siteout'] = AttendanceApiClock::query()->where([
            'dates'=> $data['dates'],
            'clock_address_type' => AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY,
        ])->groupBy('user_id')->get()->count();
        return returnJson("", ConstFile::API_RESPONSE_SUCCESS, $result);
    }

    /**
     *   按天统计所有人 按异常统计
     */
    public function alldayForHr($data){
        $weeks = Dh::getWeek($data['dates']);
        //固定制，自由制
        $attendance_list = AttendanceApi::query()->where([
            ['weeks','like',"%{$weeks}%"],
            ['system_type','!=', AttendanceApiService::ATTENDANCE_SYTTEM_SORT]
        ])->with(['department.userInfoPrimary','staff'])->get();

        $att_false = $user_ids = [];
        foreach ($attendance_list as $k=>$v){
            if($v["department"]){
                foreach ($v["department"] as $v1){
                    if($v1['userInfo']){
                        foreach ($v1['userInfo'] as $v2){
                            if(Q($v2,'pivot','is_primary') == Department::PRIMARY &&
                                Q($v2,'join_at') <= $data['dates']){
                                $v2['department_name'] = $v1['name'];
                                $user_ids[] = $v2['id'];
                            }
                        }
                    }
                }
            }
            if($v['staff']){
                foreach ($v['staff'] as $v1){
                    $user_ids[] = $v1['user_id'];
                }
            }
        }
        //dd(array_unique($user_ids));
        //排班制
        $scheduling = AttendanceApiScheduling::getSchedulingUserId($data['dates'], $data['dates'])->toArray();

        //应到人数
        $list['arrive'] = array_unique(array_merge($user_ids, $scheduling));
        sort($list['arrive']);
        //实到人数
        $list['reality_arrive'] = AttendanceApiClock::query()
            ->where('dates', $data['dates'])
            ->where('status', ConstFile::API_STATUS_NORMAL)
            ->groupBy('user_id')->pluck("user_id")->toArray();

        //未打卡
        $reality_arrive = AttendanceApiClock::query()
            ->whereIn('user_id', $list['arrive'])
            ->where('dates', $data['dates'])
            ->where('status', ConstFile::API_STATUS_NORMAL)
            ->groupBy('user_id')->pluck("user_id")->toArray();
        $list['absenteeism'] = array_diff($list['arrive'], $reality_arrive);
        sort($list['absenteeism']);


        //迟到
        $list['late'] = AttendanceApiAnomaly::query()->where([
            'dates'=> $data['dates'],
            'anomaly_type'=> AttendanceApiService::CLOCK_ANOMALY_LATE,
        ])->groupBy('user_id')->get()->pluck('user_id')->toArray();
        sort($list['late']);
        //$list['late'] = count($late);

        //外出
        $list['leaveout'] = AttendanceApiClock::query()->where([
            'dates'=> $data['dates'],
            'status'=> ConstFile::API_STATUS_NORMAL,
            'clock_address_type'=> AttendanceApiService::CLOCK_ADDRESS_OUT,
        ])->groupBy('user_id')->get()->pluck('user_id')->toArray();
        sort($list['leaveout']);
        //$list['leaveout'] = count($leaveout);

        //请假
        $leave = CountsService::getLeaveByType($data['dates'], $data['dates']);
        $list['leave'] = [];
        foreach ($leave as $k=>$v){
            $list['leave'][] = $k;
        }

        //缺卡
        $list['missing'] = AttendanceApiAnomaly::query()->where([
            'dates'=> $data['dates'],
            'anomaly_type'=> AttendanceApiService::CLOCK_ANOMALY_MISSING,
        ])->groupBy('user_id')->get()->pluck('user_id')->toArray();
        sort($list['missing']);
        return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
    }


    public function alldayClockInfoForHr($data){
        $result = User::query()
            ->whereIn('id', $data['user_id'])
            ->with([
                'fetchPrimaryDepartment',
                'clock'=> function($query) use ($data){
                    $query->where('dates',$data['dates'])->where('status',ConstFile::API_STATUS_NORMAL);
                },
                'anomaly'=> function($query) use ($data){
                    $query->where('dates',$data['dates']);
                },
                ])
            ->get(['id','chinese_name','avatar']);
        return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $result);

    }

    /*
     * 全部 - 一个月 - 考勤统计
     * */
    public function allMonthForHr($data){
        //参数验证
        $validator = Validator::make($data,['dates' => 'required|date_format:Y-m']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(),
                ConstFile::API_RESPONSE_FAIL);
        }

        $t = Dh::getBeginEndByMonth($data['dates']);

        $users_count = CountsService::getAttendanceUserByMonth($t['month_start'])->count();

        //迟到
        $list[] = [
            'name'=> '迟到',
            'nums'=> CountsService::getAnomalyByTypeCount(AttendanceApiService::CLOCK_ANOMALY_LATE, $t['month_start'], $t['month_end']),
            'type'=> AttendanceApiService::CLOCK_ANOMALY_LATE,
        ];
        //早退
        $list[] = [
            'name'=> '早退',
            'nums'=> CountsService::getAnomalyByTypeCount(AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY, $t['month_start'], $t['month_end']),
            'type'=> AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY,
        ];
        //加班
        $list[] = [
            'name'=> '加班',
            'nums'=> CountsService::getAnomalyByTypeCount(AttendanceApiService::CLOCK_ANOMALY_ADDWORK, $t['month_start'], $t['month_end']),
            'type'=> AttendanceApiService::CLOCK_ANOMALY_ADDWORK,
        ];
        //缺卡
        $list[] = [
            'name'=> '缺卡',
            'nums'=> CountsService::getAnomalyByTypeCount(AttendanceApiService::CLOCK_ANOMALY_MISSING, $t['month_start'], $t['month_end']),
            'type'=> AttendanceApiService::CLOCK_ANOMALY_MISSING,
        ];
        //旷工
        $list[] = [
            'name'=> '旷工',
            'nums'=> CountsService::getAnomalyByTypeCount(AttendanceApiService::CLOCK_ANOMALY_ABSENTEEISM, $t['month_start'], $t['month_end']),
            'type'=> AttendanceApiService::CLOCK_ANOMALY_ABSENTEEISM,
        ];

        // 外勤
        $list[] = [
            'name'=> '外勤',
            'nums'=> CountsService::getOutSideCount($t['month_start'], $t['month_end']),
            'type'=> Entry::WORK_FLOW_NO_OUTSIDE_PUNCH,
        ];
        // 出差
        $list[] = [
            'name'=> '出差',
            'nums'=> CountsService::getTripCount($t['month_start'], $t['month_end']),
            'type'=> Entry::VACATION_TYPE_BUSINESS_TRIP,
        ];
        // 事假
        $list[] = [
            'name'=> '事假',
            'nums'=> CountsService::getLeaveCount($t['month_start'], $t['month_end'], CountsService::LEAVE_OF_ABSENCE),
            'type'=> Entry::VACATION_TYPE_LEAVE.'-'.CountsService::LEAVE_OF_ABSENCE,
        ];
        // 调休
        $list[] = [
            'name'=> '调休',
            'nums'=> CountsService::getLeaveCount($t['month_start'], $t['month_end'], CountsService::LEAVE_ADJUST_REST),
            'type'=> Entry::VACATION_TYPE_LEAVE.'-'.CountsService::LEAVE_ADJUST_REST,
        ];
        //年假
        $list[] = [
            'name'=> '年假',
            'nums'=> CountsService::getLeaveCount($t['month_start'], $t['month_end'], CountsService::LEAVE_ANNUAL_LEAVE),
            'type'=> Entry::VACATION_TYPE_LEAVE.'-'.CountsService::LEAVE_ANNUAL_LEAVE,
        ];
        //病假
        $list[] = [
            'name'=> '病假',
            'nums'=> CountsService::getLeaveCount($t['month_start'], $t['month_end'], CountsService::LEAVE_SICK_LEAVE),
            'type'=> Entry::VACATION_TYPE_LEAVE.'-'.CountsService::LEAVE_SICK_LEAVE,
        ];

        $all_data = [
            'users'=> $users_count,
            'list'=> $list,
        ];

        return returnJson("",ConstFile::API_RESPONSE_SUCCESS, $all_data);
    }


    public static function getUserBasicInfo($v){
        return [
            'avatar' => Q($v,'user','avatar'),
            'chinese_name' => Q($v,'user','chinese_name'),
            'departments' => Q($v,'user','primaryDepartUser','department','name'),
        ];
    }

    public function getAnomalyByType($data){
        //参数验证
        $validator = Validator::make($data,[
            'dates' => 'required|date_format:Y-m',
            'type' => 'required',
        ]);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(),
                ConstFile::API_RESPONSE_FAIL);
        }

        $t = Dh::getBeginEndByMonth($data['dates']);
        switch ($data['type']){
            case AttendanceApiService::CLOCK_ANOMALY_LATE:
            case AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY:
            case AttendanceApiService::CLOCK_ANOMALY_ADDWORK:
                $anomaly_list = CountsService::getAnomalyByType($data['type'],$t['month_start'],$t['month_end']);
                if($anomaly_list->isEmpty()) return returnJson('',ConstFile::API_RESPONSE_SUCCESS,[]);
                foreach ($anomaly_list as $k=>$v){
                    $info[$v['user_id']][] = $v;
                }
                foreach ($info as $k=>$v){
                    $user_info = self::getUserBasicInfo($v[0]);
                    $times = 0;
                    foreach ($v as $v1){
                        $times += $v1['anomaly_time'];
                    }
                    $list[] = [
                        "avatar"=> $user_info["avatar"],
                        "chinese_name"=> $user_info["chinese_name"],
                        "departments"=> $user_info["departments"],
                        "nums"=> count($v)."次",
                        "times"=> $times."分钟",
                        "user_id"=> $k,
                    ];
                }
                break;
            case AttendanceApiService::CLOCK_ANOMALY_MISSING:
            case AttendanceApiService::CLOCK_ANOMALY_ABSENTEEISM:
                $anomaly_list = CountsService::getAnomalyByType($data['type'],$t['month_start'],$t['month_end']);
                if($anomaly_list->isEmpty()) return returnJson('',ConstFile::API_RESPONSE_SUCCESS,[]);
                foreach ($anomaly_list as $k=>$v){
                    $info[$v['user_id']][] = $v;
                }
                foreach ($info as $k=>$v){
                    $user_info = self::getUserBasicInfo($v[0]);
                    $list[] = [
                        "avatar"=> $user_info["avatar"],
                        "chinese_name"=> $user_info["chinese_name"],
                        "departments"=> $user_info["departments"],
                        "nums"=> count($v)."次",
                        "user_id"=> $k,
                    ];
                }
                break;
            case Entry::WORK_FLOW_NO_OUTSIDE_PUNCH:
                $anomaly_list = CountsService::getOutSide($t['month_start'],$t['month_end']);
                if($anomaly_list->isEmpty()) return returnJson('',ConstFile::API_RESPONSE_SUCCESS,[]);
                foreach ($anomaly_list as $k=>$v){
                    $info[$v['uid']][] = $v;
                }
                foreach ($info as $k=>$v){
                    $user_info = User::find($k);
                    $month_date = Dh::getbetweenDay($t['month_start'], $t['month_end']);
                    $nums = 0;
                    foreach ($v as $k1=>$v1){
                        //本月日期
                        $leave_date = Dh::getbetweenDay($v1->begin_time, $v1->end_time);
                        $nums += count(array_intersect($leave_date, $month_date));
                    }
                    $list[] = [
                        "avatar"=> $user_info["avatar"],
                        "chinese_name"=> $user_info["chinese_name"],
                        "departments"=> $user_info["departments"][0]['name'],
                        "nums"=> $nums."天",
                        "user_id"=> $k,
                    ];
                }
                break;
            case Entry::VACATION_TYPE_BUSINESS_TRIP:
                $anomaly_list = CountsService::getTrip($t['month_start'],$t['month_end']);
                if($anomaly_list->isEmpty()) return returnJson('',ConstFile::API_RESPONSE_SUCCESS,[]);
                foreach ($anomaly_list as $k=>$v){
                    $info[$v['uid']][] = $v->toArray();
                }
                foreach ($info as $k=>$v){
                    $user_info = User::find($k);
                    $month_date = Dh::getbetweenDay($t['month_start'], $t['month_end']);
                    $nums = 0;
                    foreach ($v as $v1){
                        foreach ($v1["trip"] as $k2=>$v2){
                            //本月日期
                            $leave_date = Dh::getbetweenDay($v2['fd_begin_time'], $v2['fd_end_time']);
                            $nums += count(array_intersect($leave_date, $month_date));
                        }
                    }

                    $list[] = [
                        "avatar"=> $user_info["avatar"],
                        "chinese_name"=> $user_info["chinese_name"],
                        "departments"=> $user_info["departments"][0]['name'],
                        "nums"=> $nums."天",
                        "user_id"=> $k,
                    ];
                }
                break;
            case Entry::VACATION_TYPE_LEAVE.'-'.CountsService::LEAVE_OF_ABSENCE:
            case Entry::VACATION_TYPE_LEAVE.'-'.CountsService::LEAVE_ADJUST_REST:
            case Entry::VACATION_TYPE_LEAVE.'-'.CountsService::LEAVE_SICK_LEAVE:
            case Entry::VACATION_TYPE_LEAVE.'-'.CountsService::LEAVE_ANNUAL_LEAVE:
                $anomaly_list = CountsService::getLeaveByType($t['month_start'],$t['month_end'],$data['type']);
                if($anomaly_list->isEmpty()) return returnJson('',ConstFile::API_RESPONSE_SUCCESS,[]);

                foreach ($anomaly_list as $k=>$v){
                    $user_info = User::find($k);
                    $month_date = Dh::getbetweenDay($t['month_start'], $t['month_end']);
                    $nums = 0;
                    foreach ($v as $k1=>$v1){
                        //本月日期
                        $leave_date = Dh::getbetweenDay($v1->begin_time, $v1->end_time);
                        $nums += count(array_intersect($leave_date, $month_date));
                    }
                    $list[] = [
                        "avatar"=> $user_info["avatar"],
                        "chinese_name"=> $user_info["chinese_name"],
                        "departments"=> $user_info["departments"][0]['name'],
                        "nums"=> $nums."天",
                        "user_id"=> $k,
                    ];
                }
                break;

        }
        return returnJson("",ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    /*
     * 个人 - 一个月 - 考勤统计
     * */
    public function getOneMonthForHr($user, $data = ''){
        //参数验证
        if (empty($user)) return returnJson('用户不存在', ConstFile::API_RESPONSE_FAIL);

        $validator = Validator::make($data,['dates' => 'required|date_format:Y-m']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(),
                ConstFile::API_RESPONSE_FAIL);
        }

        $t = Dh::getBeginEndByMonth($data['dates']);

        //所在考勤组 规则
        $rules = AttendanceApiStaff::getUserAttendanceRule($user);

        if(Q($rules, 'is_attendance') && Q($rules, 'is_attendance') !== AttendanceApiService::ATTENDANCE_STAFF_TRUE)
            return returnJson('不参与考勤，没有记录', ConstFile::API_RESPONSE_FAIL);

        $month_start = $user->join_at > $t['month_start'] ? $user->join_at : $t['month_start'];
        $month_end = $t['month_end'] > Carbon::now()->toDateString()
            ? Carbon::now()->toDateString() : $t['month_end'] ;
        //出勤班次
        $list = [ 'late' => [], 'leave' => [], 'overtime' => [],
            'late_nums' => 0, 'leave_nums' => 0, 'overtime_nums' => 0, 'absenteeism' => [],
        ];
        $list['rules']=$rules;
        $list['t']=$t;
        //事假
        $list['leave_of_absence'] = CountsService::getUserLeaveDaysCount($month_start, $month_end, $user->id, CountsService::LEAVE_OF_ABSENCE);
        //病假
        $list['leave_sick_leave'] = CountsService::getUserLeaveDaysCount($month_start, $month_end, $user->id, CountsService::LEAVE_SICK_LEAVE);

       //var_dump($list);
        //迟到 早退 加班 缺卡 旷工 排除自由制
        if($rules["attendance"]["system_type"] !== AttendanceApiService::ATTENDANCE_SYTTEM_FREE){
            $anomaly = AttendanceApiAnomaly::getAnomalyList($user->id, $month_start, $month_end);
            $list = CountsService::getAnomalyInfo($anomaly, $rules, $user, $list);
        }
        return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
    }
}

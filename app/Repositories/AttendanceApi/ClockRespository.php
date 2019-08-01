<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Requests\AttendanceApi\UpdateUserClockForHrRequest;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClasses;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\AttendanceApi\AttendanceApiScheduling;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Models\AttendanceApi\AttendanceApiUpdateUserClockForHr;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Repositories\Repository;
use App\Services\AttendanceApi\AttendanceApiService;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Validator;
use Exception;
use Auth;
use DB;

class ClockRespository extends Repository {

    public $weeks = [
        '1'=> '星期一',
        '2'=> '星期二',
        '3'=> '星期三',
        '4'=> '星期四',
        '5'=> '星期五',
        '6'=> '星期六',
        '7'=> '星期日',
    ];

    public function model() {
        return AttendanceApiClock::class;
    }

    /*
     * 根据日期获取打卡接口
     * */
    /**
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function rules($user){
        $rules = AttendanceApiStaff::getUserAttendanceRule($user);
        $list['rules'] = [
            'is_attendance'=> isset($rules['is_attendance'])
                ? $rules['is_attendance']
                : AttendanceApiService::ATTENDANCE_STAFF_TRUE,
            'system_type'=> Q($rules, 'attendance', 'system_type'),
            'classes_id'=> Q($rules, 'attendance', 'classes_id'),
            'is_getout_clock'=> Q($rules, 'attendance', 'is_getout_clock'),
            'title'=> Q($rules, 'attendance', 'title'),
            'lng'=> Q($rules, 'attendance', 'lng'),
            'lat'=> Q($rules, 'attendance', 'lat'),
            'clock_range'=> Q($rules, 'attendance', 'clock_range'),
            'address'=> Q($rules, 'attendance', 'address'),
        ];
        $list['user'] = [
            'join_at'=> $user["join_at"],
            'avatar'=> $user->fetchAvatar(),
            'chinese_name'=> $user["chinese_name"],
        ];
        //展示考勤组 规则
        switch (Q($rules, 'attendance', 'system_type')){
            case AttendanceApiService::ATTENDANCE_SYTTEM_FIXED:
                //工作日
                $weeks = explode(',',Q($rules, 'attendance', 'weeks'));
                foreach ($weeks as $k=>$v){
                    $weeks_woke_name[$v] = str_replace('星期','', $this->weeks[$v]);
                }
                //休息日
                $weeks_rest_name = array_diff_key($this->weeks, $weeks_woke_name);
                foreach ($weeks_rest_name as $k=>$v){
                    $weeks_rest_name[$k] = str_replace('星期','', $v);
                }
                //班次
                $classes = Q($rules, 'attendance', 'classes');
                $classes_times = $clock_times = [];
                for ($i=1; $i<=$classes['type'];$i++){
                    //上下班时间
                    $classes_times[] = Carbon::parse($classes["work_time_begin{$i}"])->format('H:i')
                        ."-".Carbon::parse($classes["work_time_end{$i}"])->format('H:i');
                    //打卡时间段 最早最晚打卡时间
                    $begin = Carbon::parse($classes["work_time_begin{$i}"])
                        ->subMinute($classes["clock_time_begin{$i}"] ?: AttendanceApiService::CLASSES_ONE_BEGIN_CLOCK_TIME1);
                    $end = Carbon::parse($classes["work_time_end{$i}"])
                        ->addMinute($classes["clock_time_end{$i}"] ?: AttendanceApiService::CLASSES_ONE_END_CLOCK_TIME1);

                    $tomorrow = "";
                    if($end->toDateString() > $begin->toDateString()){
                        $tomorrow = "次日";
                    }
                    $clock_times[] = "【上班】". $begin->format('H:i') ."后可打卡; 【下班】". $tomorrow ." ". $end->format('H:i') ."前可打卡;";
                }

                $list['times'] = [
                    'work_weeks' => "周".implode('、', $weeks_woke_name)." ".implode(' ', $classes_times),
                    'rest_weeks' => "周".implode('、', $weeks_rest_name)."休息 ",
                    'classes_name' => $classes['title'],
                    'clock_time' => $clock_times,
                ];
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_SORT:
                $classes_ids = explode(',', Q($rules, 'attendance', 'classes_id'));
                $classes = AttendanceApiClasses::query()
                    ->whereIn('id', $classes_ids)
                    ->get();
                foreach ($classes as $k=>$v){
                    $clock_times = [];
                    for ($i=1; $i<=$v['type'];$i++){
                        $nums = $v['type'] > 1 ? "第{$i}次 " : "";
                        //上下班时间
                        $begin = Carbon::parse($v["work_time_begin{$i}"]);
                        $end = Carbon::parse($v["work_time_end{$i}"]);

                        $tomorrow = "";
                        if($end->toDateString() > $begin->toDateString()){
                            $tomorrow = "次日";
                        }

                        $clock_times[] = $v['title'] .": ". $nums . $begin->format('H:i')
                            ."-". $tomorrow . $end->format('H:i');
                    }
                    $list['times']['clock_time'][] = implode(" ", $clock_times);
                }
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_FREE:
                //工作日
                $weeks = explode(',',Q($rules, 'attendance', 'weeks'));
                foreach ($weeks as $k=>$v){
                    $weeks_woke_name[$v] = str_replace('星期','', $this->weeks[$v]);
                }
                $clock_times = [
                    Carbon::parse(Q($rules, 'attendance', 'clock_node'))->format('H:i') . "之前打卡记为昨天的考勤",
                    Carbon::parse(Q($rules, 'attendance', 'clock_node'))->format('H:i') . "之后打卡记为今天的考勤",
                ];
                $list['times'] = [
                    'work_weeks' => "周".implode('、', $weeks_woke_name),
                    'clock_time' => $clock_times,
                ];
                break;
        }

        $list['add_clock'] = "每个月可补卡". (Q($rules, 'attendance', 'add_clock_num') ?: 0) ."次";
        $list['range'] = [
            'address' => Q($rules, 'attendance', 'address'),
            'lng' => Q($rules, 'attendance', 'lng'),
            'lat' => Q($rules, 'attendance', 'lat'),
            'clock_range' => Q($rules, 'attendance', 'clock_range'),
        ];
        return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    /*
     * 考勤 - 打卡 - 根据日期获取打卡信息
     * */
    public function clockInfo($user, $data){
        //参数验证
        $validator = Validator::make($data,['dates' => 'required|date_format:Y-m-d']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(),
                ConstFile::API_RESPONSE_FAIL);
        }

        //考勤相关的申请记录
        $flow = Flow::query()
            ->orWhere('flow_no', Entry::VACATION_TYPE_LEAVE)
            ->orWhere('flow_no', Entry::WORK_FLOW_NO_OUTSIDE_PUNCH)
            ->orWhere('flow_no', Entry::VACATION_TYPE_BUSINESS_TRIP)
            ->with(['entries'=> function($query) use ($user){
                /** @var Builder $query */
                $query
                    ->where('user_id', $user->id)
                    ->where(function ($q){
                        $q->orWhere('status', Entry::STATUS_IN_HAND);
                        $q->orWhere('status', Entry::STATUS_REJECTED);
                        $q->orWhere('status', Entry::STATUS_FINISHED);
                    })
                    ->with(['entry_data'])->whereHas('entry_data');
            }])->get();

        $list['flow'] = [];
        $now_dates = Carbon::parse($data['dates']);

        foreach ($flow as $k=>$v){
            foreach ($v->entries as $k1=>$v1){
                foreach ($v1->entry_data as $k2=>$v2){
                    if($v2->field_name == 'begin_time'){
                        $begin_time = Carbon::parse($v2->field_value);
                    }
                    if($v2->field_name == 'end_time'){
                        $end_time = Carbon::parse($v2->field_value);
                    }
                    if($v2->field_name == 'time_sub_by_hour'){
                        $time_sub_by_hour = $v2->field_value;
                    }
                    if($v2->field_name == 'vacation_type'){
                        $vacation_type = $v2->field_value;
                    }
                }
                if(isset($begin_time) && isset($end_time) && $now_dates->between($begin_time, $end_time)){
                    $list['flow'][] = [
                        'eid' => $v1->id,
                        'flow_name' => $v->flow_name,
                        'title' => $v1->title,
                        'begin_time' => $begin_time->toDateTimeString(),
                        'end_time' => $end_time->toDateTimeString(),
                        'time_sub_by_hour' => isset($time_sub_by_hour) ? $time_sub_by_hour : "",
                        'vacation_type'=> isset($vacation_type) ? $vacation_type : "",
                    ];
                }
            }
        }

        //获取用户考勤规则
        $rules = AttendanceApiStaff::getUserAttendanceRule($user, $data);
        if(Q($rules, 'attendance', 'system_type') == AttendanceApiService::ATTENDANCE_SYTTEM_FIXED){
            //固定制， 直接获取班次
            //$classes = AttendanceApiClasses::query()->find($rules['attendance']['classes']['id']);
            //休息日 加假日 班次为空
            if(AttendanceApiService::isWorkingDay($data['dates'], $rules) === true){
                $classes = Q($rules, 'attendance', 'classes');
            }else{
                $classes = null;
            }
        }elseif (Q($rules, 'attendance', 'system_type') == AttendanceApiService::ATTENDANCE_SYTTEM_SORT){
            //排班制， 通过当天排班信息 获取班次
            $scheduling_info = AttendanceApiScheduling::getSchedulingInfo($user->id, $data['dates']);
            if(empty($scheduling_info)){
                $classes = null;
            }else{
                $classes = $scheduling_info->classes;
            }
        }else{
            //自由制， 没有班次
            $classes = null;
        }
        $list['classes_info'] = $classes;

        //获取当天打卡记录
        $clock = AttendanceApiClock::query()->where([
            'user_id' => $user->id,
            'dates' => $data['dates'],
            //'status'=> ConstFile::API_STATUS_NORMAL
        ])->with('anomaly')
            ->get();

        $work_time = AttendanceApiService::getWorkTimeByDay($rules, $clock);
        $hour = intval($work_time / 3600);
        $min = intval(intval(($work_time % 3600)) / 60);
        $list["work_time"] = [
            'hour' => $hour,
            'min' => $min,
        ];
        $list["clock_nums"] = count($clock);

        $type = isset($classes['type']) ? $classes['type'] : 1;
        for ($i = 1; $i <= $type; $i++) {
            foreach ($clock as $k=>$v){
                if($v->type == AttendanceApiService::BEGIN_WORK && $v->clock_nums == $i){
                    $begin_datetimes = Carbon::parse($v->datetimes)->format('H:i');
                    $begin_anomaly = empty($v->anomaly) ? null : $v->anomaly->toArray();
                    $begin_status = $v->status;
                    $begin_address_type = $v->clock_address_type;
                    $begin_address = $v->clock_address;
                }elseif($v->type == AttendanceApiService::END_WORK && $v->clock_nums == $i){
                    $end_datetimes = Carbon::parse($v->datetimes)->format('H:i');
                    $end_anomaly = empty($v->anomaly) ? null : $v->anomaly->toArray();
                    $end_status = $v->status;
                    $end_address_type = $v->clock_address_type;
                    $end_address = $v->clock_address;
                }
            }

            $list['clock'][] = [
                [
                    'start' => isset($classes["work_time_begin{$i}"]) ? $classes["work_time_begin{$i}"] : null,
                    'datetimes' => isset($begin_datetimes) ? $begin_datetimes : null,
                    'anomaly'=>isset($begin_anomaly) ? $begin_anomaly : null,
                    'clock_time_begin'=>isset($classes["clock_time_begin{$i}"]) ? $classes["clock_time_begin{$i}"] : null,
                    'status' => isset($begin_status) ? $begin_status : null,
                    'clock_address_type' => isset($begin_address_type) ? $begin_address_type : null,
                    'clock_address' => isset($begin_address) ? $begin_address : null,
                ],
                [
                    'end' => isset($classes["work_time_end{$i}"]) ? $classes["work_time_end{$i}"] : null,
                    'datetimes' => isset($end_datetimes) ? $end_datetimes : null,
                    'anomaly'=> isset($end_anomaly) ? $end_anomaly : null,
                    'clock_time_end'=>isset($classes["clock_time_end{$i}"]) ? $classes["clock_time_end{$i}"] : null,
                    'status' => isset($end_status) ? $end_status : null,
                    'clock_address_type' => isset($end_address_type) ? $end_address_type : null,
                    'clock_address' => isset($end_address) ? $end_address : null,
                ],
            ];
        }
        foreach ($list['clock'] as $k=>$v){
            foreach ($v as $k1=>$v1){
                if(!$v1['datetimes'] && isset($v1['end'])){
                    $list['clock'][$k][$k1]['anomaly'] = AttendanceApiAnomaly::query()->where([
                        'user_id' => $user->id,
                        'dates' => $data['dates'],
                        'clock_nums' => $k + 1,
                    ])->where('anomaly_type','!=',AttendanceApiService::CLOCK_ANOMALY_ABSENTEEISM)
                        ->select(['clock_id','dates','anomaly_type','anomaly_time','is_serious_late'])
                        ->orderBy('anomaly_type', 'desc')
                        ->first();
                }
            }
        }
        $list['absenteeism'] = AttendanceApiAnomaly::query()->where([
            'user_id' => $user->id,
            'dates' => $data['dates'],
            'anomaly_type' => AttendanceApiService::CLOCK_ANOMALY_ABSENTEEISM,
        ])->select(['clock_id','dates','anomaly_type','anomaly_time','is_serious_late'])
            ->first();
        return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
    }


    //人事主动修改用户考勤
    public function UpdateUserClockForHr($data){
        $check_result = (new UpdateUserClockForHrRequest())->add($data);
        if($check_result !== true) return $check_result;

        try{
            DB::transaction(function () use ($data){
                $data['admin_id'] = Auth::id();
                AttendanceApiUpdateUserClockForHr::query()->create($data);

                //删除打卡记录
                switch ($data['anomaly_type']){
                    case AttendanceApiService::CLOCK_ANOMALY_NORMAL:
                        //修改为正常， 删除打卡记录， 重新生成， 修改异常记录
                        $this->deleteClock($data);
                        $data['datetimes'] = $data['dates'] ." ".$data['work_time'];
                        $data['clock_address_type'] = AttendanceApiService::CLOCK_ADDRESS_IN;
                        $clock = AttendanceApiClock::query()->create($data);

                        AttendanceApiAnomaly::query()
                            ->where('id', $data['anomaly_id'])
                            ->update([
                                'anomaly_type'=> $data['anomaly_type'],
                                'clock_id'=> $clock->id,
                            ]);
                        break;
                    case AttendanceApiService::CLOCK_ANOMALY_LATE:
                        $this->deleteClock($data);
                        $datetimes = strtotime($data['dates'] ." ".$data['work_time']) + $data['anomaly_time'] * 60;
                        $data['datetimes'] = date('Y-m-d H:i:s', $datetimes);
                        $data['clock_address_type'] = AttendanceApiService::CLOCK_ADDRESS_IN;
                        $clock = AttendanceApiClock::query()->create($data);

                        AttendanceApiAnomaly::query()
                            ->where('id', $data['anomaly_id'])
                            ->update([
                                'anomaly_type'=> $data['anomaly_type'],
                                'clock_id'=> $clock->id,
                            ]);
                        break;

                    case AttendanceApiService::CLOCK_ANOMALY_MISSING:
                        $this->deleteClock($data);

                        AttendanceApiAnomaly::query()
                            ->where('id', $data['anomaly_id'])
                            ->update([
                                'anomaly_type'=> $data['anomaly_type'],
                            ]);
                        break;
                }
            });
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function deleteClock($data){
        return AttendanceApiClock::query()
            ->where('type', $data['type'])
            ->where('user_id', $data['user_id'])
            ->where('classes_id', $data['classes_id'])
            ->where('dates', $data['dates'])
            ->where('clock_nums', $data['clock_nums'])
            ->delete();
    }

    public function deleteAnomaly($data){
        return AttendanceApiAnomaly::query()
            ->where('user_id', $data['user_id'])
            ->where('dates', $data['dates'])
            ->where('anomaly_type', $data['anomaly_type'])
            ->where('clock_nums', $data['clock_nums'])
            ->delete();
    }
}

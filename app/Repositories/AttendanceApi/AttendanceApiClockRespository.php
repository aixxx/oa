<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Http\Requests\AttendanceApi\AttendanceApiClockRequest;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClasses;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\AttendanceApi\AttendanceApiScheduling;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Services\AttendanceApi\AttendanceApiService;
use App\Repositories\Repository;
use DB;

class AttendanceApiClockRespository extends Repository {

    public function model() {
        return AttendanceApiClock::class;
    }

    /**
    *   获取打卡信息
     */
    public function clockInfo($data, $user){
        //参数验证
        $check_result = app()->make(AttendanceApiClockRequest::class)->attendanceClockInfoValidatorForm($data);
        if($check_result !== true) return $check_result;

        //获取考勤规则
        $rules = AttendanceApiStaff::getUserAttendanceRule($user, $data);
        if($rules['attendance']['system_type'] == AttendanceApiService::ATTENDANCE_SYTTEM_FIXED){
            $list['classes'] = AttendanceApiClasses::query()->find($rules['attendance']['classes']['id']);
        }elseif ($rules['attendance']['system_type'] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT){
            $scheduling_info = AttendanceApiScheduling::getSchedulingInfo($user->id, $data['dates']);
            $list['classes'] = AttendanceApiClasses::query()->find($scheduling_info["classes_id"]);
        }else{
            $list['classes'] = null;
        }
        //获取当天打卡记录
        $list['clock'] = AttendanceApiClock::query()->where([
            'user_id' => $user->id,
            'dates' => $data['dates'],
        ])->select(["id","dates","datetimes","remark","remark_image","type","clock_address_type","clock_nums"])->get();
        return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    /**
    *   考勤打卡
     */
    public function clock($data, $user, $YmdHis = ""){
        //参数验证
        $check_result = app()->make(AttendanceApiClockRequest::class)->attendanceClockValidatorForm($data);
        if($check_result !== true) return $check_result;

        $YmdHis = $YmdHis ? $YmdHis :Dh::now();
        //获取考勤规则
        $rules = AttendanceApiStaff::getUserAttendanceRule($user, $data);
        //获取上下班时间
        if(Q($rules, 'attendance', 'classes') && isset($data['clock_nums']) && $data['clock_nums']){
            $work_time = AttendanceApiService::isWorkTimeByType(Q($rules, 'attendance', 'classes'), $data['clock_nums'], $data, $YmdHis);
        }else{
            $work_time = AttendanceApiService::getUserWorkTime($data, $user, $rules, $YmdHis);
        }
        if($work_time === false) return returnJson('不在打卡时间段内', ConstFile::API_RESPONSE_FAIL);
        //实例化model
        $clock_model = new AttendanceApiClock();
        if($rules["attendance"]["is_getout_clock"] !== AttendanceApiService::GETOUT_NORMAL){
            return returnJson('该考勤组不允许外勤打卡', ConstFile::API_RESPONSE_FAIL);
        }
        if($data['type'] == AttendanceApiService::BEGIN_WORK){
            //上班卡只能打一次
            $begin_work_clock = AttendanceApiClock::query()->where([
                'user_id' => $user->id,
                'dates' => $data['dates'],
                'type' => $data['type'],
                'clock_nums' => $work_time['clock_nums'],
            ])->count();
            if($begin_work_clock >= 1) return returnJson('上班卡只能打一次', ConstFile::API_RESPONSE_FAIL);
            //上班打卡
            $result = $clock_model->addBeginWorkClock($data, $user, $rules, $YmdHis, $work_time);
            return $result;
        }elseif ($data['type'] == AttendanceApiService::END_WORK){
            //休息日 打下班卡， 必须先打上班卡
            $is_work_day = AttendanceApiService::isWorkingDay($data['dates'], $rules);
            $begin_time = "";
            if($is_work_day !== true){
                $begin_time = AttendanceApiClock::query()->where([
                    'user_id' => $user->id,
                    'type' => AttendanceApiService::BEGIN_WORK,
                    'dates' => $data['dates'],
                    'clock_nums' => AttendanceApiService::ATTENDANCE_CLASSES_ONE,
                ])->orderBy('datetimes','asc')->first();
                if(empty($begin_time)) return returnJson('休息日 打下班卡， 必须先打上班卡', ConstFile::API_RESPONSE_FAIL);
            }
            $result = $clock_model->addEndWorkClock($data, $user, $rules, $YmdHis, $work_time, $begin_time);
            return $result;
        }
    }

    /**
    *   更新打卡
     */
    public function updateClock($data, $user)
    {
        $check_result = app()->make(AttendanceApiClockRequest::class)->attendanceUpdateClockValidatorForm($data);
        if($check_result !== true) return $check_result;

        if($data['type'] == AttendanceApiService::BEGIN_WORK){
            AttendanceApiClock::query()->where([
                'dates'=> $data['dates'],
                'user_id'=> $user->id,
                'type'=> $data['type']
            ])->delete();
        }
        return $this->clock($data, $user);
    }
}

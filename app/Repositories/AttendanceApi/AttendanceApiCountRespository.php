<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Http\Requests\AttendanceApi\AttendanceApiCountRequest;
use App\Models\AttendanceApi\AttendanceApi;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClasses;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\AttendanceApi\AttendanceApiNationalHolidays;
use App\Models\AttendanceApi\AttendanceApiScheduling;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Repositories\Repository;
use App\Services\AttendanceApi\AttendanceApiService;
use Carbon\Carbon;

class AttendanceApiCountRespository extends Repository {

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
     * 获取统计基本信息
     * */
    public function countBasicInfo($user){
        //所在考勤组 规则
        $month = [];
        if($user->join_at){
            $begin_month = Carbon::parse($user->join_at)->startOfMonth();
        }else{
            $begin_month = Carbon::now()->startOfMonth();
        }

        $end_month = Carbon::now()->startOfMonth();

        while ($end_month->gte($begin_month)){
            $month[] = substr($end_month->toDateString(),0,7);
            $end_month = $end_month->subMonth();
        }
        $info = AttendanceApiStaff::getUserAttendanceRule($user)->toArray();
        //dd($info);
        $info['user'] = ['chinese_name'=> $user->chinese_name, 'join_at'=> $user->join_at, 'avatar'=> $user->avatar];
        $info['month'] = [$month];
        return returnJson("",ConstFile::API_RESPONSE_SUCCESS, $info);
    }

    public static function getListRemoveHolidays($att_false, $rules, $scheduling = ""){
        switch ($rules["attendance"]["system_type"]){
            //固定制
            case AttendanceApiService::ATTENDANCE_SYTTEM_FIXED:
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
                $rest = [];
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
}


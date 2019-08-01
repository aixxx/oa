<?php
/**
 * Created by PhpStorm.
 * User: chenzhikui
 * Date: 2019/4/9
 * Time: 2:14 PM
 */

namespace App\Http\Controllers\Api\V1\Attendance;


use App\Exceptions\DiyException;
use App\Exceptions\SystemException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Helpers\Dh;
use App\Models\Attendance\AnnualRule;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Models\Company;
use App\Services\AttendanceApi\AttendanceApiService;
use Illuminate\Http\Request;

class AttendanceDayController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function run(Request $request){
        $begin_time =  $request->get('begin_time');
        $end_time  =  $request->get('end_time');

        $user = \Auth::user();
        //获取时间段内所有的日期  最小单位天
        $days = Dh::getbetweenDay($begin_time, $end_time);
        $res = [];
        foreach ($days as $key => $day){
            $data['dates'] = $day;
            //获取考勤组
            //用户排班
            $rules = AttendanceApiStaff::getUserAttendanceRule($user, $data);
            //上班规则
            //获取工作日和非工作日
            $res[$key] = AttendanceApiService::isWorkingDay($data['dates'], $rules);
        }
        return $res;
    }

}
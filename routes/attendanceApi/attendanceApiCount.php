<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\AttendanceApi",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        //考勤前端接口
        //根据用户ID获取考勤规则
        $api->get('attendance-rules','ClockController@rules')->name('attendance.rules');
        //考勤打卡
        $api->post('attendance-clock',['as'=>'attendanceApi.clock','uses'=> 'ClockController@clock']);
        //根据日期获取打卡信息
        $api->get('attendance-clock-info','ClockController@clockInfo')->name('attendance.clock.info');
        //人事主动修改员工考勤记录
        $api->post('attendance-update-user-clock-for-hr','ClockController@UpdateUserClockForHr')->name('attendance.update.user.clock.for.hr');

        
        /**
         *   考勤统计
         */
        //个人 - 一天 - 人事视角
        $api->get('attendance-count-one-day-for-hr/{id}','CountsController@oneDayForHr')->name('attendance.count.one.day.for.hr');
        //个人 - 一个月 - 人事视角
        $api->get('attendance-count-one-month-for-hr/{id}','CountsController@oneMonthForHr')->name('attendance.count.one.month.for.hr');
        //所有人 - 一个月 - 人事视角
        $api->get('attendance-count-all-month-for-hr','CountsController@allMonthForHr')->name('attendance.count.all.month.for.hr');
        //根据类型查询考勤异常
        $api->get('attendance-count-anomaly-by-type','CountsController@getAnomalyByType')->name('attendance.count.anomaly.by.type');
        //所有人 - 一天 - 人事视角
        $api->get('attendance-count-all-day-for-hr','CountsController@alldayForHr')->name('attendance.count.all.day.for.hr');
        //所有人 - 一天 - 打卡明细 - 人事视角
        $api->post('attendance-count-all-day-clock-info-for-hr','CountsController@alldayClockInfoForHr')->name('attendance.count.all.day.clock.info.for.hr');

        $api->get('attendanceApiCountBasicInfo/{id}',['as'=>'attendanceApi.countBasicInfo','uses'=> 'CountsController@countBasicInfo']);

    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

    });
});

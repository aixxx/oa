<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        /**
         *   重建考勤规则
         */
        $api->get('attendanceApi_department',['as'=>'attendanceApi.getAttendanceApiDepartment','uses'=> 'AttendanceApiController@getAttendanceApiDepartment']);    //获取考勤部门

        $api->get('attendanceApi',['as'=>'attendanceApi.getAttendanceApi','uses'=> 'AttendanceApiController@getAttendanceApi']);    //获取考勤列表
        $api->get('attendanceApi/{id}',['as'=>'attendanceApi.getAttendanceApiById','uses'=> 'AttendanceApiController@getAttendanceApiById']);    //根据ID查看考勤组信息
        $api->post('attendanceApi',['as'=>'attendanceApi.addAttendanceApi','uses'=> 'AttendanceApiController@addAttendanceApi']);    //新增考勤组
        $api->post('attendanceApi/{id}',['as'=>'attendanceApi.updateAttendanceApi','uses'=> 'AttendanceApiController@updateAttendanceApi']);    //修改考勤组
        $api->get('attendanceApi_del/{id}',['as'=>'attendanceApi.delAttendanceApi','uses'=> 'AttendanceApiController@delAttendanceApi']);    //删除考勤组

        $api->get('attendanceApi_classes',['as'=>'attendanceApi.getClasses','uses'=> 'AttendanceApiController@getClasses']);    //获取班次列表
        $api->get('attendanceApi_classes/{id}',['as'=>'attendanceApi.getClassesById','uses'=> 'AttendanceApiController@getClassesById']);    //获取班次信息
        $api->post('attendanceApi_classes',['as'=>'attendanceApi.addClasses','uses'=> 'AttendanceApiController@addClasses']);    //新增班次
        $api->post('attendanceApi_classes/{id}',['as'=>'attendanceApi.updateClasses','uses'=> 'AttendanceApiController@updateClasses']);    //修改班次
        $api->get('attendanceApi_classes_del/{id}',['as'=>'attendanceApi.delClassesById','uses'=> 'AttendanceApiController@delClassesById']);    //删除班次

        $api->post('attendanceApi_cycle',['as'=>'attendanceApi.addCycle','uses'=> 'AttendanceApiController@addCycle']);    //新增周期设置
        $api->post('attendanceApi_cycle/{id}',['as'=>'attendanceApi.updateCycle','uses'=> 'AttendanceApiController@updateCycle']);    //修改周期设置
        $api->get('attendanceApi_cycle/{id}',['as'=>'attendanceApi.getCycleById','uses'=> 'AttendanceApiController@getCycleById']);    //根据ID查看周期信息

        $api->post('attendanceApi_scheduling/{id}',['as'=>'attendanceApi.schedulingAction','uses'=> 'AttendanceApiController@schedulingAction']);    //排班

        //加班规则
        $api->get('attendance-overtime-rule', ['as'=> 'attendance.overtime.rule', 'uses'=>'AttendanceApiController@getOvertimeRuleList']);
        $api->get('attendance-overtime-rule-info', ['as'=>'attendance.overtime.rule.info', 'uses'=>'AttendanceApiController@getOvertimeRuleInfo']);
        $api->get('attendance-overtime-rule-delete', ['as'=> 'attendance.overtime.rule.delete','uses'=>'AttendanceApiController@DelOvertimeRule']);
        $api->post('attendance-overtime-rule', ['as'=>'attendance.overtime.rule.add','uses'=>'AttendanceApiController@addOvertimeRule']);

        //添加编辑考勤组时， 选择人员和部门之前，先判断是否参与其他考勤组
        $api->get('attendanceApi_isUseByUserid',['as'=>'attendanceApi.isUser','uses'=> 'AttendanceApiController@isUser']);
        $api->get('attendanceApi_isUseByDepartmentid',['as'=>'attendanceApi.attendanceApi_isDepartment','uses'=> 'AttendanceApiController@isDepartment']);

    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
        $api->post('user/login', 'AuthController@authenticate');  //登录授权
    });
});

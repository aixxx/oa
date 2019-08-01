<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //会议
        $api->group(['prefix' => '/meeting'], function($api){
            $api->post('/setadd', ['as' => 'meeting.setadd', 'uses' => 'MeetingController@setAdd']);//添加会议室
            $api->post('/getlist', ['as' => 'meeting.getlist', 'uses' => 'MeetingController@getList']);//会议室列表搜索
            $api->get('/getinfo', ['as' => 'meeting.getinfo', 'uses' => 'MeetingController@getMeetingToomInfo']);//会议室详情

            $api->get('/monthlist', ['as' => 'meeting.monthlist', 'uses' => 'MeetingController@monthList']);//会议室某个月预约会议的天数
            $api->get('/getdaylist', ['as' => 'meeting.getdaylist', 'uses' => 'MeetingController@getDayList']);//会议室某一天所有预约的会议
            $api->get('/getendtime', ['as' => 'meeting.getendtime', 'uses' => 'MeetingController@getEndTime']);//预约会议根据会议开始时间来获取会议结束时间
            $api->get('/getconfigurelist', ['as' => 'meeting.getconfigurelist', 'uses' => 'MeetingController@getConfigureList']);//新建会议室设备配置列表数据接口


            $api->post('/meetingadd', ['as' => 'meeting.meetingadd', 'uses' => 'MeetingController@meetingAdd']);//添加会议
            $api->get('/getmeetinglist', ['as' => 'meeting.getmeetinglist', 'uses' => 'MeetingController@getMeetingList']);//我创建的会议
            $api->get('/meeingmeinfo', ['as' => 'meeting.meeingmeinfo', 'uses' => 'MeetingController@meetingMeInfo']);//我创建的会议详情
            $api->get('/getpluslist', ['as' => 'meeting.getpluslist', 'uses' => 'MeetingController@getPlusList']);//我参加的会议
            $api->get('/passmeeting', ['as' => 'meeting.passmeeting', 'uses' => 'MeetingController@passMeeting']);//审核通过
            $api->get('/rejectmeeting', ['as' => 'meeting.rejectmeeting', 'uses' => 'MeetingController@rejectMeeting']);//审核失败
            $api->get('/meetingreviewedinfo', ['as' => 'meeting.meetingreviewedinfo', 'uses' => 'MeetingController@meetingReviewedInfo']);//审核人的会议详情接口
            $api->get('/meetinginfo', ['as' => 'meeting.meetinginfo', 'uses' => 'MeetingController@meetingInfo']);//参与人的会议详情接口
            $api->get('/setmeetingsigin', ['as' => 'meeting.setmeetingsigin', 'uses' => 'MeetingController@setMeetingSigin']);//参与人签到
            $api->get('/gettasklist', ['as' => 'meeting.gettasklist', 'uses' => 'MeetingController@getTaskList']);//会议任务列表
            $api->get('/setnosigninremind', ['as' => 'meeting.setnosigninremind', 'uses' => 'MeetingController@setNosigninRemind']);//会议发布人给未签到的人进行提醒
            //$api->post('/setmeetingsummary', ['as' => 'meeting.setmeetingsummary', 'uses' => 'MeetingController@setMeetingSummary']);//会议发布人添加会议纪要
            $api->get('/withdraw', ['as' => 'meeting.withdraw', 'uses' => 'MeetingController@withdraw']);//会议撤销

            $api->get('/test', ['as' => 'meeting.test', 'uses' => 'MeetingController@remindTime']);//会议截止时间提醒
            $api->get('/setsummary', ['as' => 'meeting.setSummary', 'uses' => 'MeetingController@setSummary']);//会议纪要添加工作工作流

            $api->get('/getsealstype', ['as' => 'meeting.getsealstype', 'uses' => 'MeetingController@getSealsType']);//公章管理元获取公章列表

            $api->get('/getAllDepartmentList', ['as' => 'meeting.getAllDepartmentList', 'uses' => 'AuthController@getAllDepartmentList']);//公章管理元获取公章列表
            $api->get('/getAllDepartmentUserList', ['as' => 'meeting.getAllDepartmentUserList', 'uses' => 'AuthController@getAllDepartmentUserList']);//公章管理元获取公章列表
        });
    });
});

<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //绩效
        $api->group(['prefix' => '/performance'], function($api){
            $api->get('/update', ['as' => 'performance.update', 'uses' => 'PerformanceController@setUpdate']);//测试用户添加模板
            $api->post('/setadd', ['as' => 'performance.setadd', 'uses' => 'PerformanceController@setAdd']);//绩效模板添加
            $api->get('/list', ['as' => 'performance.list', 'uses' => 'PerformanceController@lists']);//绩效模板列表
            $api->get('/del', ['as' => 'performance.del', 'uses' => 'PerformanceController@del']);//绩效模板删除
            $api->get('/getObjectList', ['as' => 'performance.getObjectList', 'uses' => 'PerformanceController@getObjectList']);//考核对象数据列表
            $api->get('/getUserList', ['as' => 'performance.getUserList', 'uses' => 'PerformanceController@getUserList']);//参与考核人列表
            $api->get('/getMeApplyList', ['as' => 'performance.getMeApplyList', 'uses' => 'PerformanceController@getMeApplyList']);//被审核人的绩效申请列表
            $api->get('/getMeApplyInfo', ['as' => 'performance.getMeApplyInfo', 'uses' => 'PerformanceController@getMeApplyInfo']);//绩效详情自评接口（被审核人）
            $api->post('/setReview', ['as' => 'performance.setReview', 'uses' => 'PerformanceController@setReview']);//提交自评
            $api->get('/detail', ['as' => 'performance.detail', 'uses' => 'PerformanceController@detail']);//绩效模板详情
            $api->get('/getdplist', ['as' => 'performance.getdplist', 'uses' => 'PerformanceController@getDpList']);//部门绩效--列表接口(打分人)
            $api->get('/getdpscoring', ['as' => 'performance.getdpscoring', 'uses' => 'PerformanceController@getDpScoring']);// 绩效详情--打分界面 (打分人)
            $api->post('/setauditscoring', ['as' => 'performance.setauditscoring', 'uses' => 'PerformanceController@setAuditScoring']);// 绩效审核打分 (打分人)
            $api->get('/getdpscoringend', ['as' => 'performance.getdpscoringend', 'uses' => 'PerformanceController@getDpScoringEnd']);// 绩效打分完成界面--详情接口
            $api->get('/getdpevaluate', ['as' => 'performance.getdpevaluate', 'uses' => 'PerformanceController@getDpevaluate']);// 绩效自评详情 (打分人)
            $api->get('/getacceptscoring', ['as' => 'performance.getacceptscoring', 'uses' => 'PerformanceController@getAcceptScoring']);// 被审核人接受打分（绩效完成）
            $api->get('/getScoringInfo', ['as' => 'performance.getScoringInfo', 'uses' => 'PerformanceController@getScoringInfo']);// 绩效申请详情--执行中绩效,已完成（被审核人）
            $api->get('/getrejectinfo', ['as' => 'performance.getrejectinfo', 'uses' => 'PerformanceController@getRejectInfo']);// 绩效驳回重新自评详情页面接口---被审核人
            $api->get('/setauditreject', ['as' => 'performance.setauditreject', 'uses' => 'PerformanceController@setAuditReject']);// 绩效审核驳回操作 --打分人
            $api->post('/setrejectreview', ['as' => 'performance.setrejectreview', 'uses' => 'PerformanceController@setRejectReview']);//  绩效驳回重新自评接口--被审核人
            $api->get('/test', ['as' => 'performance.test', 'uses' => 'PerformanceController@tests']);//  测试 个人需要交多少税
            $api->get('/tests', ['as' => 'performance.test', 'uses' => 'PerformanceController@testss']);//  获取有绩效员工的绩效薪资

        });

        //员工手册惩罚模板
        $api->group(['prefix' => '/punishment'], function($api){
            $api->post('/setadd', ['as' => 'punishment.setadd', 'uses' => 'PunishmentController@setAdd']);//添加 修改 惩罚模板
            $api->get('/setdel', ['as' => 'punishment.setdel', 'uses' => 'PunishmentController@setDel']);//删除迟到惩罚数据
            $api->get('/getinfo', ['as' => 'punishment.getinfo', 'uses' => 'PunishmentController@getInfo']);//惩罚模板详情

            $api->post('/setovertimepay', ['as' => 'punishment.setovertimepay', 'uses' => 'PunishmentController@setOvertimePay']);//加班费添加修改
            $api->post('/setlatepay', ['as' => 'punishment.setlatepay', 'uses' => 'PunishmentController@setLatePay']);//迟到扣费添加修改
            $api->post('/setabsenteeism', ['as' => 'punishment.setabsenteeism', 'uses' => 'PunishmentController@setAbsenteeism']);//旷工扣费添加修改
            $api->post('/setleave', ['as' => 'punishment.setleave', 'uses' => 'PunishmentController@setLeave']);//请假扣费添加修改
            $api->get('/getabsenteeismlist', ['as' => 'punishment.getabsenteeismlist', 'uses' => 'PunishmentController@getAbsenteeismList']);//旷工扣费详情
            $api->get('/getovertimepay', ['as' => 'punishment.getovertimepay', 'uses' => 'PunishmentController@getOvertimePay']);//加班费列表
            $api->get('/getlatepaylist', ['as' => 'punishment.getlatepaylist', 'uses' => 'PunishmentController@getLatePayList']);//迟到扣费详情
            $api->get('/getovertimepayinfo', ['as' => 'punishment.getovertimepayinfo', 'uses' => 'PunishmentController@getOvertimePayInfo']);//加班费详情
            $api->get('/setleavelist', ['as' => 'punishment.setleavelist', 'uses' => 'PunishmentController@setLeaveList']);//请假扣费详情
        });
    });
    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        $api->get('performance/setBatchApply', 'PerformanceController@setBatchApply');//给所有人发绩效申请定时脚本
    });

});

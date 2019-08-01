<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 16:15
 * desc: 工作汇报
 */

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        $api->group(['prefix' => '/supervise'], function($api){
            $api->post('/superviseList', ['uses'=>'SuperviseController@superviseList', 'as'=>'supervise.superviseList']);//督办列表
            $api->post('/addCancelSupervise', ['uses'=>'SuperviseController@addCancelSupervise', 'as'=>'supervise.addCancelSupervise']);//添加取消督办
            $api->post('/superviseStatistics', ['uses'=>'SuperviseController@superviseStatistics', 'as'=>'supervise.superviseStatistics']);//督办统计
            $api->post('/appointSupervise', ['uses'=>'SuperviseController@appointSupervise', 'as'=>'supervise.appointSupervise']);//指派督办
            $api->post('/createChildTask', ['uses'=>'SuperviseController@createChildTask', 'as'=>'supervise.createChildTask']);//创建子任务
            $api->get('/taskDetail', ['uses'=>'SuperviseController@taskDetail', 'as'=>'supervise.taskDetail']);//任务详情
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之后在这里写api

        /*$api->group(['prefix' => '/report'], function($api){
            $api->post('/editReport', 'ReportController@editReport');//添加修改报告
            $api->post('/editReportInfo', 'ReportController@editReportInfo');//编辑报告详情
            $api->post('/reportDetail', 'ReportController@reportDetail');//报告详情
            $api->post('/likeReport', 'ReportController@likeReport');//报告点赞
            $api->post('/delReport', 'ReportController@delReport');//删除报告
            $api->post('/readReport', 'ReportController@readReport');//阅读报告
            $api->post('/readList', 'ReportController@readList');//报告已读未读列表
            $api->post('/reportList', 'ReportController@reportList');//报告列表

            $api->post('/ruleInfo', 'ReportController@ruleInfo');//报告规则详情
            $api->post('/editRule', 'ReportController@editRule');//创建修改报告规则
            $api->get('/myNeedReport', 'ReportController@myNeedReport');//我要写的汇报
            $api->get('/myReportRule', 'ReportController@myReportRule');//我的汇报规则
            $api->post('/getPreOrAfterReportTime', 'ReportController@getPreOrAfterReportTime');//获取上一个/下一个汇报时间点
            $api->post('/ruleStatisticsList', 'ReportController@ruleStatisticsList');//统计规则列表
            $api->post('/logReport', 'ReportController@logReport');//日志报表

            $api->post('/templateInfo', 'ReportController@templateInfo');//汇报模版详情
            $api->get('/templateList', 'ReportController@templateList');//汇报模版列表
            $api->post('/templateField', 'ReportController@templateField');//汇报模版字段
            $api->get('/fieldType', 'ReportController@fieldType');//模板字段类型
            $api->post('/editTemplate', 'ReportController@editTemplate');//添加修改汇报模版
        });*/

    });
});
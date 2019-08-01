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
        $api->group(['prefix' => '/report'], function($api){
            $api->post('/editReport', ['uses'=>'ReportController@editReport', 'as'=>'report.editReport']);//添加修改报告
            $api->post('/editReportInfo', ['uses'=>'ReportController@editReportInfo', 'as'=>'report.editReportInfo']);//编辑报告详情
            $api->post('/reportDetail', ['uses'=>'ReportController@reportDetail', 'as'=>'report.reportDetail']);//报告详情
            $api->post('/likeReport', ['uses'=>'ReportController@likeReport', 'as'=>'report.likeReport']);//报告点赞
            $api->post('/delReport', ['uses'=>'ReportController@delReport', 'as'=>'report.delReport']);//删除报告
            $api->post('/readReport', ['uses'=>'ReportController@readReport', 'as'=>'report.readReport']);//阅读报告
            $api->post('/readList', ['uses'=>'ReportController@readList', 'as'=>'report.readList']);//报告已读未读列表
            $api->post('/reportList', ['uses'=>'ReportController@reportList', 'as'=>'report.reportList']);//报告列表

            $api->post('/ruleInfo', ['uses'=>'ReportController@ruleInfo', 'as'=>'report.ruleInfo']);//报告规则详情
            $api->post('/editRule', ['uses'=>'ReportController@editRule', 'as'=>'report.editRule']);//创建修改报告规则
            $api->get('/myNeedReport', ['uses'=>'ReportController@myNeedReport', 'as'=>'report.myNeedReport']);//我要写的汇报
            $api->get('/myReportRule', ['uses'=>'ReportController@myReportRule', 'as'=>'report.myReportRule']);//我的汇报规则
            $api->post('/getPreOrAfterReportTime', ['uses'=>'ReportController@getPreOrAfterReportTime', 'as'=>'report.getPreOrAfterReportTime']);//获取上一个/下一个汇报时间点
            $api->post('/ruleStatisticsList', ['uses'=>'ReportController@ruleStatisticsList', 'as'=>'report.ruleStatisticsList']);//统计规则列表
            $api->post('/logReport', ['uses'=>'ReportController@logReport', 'as'=>'report.logReport']);//日志报表
            $api->get('/getCurrentCycle', ['uses'=>'ReportController@getCurrentCycle', 'as'=>'report.getCurrentCycle']);//获取汇报当前周期

            $api->post('/templateInfo', ['uses'=>'ReportController@templateInfo', 'as'=>'report.templateInfo']);//汇报模版详情
            $api->get('/templateList', ['uses'=>'ReportController@templateList', 'as'=>'report.templateList']);//汇报模版列表
            $api->post('/templateField', ['uses'=>'ReportController@templateField', 'as'=>'report.templateField']);//汇报模版字段
            $api->get('/fieldType', ['uses'=>'ReportController@fieldType', 'as'=>'report.fieldType']);//模板字段类型
            $api->post('/editTemplate', ['uses'=>'ReportController@editTemplate', 'as'=>'report.editTemplate']);//添加修改汇报模版
            $api->post('/delReportTemplate', ['uses'=>'ReportController@delReportTemplate', 'as'=>'report.delReportTemplate']);//删除汇报模版
            $api->post('/delReportRule', ['uses'=>'ReportController@delReportRule', 'as'=>'report.delReportRule']);//删除汇报规则
            $api->post('/remindReader', ['uses'=>'ReportController@remindReader', 'as'=>'report.remindReader']);//提醒未读汇报的人
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
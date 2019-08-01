<?php
//待入职用户 转正  路由

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/workflow/positive'], function ($api) {
            $api->get('/list', ['as' => 'api.positive.list','uses' => 'PositiveController@toDoList']);//流程列表
            $api->get('/flow_list', ['as' => 'api.positive.flow.list','uses' => 'PositiveController@workflowList']);//申请表
            $api->get('/flow_create', ['as' => 'api.positive.flow.create','uses' => 'PositiveController@createWorkflow']);//申请单
            $api->post('/flow_store', ['as' => 'api.positive.flow.store','uses' => 'PositiveController@storeWorkflow']);//添加个人申请
            $api->get('/flow_show', ['as' => 'api.positive.flow.show', 'uses' => 'PositiveController@showWorkflow']);//个人申请列表
            $api->get('/show', ['as' => 'api.positive.show', 'uses' => 'PositiveController@show']);//个人申请列表
            //审批人
            $api->get('/auditor_flow_show', ['as' => 'api.positive.auditor_flow.show', 'uses' => 'PositiveController@showAuditorWorkflow']);
            $api->post('/pass', ['as' => 'api.positive.flow.pass','uses' => 'PositiveController@passWorkflow']);//通过
            $api->post('/reject', ['as' => 'api.positive.flow.reject','uses' => 'PositiveController@rejectWorkflow']);//驳回
            //工资包
            $api->post('/wageStore', ['as' => 'api.positive.store','uses' => 'PositiveController@storeWageWorkflow']);//添加工资包个人申请
            $api->get('/wageShowApply', ['as' => 'api.positive.show', 'uses' => 'PositiveController@wageShowApply']);//工资包申请列表
            $api->get('/wage_show', ['as' => 'api.flow.wage_show', 'uses' => 'PositiveController@wageShow']);//工资包列表
            //流程查询
            $api->get('/processQuery', ['as' => 'api.flow.processQuery', 'uses' => 'PositiveController@processQuery']);//流程列表
            $api->get('/entryShow', ['as' => 'api.positive.entryShow', 'uses' => 'PositiveController@entryShow']);//流程列表
            $api->get('/procShow', ['as' => 'api.positive.proc', 'uses' => 'PositiveController@procShow']);//流程列表
            //判断唯一
            $api->get('/positiveEntry', ['as' => 'api.positive.positive_entry', 'uses' => 'PositiveController@positiveApplyEntry']);
            $api->get('/positiveWage', ['as' => 'api.positive.positive_wage', 'uses' => 'PositiveController@fetchPositiveWageEntry']);
        });
    });
});
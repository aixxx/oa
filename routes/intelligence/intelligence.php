<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9
 * Time: 13:30
 */

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Intelligence", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/intelligence'], function ($api) {
            //情报分类
            $api->post('/addType', ['as' => 'api.intelligenceTypeAdd', 'uses' => 'IntelligenceController@intelligenceTypeAdd']);//情报分类添加
            $api->post('/editType', ['as' => 'api.intelligenceTypeEdit', 'uses' => 'IntelligenceController@intelligenceTypeEdit']);//情报分类编辑
            $api->post('/deteleType', ['as' => 'api.intelligenceTypeDelete', 'uses' => 'IntelligenceController@intelligenceTypeDelete']);//情报分类删除
            $api->post('/showType', ['as' => 'api.intelligenceTypeShow', 'uses' => 'IntelligenceController@intelligenceTypeShow']);//情报分类列表

            //情报目标
            $api->post('/create', ['as' => 'api.intelligenceAdd', 'uses' => 'IntelligenceController@intelligenceAdd']);//情报目标添加
            $api->post('/inteShow', ['as' => 'api.intelligenceShow', 'uses' => 'IntelligenceController@intelligenceShow']);//情报大厅列表
            $api->post('/inteAdminShow', ['as' => 'api.intelligenceAdminShow', 'uses' => 'IntelligenceController@intelligenceAdminShow']);//管理员列表
            $api->post('/inteAdminDetails', ['as' => 'api.intelligenceAdminDetails', 'uses' => 'IntelligenceController@intelligenceAdminDetails']);//管理员汇总列表
            $api->post('/inteMyShow', ['as' => 'api.intelligenceMyShow', 'uses' => 'IntelligenceController@intelligenceMyShow']);//情报员列表
            //认领任务
            $api->post('/claim', ['as' => 'api.intelligenceClaim', 'uses' => 'IntelligenceController@intelligenceClaim']);//认领任务
            $api->post('/consent', ['as' => 'api.intelligenceConsent', 'uses' => 'IntelligenceController@intelligenceConsent']);//确认任务
            $api->post('/refused', ['as' => 'api.intelligenceRefused', 'uses' => 'IntelligenceController@intelligenceRefused']);//拒绝任务

            //工作流
//            $api->get('/list', ['as' => 'api.Intelligence.list', 'uses' => 'IntelligenceController@toDoList']);
            $api->get('/flow_list', ['as' => 'api.inte.flow.list', 'uses' => 'IntelligenceController@workflowList']);
            $api->get('/flow_create', ['as' => 'api.inte.create', 'uses' => 'IntelligenceController@createWorkflow']);
            $api->post('/flow_store', ['as' => 'api.inte.flow.store','uses' => 'IntelligenceController@storeWorkflow']);//添加个人申请
            $api->get('/flow_show', ['as' => 'api.inte.flow.show', 'uses' => 'IntelligenceController@showWorkflow']);//个人申请列表
            $api->get('/show', ['as' => 'api.inte.show', 'uses' => 'IntelligenceController@show']);//个人申请列表

            //审批人
            $api->get('/auditor_flow_show', ['as' => 'api.inte.auditor_flow.show', 'uses' => 'IntelligenceController@showAuditorWorkflow']);
            $api->post('/pass', ['as' => 'api.inte.flow.pass','uses' => 'IntelligenceController@passWorkflow']);//通过
            $api->post('/reject', ['as' => 'api.inte.flow.reject','uses' => 'IntelligenceController@rejectWorkflow']);//驳回
        });
    });
});
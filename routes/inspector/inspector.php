<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14
 * Time: 14:05
 */

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Inspector", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/inspector'], function ($api) {
            //列表
            $api->post('/show', ['uses' => 'InspectorController@myShow']);//列表
            $api->post('/list', ['uses' => 'InspectorController@adminList']);//大厅
            //工作流
//            $api->get('/list', ['as' => 'api.Intelligence.list', 'uses' => 'IntelligenceController@toDoList']);
            $api->get('/flow_list', ['as' => 'api.insp.flow.list', 'uses' => 'InspectorController@workflowList']);
            $api->get('/flow_create', ['as' => 'api.insp.create', 'uses' => 'InspectorController@createWorkflow']);
            $api->post('/flow_store', ['as' => 'api.insp.flow.store','uses' => 'InspectorController@storeWorkflow']);//添加个人申请
            $api->get('/flow_show', ['as' => 'api.insp.flow.show', 'uses' => 'InspectorController@showWorkflow']);//个人申请列表
            $api->get('/show', ['as' => 'api.insp.show', 'uses' => 'InspectorController@show']);//个人申请列表

            //审批人
            $api->get('/auditor_flow_show', ['as' => 'api.insp.auditor_flow.show', 'uses' => 'InspectorController@showAuditorWorkflow']);
            $api->post('/pass', ['as' => 'api.insp.flow.pass','uses' => 'InspectorController@passWorkflow']);//通过
            $api->post('/reject', ['as' => 'api.insp.flow.reject','uses' => 'InspectorController@rejectWorkflow']);//驳回
        });
    });
});
<?php
//待入职用户 入离职 转正 路由

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/workflow'], function ($api) {
            $api->get('/list', ['as' => 'api.entry.list', 'uses' => 'EntryController@toDoList']);
            $api->get('/flow_list', ['as' => 'api.flow.list', 'uses' => 'EntryController@workflowList']);
			$api->get('/create_list', ['as' => 'api.flow.create_list', 'uses' => 'EntryController@createList']);
            $api->get('/flow_create', ['as' => 'api.flow.create', 'uses' => 'EntryController@createWorkflow']);
            $api->post('/flow_store', ['as' => 'api.flow.store', 'uses' => 'EntryController@storeWorkflow']);
            $api->get('/flow_show', ['as' => 'api.flow.show', 'uses' => 'EntryController@showWorkflow']);

            $api->get('/history', ['as' => 'api.flow.history', 'uses' => 'EntryController@history']);
            $api->post('/cancel', ['as' => 'api.flow.cancel', 'uses' => 'EntryController@cancel']);
            $api->post('/urge_save', ['as' => 'api.flow.urge_save', 'uses' => 'EntryController@urgeSave']);
            $api->get('/urge_show', ['as' => 'api.flow.urge_show', 'uses' => 'EntryController@urgeShow']);

            //审批人
            $api->get('/auditor_flow_show', ['as' => 'api.auditor_flow.show', 'uses' => 'EntryController@showAuditorWorkflow']);
            $api->post('/pass', ['as' => 'api.flow.pass', 'uses' => 'EntryController@passWorkflow']);
            $api->post('/reject', ['as' => 'api.flow.reject', 'uses' => 'EntryController@rejectWorkflow']);
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之后在这里写api
        $api->post('user/login', 'AuthController@authenticate');  //登录授权
        $api->get('/basic_info', ['as' => 'api.set.basic_info', 'uses' => 'EntryController@fetchBasicInfo']);
    });
});
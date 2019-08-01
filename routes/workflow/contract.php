<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //合同
        $api->group(['prefix' => '/workflow/contract'], function($api){
            $api->get('/list', ['as' => 'api.contract.list', 'uses' => 'ContractController@toDoList']);
            $api->get('/flow_list', ['as' => 'api.contract.flow.list', 'uses' => 'ContractController@workflowList']);
            $api->get('/flow_create', ['as' => 'api.contract.flow.create', 'uses' => 'ContractController@createWorkflow']);
            $api->post('/flow_store', ['as' => 'api.contract.flow.store', 'uses' => 'ContractController@storeWorkflow']);
            $api->get('/flow_show', ['as' => 'api.contract.flow.show', 'uses' => 'ContractController@showWorkflow']);

            $api->get('/history', ['as' => 'api.contract.flow.history', 'uses' => 'ContractController@history']);
            $api->get('/show', ['as' => 'api.contract.show', 'uses' => 'ContractController@show']);//个人申请列表

            //审批人
            $api->get('/auditor_flow_show', ['as' => 'api.contract.auditor_flow.show', 'uses' => 'ContractController@showAuditorWorkflow']);
            $api->post('/pass', ['as' => 'api.contract.flow.pass', 'uses' => 'ContractController@passWorkflow']);
            $api->post('/reject', ['as' => 'api.contract.flow.reject', 'uses' => 'ContractController@rejectWorkflow']);
            //人事待办

            $api->get('/hr_pending_list', ['as' => 'api.contract.hrlist', 'uses' => 'ContractController@hrlist']);
            $api->get('/getperformanceid', ['as' => 'api.contract.getperformanceid', 'uses' => 'ContractController@getperformanceid']);
            $api->get('/fetchContract', ['as' => 'api.contract.fetchContract', 'uses' => 'ContractController@fetchContract']);
            $api->get('/tatol', ['as' => 'api.contract.tatol', 'uses' => 'ContractController@getuserstatustatol']);
        });
    });


    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
    });
});

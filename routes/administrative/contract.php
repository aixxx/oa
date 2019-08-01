<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/28
 * Time: 14:43
 */
//行政合同

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Administrative", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/administrative/contract'], function ($api) {
            $api->get('/list', ['as'=>'api.administrative.contract.show','uses' => 'ContractController@toDoList']);
            $api->get('/flow_list', [ 'uses' => 'ContractController@workflowList']);
            $api->get('/flow_create', ['as'=>'api.administrative.contract.create','uses' => 'ContractController@createWorkflow']);
            $api->post('/flow_store', ['uses' => 'ContractController@storeWorkflow']);
            $api->get('/flow_show', ['uses' => 'ContractController@showWorkflow']);

            //审批人
            $api->get('/auditor_flow_show', ['as'=>'api.administrative.contract.auditor_flow_show','uses' => 'ContractController@showAuditorWorkflow']);
            $api->post('/pass', ['uses' => 'ContractController@passWorkflow']);
            $api->post('/reject', ['uses' => 'ContractController@rejectWorkflow']);

            //行政合同
            $api->get('/show', ['uses' => 'ContractController@contractShow']);//合同列表
            $api->post('/search', ['uses' => 'ContractController@contractSearch']);//合同检索
            $api->post('/workShow', ['uses' => 'ContractController@workflowShows']);//流程详情
        });
    });
});
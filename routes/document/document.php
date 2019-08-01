<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    //需要登录的
    $api->group(['namespace' => 'App\Http\Controllers\Api\V1','middleware'=>'auth:api'], function ($api) {
        //文件
        $api->group(['prefix' => '/document'], function($api){
            $api->get('/show_form', ['as' => 'document.show_form', 'uses' => 'DocumentController@showDocumentForm']);
            $api->any('/create_document', ['as' => 'document.create_document', 'uses' => 'DocumentController@createDocument']);
            $api->get('/flow_show', ['as' => 'document.flow_show', 'uses' => 'DocumentController@showWorkflow']);
            $api->get('/document_show', ['as' => 'document.document_show', 'uses' => 'DocumentController@showDocList']);

            $api->post('/cancel', ['as' => 'document.cancel', 'uses' => 'DocumentController@cancelWorkflow']);


            //审批人
            $api->get('/auditor_flow_show', ['as' => 'document.auditor_flow.show', 'uses' => 'DocumentController@showAuditorWorkflow']);
            $api->post('/pass', ['as' => 'document.pass', 'uses' => 'DocumentController@passWorkflow']);
            $api->post('/reject', ['as' => 'document.reject', 'uses' => 'DocumentController@rejectWorkflow']);
        });
    });


  //不需要登录的
    $api->group(['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
        //文件
        $api->group(['prefix' => '/document'], function($api){

        });
    });
});
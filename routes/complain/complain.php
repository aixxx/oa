<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Salary",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/complain'], function($api){
            //个人信息
            $api->get('/show_form', ['as' => 'api.complain.showForm', 'uses' => 'ComplainController@showForm']);
            $api->post('/store', ['as' => 'api.complain.store', 'uses' => 'ComplainController@store']);
            $api->post('/show', ['as' => 'api.complain.showForm', 'uses' => 'ComplainController@show']);
            $api->post('/auditor_show', ['as' => 'api.complain.showForm', 'uses' => 'ComplainController@auditorShow']);
        });
    });
});
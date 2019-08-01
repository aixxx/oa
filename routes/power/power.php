<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Power",'middleware'=>'auth:api'], function ($api) {
        //权限
        $api->group(['prefix' => '/power'], function($api){
            $api->get('/',['as'=>'power.index','uses'=> 'PowerController@index']);
        });
    });


    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

    });
});

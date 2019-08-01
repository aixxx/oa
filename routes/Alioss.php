<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {

        $api->post('alioss/upload_oss','AliossController@upload_oss');

        $api->post('alioss/upload','AliossController@upload');
    });


});



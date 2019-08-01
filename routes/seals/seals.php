<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {

        $api->post('seals/seals_type_add','SealsController@seals_type_add');
        $api->post('seals/upload_seals','SealsController@upload_seals');

    });


});
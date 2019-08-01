<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Salary",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        $api->get('score-count-month','ScoreController@getList');
        $api->get('score-count-month-info','ScoreController@getListInfo');
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

    });
});

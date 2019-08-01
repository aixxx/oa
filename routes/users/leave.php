<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/users'], function ($api) {
            $api->get('/fire_users', ['as' => 'api.users.fire_users', 'uses' => 'LeaveController@showFireForm']);
            $api->post('/add_fire_users', ['as' => 'api.users.add_fire_users', 'uses' => 'LeaveController@storeFireForm']);
            $api->get('/leave_hand_over', ['as' => 'api.users.leave_hand_over', 'uses' => 'LeaveController@showLeaveHandOverForm']);
            $api->get('/active_leave', ['as' => 'api.users.active_leave', 'uses' => 'LeaveController@showActiveLeaveForm']);
            $api->get('/check_active_leave', ['as' => 'api.users.check_active_leave', 'uses' => 'LeaveController@checkCanApplyLeaveEntry']);
            //checkCanApplyLeaveEntry
        });
    });
});
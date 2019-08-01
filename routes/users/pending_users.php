<?php
//待入职用户 入离职 转正 路由

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //$api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/pending_users'], function ($api) {
            //hr管理待入职用户
//            $api->get('/list', ['as' => 'pending_users.list', 'uses' => 'PendingUsersController@index']);
            $api->post('/create', ['as' => 'pending_users.create', 'uses' => 'PendingUsersController@create']);
//            $api->post('/delete', ['as' => 'pending_users.delete', 'uses' => 'PendingUsersController@delete']);
//            $api->post('/edit', ['as' => 'pending_users.edit', 'uses' => 'PendingUsersController@edit']);
            $api->get('/show_form', ['as' => 'pending_users.show_form', 'uses' => 'PendingUsersController@showPendingUserForm']);
            $api->get('/fetch_position', ['as' => 'pending_users.fetch_position', 'uses' => 'PendingUsersController@fetchPosition']);
            

        });
    });
});
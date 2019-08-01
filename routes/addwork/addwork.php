<?php
//反馈

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
        // 需要获取用户信息的
        $api->group(['prefix' => '/addwork'], function ($api) {
            $api->post('addwork_field', ['as' => 'addwork.addwork_field', 'uses' => 'AddworkController@addwork_field']); // 加班申请模版
            $api->post('addworks', ['as' => 'addwork.addworks', 'uses' => 'AddworkController@addworks']); // 加班申请写入
//        $api->post('addwork_list_submit', ['as' => 'addwork.addwork_list_submit', 'uses' => 'AddworkController@addwork_list_submit']); // 加班申请列表-提交人视角
//        $api->post('addwork_list_audit', ['as' => 'addwork.addwork_list_audit', 'uses' => 'AddworkController@addwork_list_audit']); // 加班申请列表-审批人视角
            $api->post('detail', ['as' => 'addwork.detail', 'uses' => 'AddworkController@detail']); // 加班申请详情
//        $api->post('detail_audit', ['as' => 'addwork.detail_audit', 'uses' => 'AddworkController@detail_audit']); // 加班申请详情-审批人视角
            $api->post('audit', ['as' => 'addwork.audit', 'uses' => 'AddworkController@audit']); // 加班申请审批
            $api->post('revocation', ['as' => 'addwork.revocation', 'uses' => 'AddworkController@revocation']); // 加班申请撤销
            $api->post('history_list', ['as' => 'addwork.history_list', 'uses' => 'AddworkController@history_list']); // 个人/人事申请列表，历史记录
            $api->post('comment', ['as' => 'addwork.comment', 'uses' => 'AddworkController@comment']); // 评论
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        // 不需要获取用户信息的
        $api->group(['prefix' => '/addwork'], function ($api) {

        });
    });
});
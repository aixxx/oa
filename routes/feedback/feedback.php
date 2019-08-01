<?php
//反馈

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
        // 需要获取用户信息的
        $api->group(['prefix' => '/feedback'], function ($api) {
            $api->get('list', ['as' => 'feedback.feed', 'uses' => 'FeedbackController@feed']);
            $api->get('getlist', ['as' => 'feedback.getlist', 'uses' => 'FeedbackController@getFeedbackList']);
            $api->post('edit', ['as' => 'feedback.feedbackedit', 'uses' => 'FeedbackController@feedbackedit']);
            $api->post('reply', ['as' => 'feedback.reply', 'uses' => 'FeedbackController@reply']);
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        // 不需要获取用户信息的
        $api->group(['prefix' => '/feedback'], function ($api) {
            $api->get('detail', ['as' => 'feedback.feedbackdetail', 'uses' => 'FeedbackController@feedbackdetail']);
        });
    });
});
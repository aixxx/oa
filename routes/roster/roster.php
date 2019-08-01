<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 13:36
 */


$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/roster'], function ($api) {
            //花名册
            $api->post('/userNumber', ['as' => 'roster.userNumber', 'uses' => 'RosterController@userNumber']);//在职，全职，实习，兼职人数统计
            $api->post('/rosterShow', ['as' => 'roster.rosterShow', 'uses' => 'RosterController@rosterShow']);//全员在职人员
            $api->post('/shows', ['as' => 'roster.shows', 'uses' => 'RosterController@shows']);//类型在职人员
            $api->post('/detailShow', ['as' => 'roster.show', 'uses' => 'RosterController@show']);//直属部门人员筛选
            $api->post('/detailShowAll', ['as' => 'roster.showAll', 'uses' => 'RosterController@showAll']);//顶级部门筛选
            $api->post('/search', ['as' => 'roster.search', 'uses' => 'RosterController@search']);//基本搜索
            $api->post('/userSearch', ['as' => 'roster.userSearch', 'uses' => 'RosterController@userSearch']);//人员搜索
            $api->post('/oneUserShow', ['as' => 'roster.oneUserShow', 'uses' => 'RosterController@oneUserShow']);//个人
            $api->post('/userNoPerfect', ['as' => 'roster.userNoPerfect', 'uses' => 'RosterController@userNoPerfect']);//个人完善
            $api->post('/holiday', ['as' => 'roster.userNoPerfect', 'uses' => 'RosterController@holiday']);//剩余假期
            $api->post('/send_turn_positive_msg', ['as' => 'roster.send_turn_positive_msg', 'uses' => 'RosterController@send_turn_positive_msg']);//剩余假期
            $api->post('/send_improving_data_msg', ['as' => 'roster.send_improving_data_msg', 'uses' => 'RosterController@send_improving_data_msg']);//剩余假期
        });
    });
});
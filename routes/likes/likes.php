<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 16:15
 * desc: 工作汇报
 */

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {

        $api->group(['prefix' => '/like'], function($api){
            $api->post('/addLike', ['uses'=>'LikeController@addLike', 'as'=>'like.addLike']);//添加取消点赞
            $api->post('/likeList', ['uses'=>'LikeController@likeList', 'as'=>'like.likeList']);//点赞列表
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之后在这里写api

//        $api->group(['prefix' => '/like'], function($api){
//            $api->post('/addLike', 'LikeController@addLike');//添加取消点赞
//            $api->post('/likeList', 'LikeController@likeList');//点赞列表
//
//        });

    });
});
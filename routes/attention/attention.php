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

        $api->group(['prefix' => '/attention'], function($api){
            $api->post('/addAttention', ['uses'=>'AttentionController@addAttention', 'as'=>'attention.addAttention']);//添加取消关注
            $api->post('/attentionList', ['uses'=>'AttentionController@attentionList', 'as'=>'attention.attentionList']);//关注列表

        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之后在这里写api

        $api->group(['prefix' => '/attention'], function($api){


        });

    });
});
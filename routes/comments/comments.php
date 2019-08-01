<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 13:36
 */
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
       //假期

        $api->group(['prefix' => '/comments'], function($api){

            $api->post('addcomments', ['as' => 'comments.acomments', 'uses' => 'CommentsController@acomments']);
            $api->post('avescore', ['as' => 'comments.ascore', 'uses' => 'CommentsController@ascore']);
            $api->post('getscorelist', ['as' => 'comments.myscore', 'uses' => 'CommentsController@myscore']);
            $api->post('total_comments', ['as' => 'comments.total_comments', 'uses' => 'CommentsController@total_comments']);


        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
        $api->post('user/login', 'AuthController@authenticate');  //登录授权

        $api->group(['prefix' => '/comments'], function($api){

            $api->post('getscorebyoid', ['as' => 'comments.getoscore', 'uses' => 'CommentsController@getoscore']);
            $api->post('comment_list', ['as' => 'comments.comment_list', 'uses' => 'CommentsController@comment_list']);
        });

    });
});



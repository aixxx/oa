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
        $api->group(['prefix' => '/users'], function($api){
            //个人信息
            $api->post('/profile', ['as' => 'users.userFiles', 'uses' => 'UsersController@userFiles']);
            $api->post('/profileCreate', ['as' => 'userss.userFilesCreate', 'uses' => 'UsersController@userFilesCreate']);//userDetailInfo 添加与编辑
            $api->post('/urgent', ['as' => 'users.userUrgentEdit', 'uses' => 'UsersController@userUrgentEdit']);//紧急人添加与编辑
            $api->post('/family', ['as' => 'users.userFamilyEdit', 'uses' => 'UsersController@userFamilyEdit']);//家庭添加与编辑
            $api->post('/delete', ['as' => 'users.userDelete', 'uses' => 'UsersController@userDelete']);//删除家庭
            $api->post('/card', ['as' => 'users.userCard', 'uses' => 'UsersController@userCard']);//名片展示
            $api->post('/edit', ['as' => 'users.profileEdit', 'uses' => 'UsersController@profileEdit']);//名片修改
            $api->post('/basisUser', ['as' => 'users.basisUser', 'uses' => 'UsersController@basisUser']);//基础信息
            $api->post('/isNoPercent', ['as' => 'users.isNoPercent', 'uses' => 'UsersController@isNoPercent']);//完善资料
        });
    });
});

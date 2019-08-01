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
        $api->group(['prefix' => '/leaveout'], function($api){
            //获取外出字段和排班时间
            $api->post('/leaveout_field', ['as' => 'leaveout.leaveout_field', 'uses' => 'LeaveoutController@leaveout_field']);
            //写入外出申请
            $api->post('/create_leaveout', ['as' => 'leaveout.create_leaveout', 'uses' => 'LeaveoutController@create_leaveout']);
            //已提交待审核
            $api->post('/leaveout_check', ['as' => 'leaveout.leaveout_check', 'uses' => 'LeaveoutController@leaveout_check']);
            //申请人外出记录
            $api->post('/leaveout_list', ['as' => 'leaveout.leaveout_list', 'uses' => 'LeaveoutController@leaveout_list']);
            //外出申请审批
            $api->post('/leaveout_shenpi', ['as' => 'leaveout.leaveout_shenpi', 'uses' => 'LeaveoutController@leaveout_shenpi']);
            //撤销自己的外出
            $api->post('/revoke_leaveout', ['as' => 'leaveout.revoke_leaveout', 'uses' => 'LeaveoutController@revoke_leaveout']);
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
        $api->post('user/login', 'AuthController@authenticate');  //登录授权
    });
});



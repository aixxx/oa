<?php

/*
|--------------------------------------------------------------------------
| API Routes  , 'middleware' => 'auth:api'
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
  */


$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        $api->get('me', 'AuthController@me');
		$api->get('common', 'AuthController@index');
		//$api->post('send', 'PubilcController@send');
        $api->get('common/user', 'AuthController@getUser');
        $api->get('common/getDept', 'AuthController@getDept');
        $api->get('common/getDeptUsers', 'AuthController@getDeptUsers');
		$api->get('common/get_dept_leader', 'PubilcController@getDeptLeader');
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
        $api->post('user/login', 'AuthController@authenticate');  //登录授权
        $api->get('department/children_list', 'AuthController@fetchChildrenDepartmentsById');//获取某个部门的子部门列表
        $api->get('company/list', 'AuthController@fetchAllCompanies');//获取所有公司列表
        $api->get('performance/setBatchApply', 'PerformanceController@setBatchApply');//给所有人发绩效申请定时脚本
		$api->post('send', 'PubilcController@send');//入职发送验证
		
    });
});

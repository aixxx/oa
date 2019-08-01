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
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Financial",'middleware'=>'auth:api'], function ($api) {

        $api->group(['prefix' => '/bussiness_plan'], function($api){
            $api->post('/editBussinessPlan', ['uses'=>'BussinessPlanController@editBussinessPlan', 'as'=>'bussiness_plan.editBussinessPlan']);//添加修改计划
            $api->get('/planList', ['uses'=>'BussinessPlanController@planList', 'as'=>'bussiness_plan.planList']);//计划列表
            $api->get('/editPlanInfo', ['uses'=>'BussinessPlanController@editPlanInfo', 'as'=>'bussiness_plan.editPlanInfo']);//获取修改计划的详情
            $api->post('/editCategoryPlan', ['uses'=>'BussinessPlanController@editCategoryPlan', 'as'=>'bussiness_plan.editCategoryPlan']);//添加修改类目计划
            $api->get('/categoryPlanList', ['uses'=>'BussinessPlanController@categoryPlanList', 'as'=>'bussiness_plan.categoryPlanList']);//类目计划列表
            $api->get('/editCategoryPlanInfo', ['uses'=>'BussinessPlanController@editCategoryPlanInfo', 'as'=>'bussiness_plan.editCategoryPlanInfo']);//获取修改类目计划的详情
            $api->any('/planStatistics', ['uses'=>'BussinessPlanController@planStatistics', 'as'=>'bussiness_plan.planStatistics']);//经营计划统计
            $api->get('/getCategoryPlanList', ['uses'=>'BussinessPlanController@getCategoryPlanList', 'as'=>'bussiness_plan.getCategoryPlanList']);//获取修改类目计划的详情
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Financial"], function ($api) {
        //之后在这里写api

        $api->group(['prefix' => '/bussiness_plan'], function($api){

        });

    });
});
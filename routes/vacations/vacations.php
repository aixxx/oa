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

        $api->group(['prefix' => '/vacations'], function($api){

            $api->post('getmyleave', ['as' => 'vacations.getmyleaves', 'uses' => 'VacationsController@getmyleaves']);
            $api->post('gettembycomid', ['as' => 'vacations.gettem', 'uses' => 'VacationsController@gettem']);
            $api->post('getusertimes', ['as' => 'vacations.getutimes', 'uses' => 'VacationsController@getutimes']);
            $api->post('absence', ['as' => 'vacations.rabsence', 'uses' => 'VacationsController@rabsence']);
            $api->post('leastatuslist', ['as' => 'vacations.leavelist', 'uses' => 'VacationsController@leavelist']);

            $api->post('leaappoper', ['as' => 'vacations.leaappoperation', 'uses' => 'VacationsController@leaappoperation']);
            $api->post('revocation', ['as' => 'vacations.cxrevocation', 'uses' => 'VacationsController@cxrevocation']);
            $api->get('leave_management_list', ['as' => 'vacations.leave_management_list', 'uses' => 'VacationsController@leave_management_list']);
            $api->get('leavetypedetail', ['as' => 'vacations.leavetypedetail', 'uses' => 'VacationsController@leavetypedetail']);
            $api->post('addupleaverule', ['as' => 'vacations.addupleaverule', 'uses' => 'VacationsController@addupleaverule']);
            $api->get('detail_vacation', ['as' => 'vacations.detail', 'uses' => 'VacationsController@detailVacation']);
            $api->get('modify_vacation', ['as' => 'vacations.modify_vacation', 'uses' => 'VacationsController@modifyVacation']);
            $api->get('list_vacation', ['as' => 'vacations.list_vacation', 'uses' => 'VacationsController@listVacation']);
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
        $api->post('user/login', 'AuthController@authenticate');  //登录授权

        $api->group(['prefix' => '/vacations'], function($api){

            $api->post('adduservacations', ['as' => 'vacations.adduservaca', 'uses' => 'VacationsController@adduservaca']);
            $api->post('leave_detail', ['as' => 'vacations.leavedetail', 'uses' => 'VacationsController@leavedetail']);
        });

    });
});



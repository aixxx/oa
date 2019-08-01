<?php
//财务相关 路由

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api 
        $api->group(['prefix' => '/welfare'], function ($api) {
			$api->get('/list', 'WelfareController@index')->name('welfare.list');//福利审核列表-创建人
			$api->get('/person_list', 'WelfareController@personList')->name('welfare.person_list');//福利审核列表-获取福利领取资格的人
			$api->get('/create', 'WelfareController@create')->name('welfare.create');//福利添加
			$api->post('/save', 'WelfareController@save')->name('welfare.create');//福利保存
			$api->get('/show', 'WelfareController@show')->name('welfare.show');//福利查看-科长发起人
			$api->get('/person_show', 'WelfareController@personShow')->name('welfare.person_show');//福利查看-获取福利领取资格的人
			$api->get('/receiver', 'WelfareController@receiverList')->name('welfare.receiver');//领取人列表
			$api->post('/apply', 'WelfareController@apply')->name('welfare.apply');//福利申请
			$api->post('/check', 'WelfareController@check')->name('welfare.check');//福利领取人审核通过或拒绝
			
           

            //审批人myAudited
			$api->get('/auditor_show', 'WelfareController@showAuditor')->name('welfare.auditor_show');//福利查看-审批人
			$api->post('/pass', 'WelfareController@pass')->name('welfare.pass');//福利审批通过
			$api->post('/reject', 'WelfareController@reject')->name('welfare.reject');//福利审批拒绝

        });
    });
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之后在这里写api
        
    });

});

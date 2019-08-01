<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //投票
        $api->group(['prefix' => '/salary'], function($api){
            $api->get('/',['as'=>'salary.index','uses'=> 'SalaryController@index']);
            $api->any('/templatecreate',['as'=>'salary.templatecreate','uses'=> 'SalaryController@templatecreate']);
            $api->any('/templateedit',['as'=>'salary.templateedit','uses'=> 'SalaryController@templateedit']);
            $api->any('/templatelists',['as'=>'salary.templatelists','uses'=> 'SalaryController@templatelists']);
            $api->any('/templateget',['as'=>'salary.templateget','uses'=> 'SalaryController@templateget']);
            $api->any('/adddata',['as'=>'salary.adddata','uses'=> 'SalaryController@adddata']);
            $api->any('/getsalaryfield',['as'=>'salary.getsalaryfield','uses'=> 'SalaryController@getsalaryfield']);
            $api->any('/calculatingsalary',['as'=>'salary.calculatingsalary','uses'=> 'SalaryController@calculatingsalary']);
            $api->any('/getuserssalary',['as'=>'salary.getuserssalary','uses'=> 'SalaryController@getuserssalary']);
        });
    });


    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
    });
});

<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //投票
        $api->group(['prefix' => '/socialsecurity'], function($api){
            $api->get('/',['as'=>'socialsecurity.index','uses'=> 'SocialSecurityController@index']);
            $api->any('/create',['as'=>'socialsecurity.create','uses'=> 'SocialSecurityController@create']);
            $api->any('/delete',['as'=>'socialsecurity.delete','uses'=> 'SocialSecurityController@delete']);
            $api->any('/showusersocialsecurity',['as'=>'socialsecurity.showusersocialsecurity','uses'=> 'SocialSecurityController@showusersocialsecurity']);
            $api->any('/createparticipant',['as'=>'socialsecurity.createparticipant','uses'=> 'SocialSecurityController@createparticipant']);
            $api->any('/getUserSocialSecurity',['as'=>'socialsecurity.getUserSocialSecurity','uses'=> 'SocialSecurityController@getUserSocialSecurity']);
        });
    });


    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
    });
});

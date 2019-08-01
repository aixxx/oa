<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
        //日程 start
        $api->group(['middleware' => 'jwt.auth', 'prefix' => '/schedules'], function ($api) {
            $api->get('/', ['as' => 'schedules.index', 'uses' => 'SchedulesController@fetchSchedules']);
            $api->get('/user_schedules', ['as' => 'schedules.user_schedules', 'uses' => 'SchedulesController@fetchUserSchedules']);
            $api->get('/show',['as'=>'schedules.show','uses'=> 'SchedulesController@show']);
            $api->post('/create', ['as' => 'schedules.create', 'uses' => 'SchedulesController@create']);
            $api->post('/edit', ['as' => 'schedules.edit', 'uses' => 'SchedulesController@edit']);
            $api->post('/confirm', ['as' => 'schedules.confirm', 'uses' => 'SchedulesController@confirm']);
            $api->get('/confirm_status',['as'=>'schedules.fetch_confirm_status','uses'=> 'SchedulesController@fetchConfirmStatus']);
            $api->get('/set_prompt_type',['as'=>'schedules.setUserSchedulePromptType','uses'=> 'SchedulesController@setUserSchedulePromptType']);
            //$api->post('/delete',['as'=>'schedules.delete','uses'=> 'SchedulesController@delete']);
        });
    });
});

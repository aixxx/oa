<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Salary",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        $api->post('salary-reward-punishment','RewardPunishmentController@add');
        $api->get('salary-reward-punishment','RewardPunishmentController@getList');
        $api->get('salary-reward-punishment-info','RewardPunishmentController@getInfo');
        $api->get('salary-reward-punishment-my-info','RewardPunishmentController@getMyInfo');
        $api->get('salary-reward-punishment-delete','RewardPunishmentController@delete');

        $api->post('salary-punishment-by-task', 'RewardPunishmentController@addByTask');

        $api->post('salary-reward-punishment-appeal','RewardPunishmentController@RewardPunishmentAppeal');
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

    });
});

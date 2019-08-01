<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\FlowCustomize",'middleware'=>'auth:api'], function ($api) {
        //我的待办
        $api->get('api-flow-customize', ['as' => 'api.flow.customize', 'uses' => 'ApiController@apiFlowCustomize']);
        //待我审批
        $api->get('api-flow-customize-auditor-flow-show', ['as'=>'api.flow.customize.auditor.flow.show','uses' => 'ApiController@apiFlowCustomizeAuditorFlowShow']);

    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

    });
});

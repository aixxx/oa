<?php
/** @var \Dingo\Api\Routing\Route $api */
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    /** @var Route $api */
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        /** @var Route $api */
        //投票
        $api->group(
            [
                'prefix' => '/message',
                'namespace'=> 'Message',
//                'middleware'=>['']
            ], function($api){
            /** @var Route $api */
            $api->get('/receive_list', "ReceiveListController@index")->name('message.receive_list');
            $api->get('/send_list', "SendListController@index")->name('message.send_list');
            $api->get('/detail', "DetailController@index")->name('message.detail');
            $api->get('/delete', "DeleteController@index")->name('message.delete');
            $api->post('/send', "SendController@index")->name('message.send');
            $api->get('/list', "ListController@index")->name('message.list');
            $api->get('/sys_list', "SysListController@index")->name('message.sys_list');
            $api->get('/cnt', "MineUnReadListController@index")->name('message.cnt');
        });
    });



});

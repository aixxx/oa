<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>['auth:api','total:api']], function ($api) {
        //投票
        $api->group(['prefix' => '/vote'], function($api){
            $api->get('/',['as'=>'vote.index','uses'=> 'VoteController@index']);
            $api->any('/deletevote',['as'=>'vote.deletevote','uses'=> 'VoteController@deletevote']);
            $api->any('/createoreditvote',['as'=>'vote.createoreditvote','uses'=> 'VoteController@createoreditvote']);
            $api->any('/votingoperation',['as'=>'vote.votingoperation','uses'=> 'VoteController@votingoperation']);
            $api->any('/getvotelist',['as'=>'vote.getvotelist','uses'=> 'VoteController@getvotelist']);
            $api->any('/show',['as'=>'vote.show','uses'=> 'VoteController@show']);
            $api->any('/voteconfirm',['as'=>'vote.voteconfirm','uses'=> 'VoteController@voteconfirm']);
            $api->any('/voteoption',['as'=>'vote.voteoption','uses'=> 'VoteController@voteoption']);
        });
    });


    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
        $api->group(['prefix' => '/vote'], function($api){
            $api->any('/voteinitialise',['as'=>'vote.voteinitialise','uses'=> 'VoteController@voteinitialise']);
        });
    });
});

<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Assets", 'middleware' => 'auth:api'], function ($api) {
        //公司资产操作
        $api->group(['prefix' => '/corporateassets'], function ($api) {
            $api->get('/', ['as' => 'corporateassets.index', 'uses' => 'CorporateAssetsController@index']);
            $api->any('/create', ['as' => 'corporateassets.create', 'uses' => 'CorporateAssetsController@create']);
            $api->any('/list', ['as' => 'corporateassets.lists', 'uses' => 'CorporateAssetsController@lists']);
            $api->any('/show', ['as' => 'corporateassets.show', 'uses' => 'CorporateAssetsController@show']);
            $api->any('/management', ['as' => 'corporateassets.management', 'uses' => 'CorporateAssetsController@management']);
            $api->any('/processquery', ['as' => 'corporateassets.processquery', 'uses' => 'CorporateAssetsController@processquery']);
            $api->any('/getnum', ['as' => 'corporateassets.getnum', 'uses' => 'CorporateAssetsController@getnum']);
            $api->any('/report', ['as' => 'corporateassets.report', 'uses' => 'CorporateAssetsController@report']);
        });
        /**
         * 公司资产行为
         */
        //公司资产领用
        $api->group(['prefix' => '/corporateassetsuse'], function ($api) {
            $api->get('/', ['as' => 'corporateassetsuse.index', 'uses' => 'CorporateAssetsController@index']);
            $api->any('/show_form', ['as' => 'corporateassetsuse.show_form', 'uses' => 'CorporateAssetsUseController@showform']);
            $api->any('/create', ['as' => 'corporateassetsuse.create', 'uses' => 'CorporateAssetsUseController@create']);
            $api->any('/reject', ['as' => 'corporateassetsuse.reject', 'uses' => 'CorporateAssetsUseController@rejectWorkflow']);
            $api->any('/pass', ['as' => 'corporateassetsuse.pass', 'uses' => 'CorporateAssetsUseController@passWorkflow']);
        });
        //公司资产借用
        $api->group(['prefix' => '/corporateassetsborrow'], function ($api) {
            $api->get('/', ['as' => 'corporateassetsborrow.index', 'uses' => 'CorporateAssetsBorrowController@index']);
            $api->any('/show_form', ['as' => 'corporateassetsborrow.show_form', 'uses' => 'CorporateAssetsBorrowController@showform']);
            $api->any('/create', ['as' => 'corporateassetsborrow.create', 'uses' => 'CorporateAssetsBorrowController@create']);
            $api->any('/test', ['as' => 'corporateassetsborrow.test', 'uses' => 'CorporateAssetsBorrowController@test']);
            $api->any('/reject', ['as' => 'corporateassetsborrow.reject', 'uses' => 'CorporateAssetsBorrowController@rejectWorkflow']);
        });
        //公司资产归还
        $api->group(['prefix' => '/corporateassetsreturn'], function ($api) {
            $api->get('/', ['as' => 'corporateassetsreturn.index', 'uses' => 'CorporateAssetsReturnController@index']);
            $api->any('/show_form', ['as' => 'corporateassetsreturn.show_form', 'uses' => 'CorporateAssetsReturnController@showform']);
            $api->any('/create', ['as' => 'corporateassetsreturn.create', 'uses' => 'CorporateAssetsReturnController@create']);
        });
        //公司资产调拨
        $api->group(['prefix' => '/corporateassetstransfer'], function ($api) {
            $api->get('/', ['as' => 'corporateassetstransfer.index', 'uses' => 'CorporateAssetsTransferController@index']);
            $api->any('/show_form', ['as' => 'corporateassetstransfer.show_form', 'uses' => 'CorporateAssetsTransferController@showform']);
            $api->any('/create', ['as' => 'corporateassetstransfer.create', 'uses' => 'CorporateAssetsTransferController@create']);
            $api->any('/reject', ['as' => 'corporateassetstransfer.reject', 'uses' => 'CorporateAssetsTransferController@rejectWorkflow']);
            $api->any('/pass', ['as' => 'corporateassetstransfer.pass', 'uses' => 'CorporateAssetsTransferController@passWorkflow']);
        });
        //公司资产送修
        $api->group(['prefix' => '/corporateassetsrepair'], function ($api) {
            $api->get('/', ['as' => 'corporateassetsrepair.index', 'uses' => 'CorporateAssetsRepairController@index']);
            $api->any('/show_form', ['as' => 'corporateassetsrepair.show_form', 'uses' => 'CorporateAssetsRepairController@showform']);
            $api->any('/create', ['as' => 'corporateassetsrepair.create', 'uses' => 'CorporateAssetsRepairController@create']);
            $api->any('/completed', ['as' => 'corporateassetsrepair.completed', 'uses' => 'CorporateAssetsRepairController@completed']);
        });
        //公司资产报废
        $api->group(['prefix' => '/corporateassetsscrapped'], function ($api) {
            $api->get('/', ['as' => 'corporateassetsscrapped.index', 'uses' => 'CorporateAssetsScrappedController@index']);
            $api->any('/show_form', ['as' => 'corporateassetsscrapped.show_form', 'uses' => 'CorporateAssetsScrappedController@showform']);
            $api->any('/create', ['as' => 'corporateassetsscrapped.create', 'uses' => 'CorporateAssetsScrappedController@create']);
        });
        //公司资产增值
        $api->group(['prefix' => '/corporateassetsvalueadded'], function ($api) {
            $api->get('/', ['as' => 'corporateassetsvalueadded.index', 'uses' => 'CorporateAssetsValueaddedController@index']);
            $api->any('/show_form', ['as' => 'corporateassetsvalueadded.show_form', 'uses' => 'CorporateAssetsValueaddedController@showform']);
            $api->any('/create', ['as' => 'corporateassetsvalueadded.create', 'uses' => 'CorporateAssetsValueaddedController@create']);
        });
        //公司资产折旧
        $api->group(['prefix' => '/corporateassetsdepreciation'], function ($api) {
            $api->get('/', ['as' => 'corporateassetsdepreciation.index', 'uses' => 'CorporateAssetsDepreciationController@index']);
            $api->any('/show_form', ['as' => 'corporateassetsdepreciation.show_form', 'uses' => 'CorporateAssetsDepreciationController@showform']);
            $api->any('/create', ['as' => 'corporateassetsdepreciation.create', 'uses' => 'CorporateAssetsDepreciationController@create']);
        });
    });


    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        //之后在这里写api
    });
});

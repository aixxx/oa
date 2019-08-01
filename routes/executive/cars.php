<?php

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Executive",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        $api->post('executive-cars','CarsController@add')->name('executive.cars.add');
        $api->get('executive-cars-delete','CarsController@delete')->name('executive.cars.delete');
        $api->get('executive-cars','CarsController@getList')->name('executive.cars');

        $api->post('executive-cars-record','CarsRecordController@add')->name('executive.cars.record.add');
        $api->get('executive-cars-record','CarsRecordController@getList')->name('executive.cars.record');

        $api->post('executive-cars-use','CarsUseController@add')->name('executive.cars.use.add');
        $api->get('executive-cars-use-delete','CarsUseController@delete')->name('executive.cars.use.delete');
        $api->get('executive-cars-use','CarsUseController@getList')->name('executive.cars.use');


        $api->post('executive-cars-appoint','CarsAppointController@add')->name('executive.cars.appoint.add');
        $api->get('executive-cars-appoint','CarsAppointController@getList')->name('executive.cars.appoint');
        $api->get('executive-cars-appoint-option','CarsAppointController@option')->name('executive.cars.appoint.option');


        $api->post('executive-cars-sendback','CarsSendbackController@add')->name('executive.cars.sendback');
        $api->get('executive-cars-sendback','CarsSendbackController@getList')->name('executive.cars.sendback');

    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

    });
});

<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\PAS\Warehouse",'middleware'=>'auth:api'], function ($api) {
        //供应商
        $api->group(['prefix' => '/warehouse/warehouse', 'namespace' => 'Warehouse'], function($api){
            $api->get('/list', ['as' => 'warehouse.warehouse.list', 'uses' => 'ListController@index']);//仓库列表
            $api->post('/save', ['as' => 'warehouse.warehouse.save', 'uses' => 'SaveController@index']);//保存仓库
            $api->get('/detail', ['as' => 'warehouse.warehouse.detail', 'uses' => 'DetailController@index']);//仓库详情信息
        });

        $api->group(['prefix' => '/warehouse/allocation', 'namespace' => 'Allocation'], function($api){
            $api->get('/list', ['as' => 'warehouse.allocation.list', 'uses' => 'ListController@index']);//货位列表
        });
        $api->group(['prefix' => '/warehouse/plan', 'namespace' => 'Plan'], function($api){
            $api->post('/save', ['as' => 'warehouse.plan.save', 'uses' => 'SaveController@index']);//货位列表
            $api->get('/list', ['as' => 'warehouse.plan.list', 'uses' => 'ListController@index']);//入库记录列表
            $api->get('/detail', ['as' => 'warehouse.plan.detail', 'uses' => 'DetailController@index']);//入库记录详情
            $api->get('/create', ['as' => 'warehouse.plan.create', 'uses' => 'CreateController@index']);//入库记录详情
            $api->get('/apply_list', ['as' => 'warehouse.plan.apply_list', 'uses' => 'ApplyListController@index']);//入库申请列表
            $api->post('/enter', ['as' => 'warehouse.plan.enter', 'uses' => 'EnterController@index']);//入库申请列表
            $api->get('/allocation_list', ['as' => 'warehouse.plan.allocation_list', 'uses' => 'AllocationListController@index']);//入库申请列表
        });
        $api->group(['prefix' => '/warehouse/allot', 'namespace' => 'Allot'], function($api){
            $api->post('/save', ['as' => 'warehouse.allot.save', 'uses' => 'SaveController@index']);//调拨单保存
            $api->get('/list', ['as' => 'warehouse.allot.list', 'uses' => 'ListController@index']);//调拨单列表
            $api->get('/detail', ['as' => 'warehouse.allot.detail', 'uses' => 'DetailController@index']);//调拨单详情
        });
        $api->group(['prefix' => '/warehouse/delivery_type', 'namespace' => 'DeliveryType'], function($api){
            $api->post('/save', ['as' => 'warehouse.delivery_type.save', 'uses' => 'SaveController@index']);//保存发货方式
            $api->post('/update', ['as' => 'warehouse.delivery_type.update', 'uses' => 'UpdateController@index']);//发货方式修改
            $api->get('/detail', ['as' => 'warehouse.delivery_type.detail', 'uses' => 'DetailController@index']);//发货方式详情
            $api->get('/point_list', ['as' => 'warehouse.delivery_type.point_list', 'uses' => 'PointListController@index']);//网店列表
            $api->post('/point_save', ['as' => 'warehouse.delivery_type.point_save', 'uses' => 'PointSaveController@index']);//保存网店
            $api->post('/logistics_save', ['as' => 'warehouse.delivery_type.logistics_save', 'uses' => 'LogisticsSaveController@index']);//保存物流
            $api->get('/logistics_list', ['as' => 'warehouse.delivery_type.logistics_list', 'uses' => 'LogisticsListController@index']);//物流列表
        });
        $api->group(['prefix' => '/warehouse/out_card', 'namespace' => 'OutCard'], function($api){
            $api->post('/save', ['as' => 'warehouse.out_card.save', 'uses' => 'SaveController@index']);// 保存出库单
            $api->get('/list', ['as' => 'warehouse.out_card.point_list', 'uses' => 'ListController@index']);//出库批次列表
            $api->get('/apply_list', ['as' => 'warehouse.out_card.apply_list', 'uses' => 'ApplyListController@index']);//出库批次列表
            $api->get('/detail', ['as' => 'warehouse.out_card.detail', 'uses' => 'DetailController@index']);//出库记录详情
            $api->get('/create', ['as' => 'warehouse.out_card.create', 'uses' => 'CreateController@index']);//出库记录详情
            $api->get('/cancel', ['as' => 'warehouse.out_card.cancel', 'uses' => 'CancelController@index']);//撤销出库记录
        });
        $api->group(['prefix' => '/warehouse/stock_check', 'namespace' => 'StockCheck'], function($api){
            $api->post('/save', ['as' => 'warehouse.stock_check.save', 'uses' => 'SaveController@index']);//添加盘点
            $api->get('/list', ['as' => 'warehouse.stock_check.list', 'uses' => 'ListController@index']);//盘点列表
            $api->get('/detail', ['as' => 'warehouse.stock_check.detail', 'uses' => 'DetailController@index']);//盘点详情
        });
        $api->group(['prefix' => '/warehouse/goods', 'namespace' => 'Goods'], function($api){
            $api->get('/list', ['as' => 'warehouse.goods.list', 'uses' => 'ListController@index']);//商品列表
            $api->get('/flow', ['as' => 'warehouse.goods.flow', 'uses' => 'FlowController@index']);//商品流水
            $api->get('/analyze', ['as' => 'warehouse.goods.analyze', 'uses' => 'AnalyzeController@index']);//商品列表
        });



    });
});

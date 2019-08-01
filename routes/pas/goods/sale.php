<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 16:15
 * desc: 工作汇报
 */

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\PAS",'middleware'=>'auth:api'], function ($api) {
        $api->group(['prefix' => '/sale'], function($api){
            $api->post('/editSaleInvoice', ['uses'=>'SaleOrderController@editSaleInvoice', 'as'=>'saleOrder.editSaleInvoice']);//添加修改销售发货单
            $api->post('/saleInvoiceInfo', ['uses'=>'SaleOrderController@saleInvoiceInfo', 'as'=>'saleOrder.saleInvoiceInfo']);//销售发货单详情
            $api->post('/editSaleOrder', ['uses'=>'SaleOrderController@editSaleOrder', 'as'=>'saleOrder.editSaleOrder']);//添加修改销售单
            $api->post('/saleOrderList', ['uses'=>'SaleOrderController@saleOrderList', 'as'=>'saleOrder.saleOrderList']);//销售单列表
            $api->post('/changeBuyGoods', ['uses'=>'SaleOrderController@changeBuyGoods', 'as'=>'saleOrder.changeBuyGoods']);//修改销售单购买的商品
            $api->get('/saleOrderInfo', ['uses'=>'SaleOrderController@saleOrderInfo', 'as'=>'saleOrder.saleOrderInfo']);//销售单详情
            $api->get('/cancelSaleOrder', ['uses'=>'SaleOrderController@cancelSaleOrder', 'as'=>'saleOrder.cancelSaleOrder']);//撤销销售单
            $api->post('/saleOutOfWarehouse', ['uses'=>'SaleOrderController@saleOutOfWarehouse', 'as'=>'saleOrder.saleOutOfWarehouse']);//销售单申请出库
            $api->get('/saleOutWarehouseInfo', ['uses'=>'SaleOrderController@saleOutWarehouseInfo', 'as'=>'saleOrder.saleOutWarehouseInfo']);//出库申请单详情
            $api->post('/cancelSaleOutWarehouse', ['uses'=>'SaleOrderController@cancelSaleOutWarehouse', 'as'=>'saleOrder.cancelSaleOutWarehouse']);//撤销出库申请单
            $api->get('/saleOutList', ['uses'=>'SaleOrderController@saleOutList', 'as'=>'saleOrder.saleOutList']);//出库单列表
            $api->post('/editReturnOrder', ['uses'=>'SaleOrderController@editReturnOrder', 'as'=>'saleOrder.editReturnOrder']);//添加修改退货单
            $api->get('/saleReturnOrderDetail', ['uses'=>'SaleOrderController@saleReturnOrderDetail', 'as'=>'saleOrder.saleReturnOrderDetail']);//退货单详情
            $api->post('/cancelSaleReturnOrder', ['uses'=>'SaleOrderController@cancelSaleReturnOrder', 'as'=>'saleOrder.cancelSaleReturnOrder']);//撤销退货单
            $api->post('/saleReturnOrderList', ['uses'=>'SaleOrderController@saleReturnOrderList', 'as'=>'saleOrder.saleReturnOrderList']);//退货单列表
            $api->post('/returnInWarehouse', ['uses'=>'SaleOrderController@returnInWarehouse', 'as'=>'saleOrder.returnInWarehouse']);//退货单入库申请
            $api->post('/cancelSaleInWarehouse', ['uses'=>'SaleOrderController@cancelSaleInWarehouse', 'as'=>'saleOrder.cancelSaleInWarehouse']);//撤销退货单入库申请
            $api->post('/returnInWarehouseDetail', ['uses'=>'SaleOrderController@returnInWarehouseDetail', 'as'=>'saleOrder.returnInWarehouseDetail']);//退货入库单详情
            $api->get('/saleReturnInList', ['uses'=>'SaleOrderController@saleReturnInList', 'as'=>'saleOrder.saleReturnInList']);//退货入库列表
//$api->get('/editSaleReturnOrderInDetail', ['uses'=>'SaleOrderController@editSaleReturnOrderInDetail', 'as'=>'saleOrder.editSaleInvoice']);//修改销售退货入库单详情
            $api->get('/getUserAddress', ['uses'=>'SaleOrderController@getUserAddress', 'as'=>'saleOrder.getUserAddress']);//获取用户的发货地址信息
            $api->get('/getCustomerDetail', ['uses'=>'SaleOrderController@getCustomerDetail', 'as'=>'saleOrder.getCustomerDetail']);//客户详情


        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1\PAS"], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/sale'], function($api){
            $api->get('/getCustomerList', ['uses'=>'SaleOrderController@getCustomerList', 'as'=>'saleOrder.getCustomerList']);//客户列表
        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Rpc"], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/sale'], function($api){
            $api->get('/getUserOrders', 'OrderController@getUserOrders');//获取用户的发货地址信息
        });
    });

});
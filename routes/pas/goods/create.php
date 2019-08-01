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
        $api->group(['prefix' => '/goods'], function($api){
            $api->post('/editCategory', ['uses'=>'GoodsController@editCategory', 'as'=>'goods.editCategory']);//添加修改类目
            $api->post('/createSpecific', ['uses'=>'GoodsController@createSpecific', 'as'=>'goods.createSpecific']);//添加规格
            $api->post('/editSpecific', ['uses'=>'GoodsController@editSpecific', 'as'=>'goods.editSpecific']);//修改规格
            $api->post('/delSpecificItem', ['uses'=>'GoodsController@delSpecificItem', 'as'=>'goods.delSpecificItem']);//删除规格选项
            $api->get('/specificChildren', ['uses'=>'GoodsController@specificChildren', 'as'=>'goods.specificChildren']);//规格及选项
            $api->post('/delSpecific', ['uses'=>'GoodsController@delSpecific', 'as'=>'goods.delSpecific']);//删除规格
            $api->post('/addSkuPrice', ['uses'=>'GoodsController@addSkuPrice', 'as'=>'goods.addSkuPrice']);//添加sku定价
            $api->post('/delSkuPrice', ['uses'=>'GoodsController@delSkuPrice', 'as'=>'goods.delSkuPrice']);//删除sku定价
            $api->post('/addSkuStoreEarlyWarning', ['uses'=>'GoodsController@addSkuStoreEarlyWarning', 'as'=>'goods.addSkuStoreEarlyWarning']);//添加规格库存预警
            $api->post('/delSkuStoreEarlyWarning', ['uses'=>'GoodsController@delSkuStoreEarlyWarning', 'as'=>'goods.delSkuStoreEarlyWarning']);//删除规格库存预警
            $api->post('/editAttribute', ['uses'=>'GoodsController@editAttribute', 'as'=>'goods.editAttribute']);//添加修改属性
            $api->post('/delAttribute', ['uses'=>'GoodsController@delAttribute', 'as'=>'goods.delAttribute']);//删除属性
            $api->post('/editBrand', ['uses'=>'GoodsController@editBrand', 'as'=>'goods.editBrand']);//添加修改品牌
            $api->post('/delBrand', ['uses'=>'GoodsController@delBrand', 'as'=>'goods.delBrand']);//删除品牌
            $api->post('/brandList', ['uses'=>'GoodsController@brandList', 'as'=>'goods.brandList']);//品牌列表
            $api->post('/editGoods', ['uses'=>'GoodsController@editGoods', 'as'=>'goods.editGoods']);//添加修改商品
            $api->get('/editGoodsInfo', ['uses'=>'GoodsController@editGoodsInfo', 'as'=>'goods.editGoodsInfo']);//商品信息
            $api->post('/goodsList', ['uses'=>'GoodsController@goodsList', 'as'=>'goods.goodsList']);//商品列表
            $api->get('/goodsBuyDetail', ['uses'=>'GoodsController@goodsBuyDetail', 'as'=>'goods.goodsBuyDetail']);//商品购买详情
            $api->post('/changeSpecificItem', ['uses'=>'GoodsController@changeSpecificItem', 'as'=>'goods.changeSpecificItem']);//规格切换获取规格组合信息
            $api->post('/selectRelateSpecific', ['uses'=>'GoodsController@selectRelateSpecific', 'as'=>'goods.selectRelateSpecific']);//选规格获取关联规格
            $api->post('/userBuyGoodsList', ['uses'=>'GoodsController@userBuyGoodsList', 'as'=>'goods.userBuyGoodsList']);//指定用户购买的商品列表

        });
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1\PAS"], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/goods'], function($api){
            $api->get('/categoryList', ['uses'=>'GoodsController@categoryList', 'as'=>'goods.categoryList']);//下级类目列表
            $api->get('/getCategoryList', ['uses'=>'GoodsController@getCategoryList', 'as'=>'goods.getCategoryList']);//多级类目
            $api->get('/goodsStatistics', ['uses'=>'GoodsController@goodsStatistics', 'as'=>'goods.goodsStatistics']);//商品统计

        });
    });


    $api->group(["namespace" => "App\Listeners"], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/sale'], function($api){
            $api->get('/hhh', 'ReturnSaleOrderPassListener@handle');

        });
    });


    $api->group(["namespace" => "App\Http\Controllers\Rpc"], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/order'], function($api){
            $api->get('/hhh', 'OrderController@getUserOrders');

            $api->get('/hhhh', 'GoodsController@getCustomerBuyGoods');


            $api->get('/hh', 'GoodsController@getSupplierGoods');
        });

    });
});
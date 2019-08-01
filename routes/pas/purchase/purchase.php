<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\PAS",'middleware'=>'auth:api'], function ($api) {
        //供应商
        $api->group(['prefix' => '/supplier'], function($api){
            $api->post('/setadd', ['as' => 'supplier.setadd', 'uses' => 'SupplierController@setAdd']);//添加供应商
            $api->post('/setupdate', ['as' => 'supplier.setupdate', 'uses' => 'SupplierController@setUpdate']);//添加供应商
            $api->get('/getcode', ['as' => 'supplier.getcode', 'uses' => 'SupplierController@getCode']);//获取供应商编号
            $api->get('/getinfo', ['as' => 'supplier.getinfo', 'uses' => 'SupplierController@getInfo']);//获取供应商详情
            $api->get('/getlistone', ['as' => 'supplier.getlistone', 'uses' => 'SupplierController@getListOne']);//获取供应商详情
            $api->get('/getlist', ['as' => 'supplier.getlist', 'uses' => 'SupplierController@getList']);//获取供应商列表
            $api->get('/chtowarr', ['as' => 'supplier.chtowarr', 'uses' => 'SupplierController@chTowarr']);//获取中文字符的首字母
            $api->get('/statistical', ['as' => 'supplier.statistical', 'uses' => 'SupplierController@Statistical']);//供应商对账统计
            $api->get('/procurementStatistics', ['as' => 'supplier.procurementStatistics', 'uses' => 'SupplierController@ProcurementStatistics']);//采购统计
        });

        //入库申请单
        $api->group(['prefix' => '/warehousingapply'], function($api){
            $api->post('/setadd', ['as' => 'warehousingapply.setadd', 'uses' => 'WarehousingApplyController@setAdd']);//添加入库申请单
            $api->post('/setupdate', ['as' => 'warehousingapply.setupdate', 'uses' => 'WarehousingApplyController@setUpdate']);//修改入库申请单
            $api->get('/getinfone', ['as' => 'warehousingapply.getinfone', 'uses' => 'WarehousingApplyController@getInfo']);//入库申请单详情
            $api->get('/getcode', ['as' => 'warehousingapply.getcode', 'uses' => 'WarehousingApplyController@getCode']);//获取入库单号
            $api->get('/getlist', ['as' => 'warehousingapply.getlist', 'uses' => 'WarehousingApplyController@getList']);//入库申请单列表
            $api->get('/getrelationinfo', ['as' => 'warehousingapply.getRelationInfo', 'uses' => 'WarehousingApplyController@getRelationInfo']);//查询入库单跟采购单关联页面的详情数据
            $api->get('/geterr', ['as' => 'warehousingapply.geterr', 'uses' => 'WarehousingApplyController@getErr']);
            $api->get('/setInvoiceAdd', ['as' => 'warehousingapply.setInvoiceAdd', 'uses' => 'WarehousingApplyController@setInvoiceAdd']);//添加发货单
        });
        //退货申请单
        $api->group(['prefix' => '/returnOrder'], function($api){
            $api->post('/setadd', ['as' => 'returnOrder.setadd', 'uses' => 'ReturnOrderController@setAdd']);//添加入库申请单
            $api->post('/setupdate', ['as' => 'returnOrder.setupdate', 'uses' => 'ReturnOrderController@setUpdate']);//修改入库申请单
            $api->get('/getinfo', ['as' => 'returnOrder.getinfo', 'uses' => 'ReturnOrderController@getInfo']);//退货单详情
            $api->get('/getcode', ['as' => 'returnOrder.getcode', 'uses' => 'ReturnOrderController@getCode']);//获取入库单号
            $api->get('/getweList', ['as' => 'returnOrder.getweList', 'uses' => 'ReturnOrderController@getWeList']);//我的退货单列表
            $api->get('/getweinfo', ['as' => 'returnOrder.getweinfo', 'uses' => 'ReturnOrderController@getInfoOne']);//代办中我的退货单详情
            $api->get('/getinfotow', ['as' => 'returnOrder.getinfotow', 'uses' => 'ReturnOrderController@getInfoTow']);//代办中我审核的退货单详情
            $api->get('/delcost', ['as' => 'returnOrder.delcost', 'uses' => 'ReturnOrderController@delCost']);//删除退货单中的费用信息数据
            $api->get('/withdraw', ['as' => 'returnOrder.withdraw', 'uses' => 'ReturnOrderController@withdraw']);//退货单撤回
            $api->get('/getrelationinfo', ['as' => 'returnOrder.getRelationInfo', 'uses' => 'ReturnOrderController@getRelationInfo']);//退货单跟采购单关联页面的数据
        });

        //付款申请单申请单
        $api->group(['prefix' => '/payment'], function($api){
            $api->post('/setadd', ['as' => 'payment.setadd', 'uses' => 'PaymentOrderController@setAdd']);//添加付款申请单
            $api->post('/setupdate', ['as' => 'payment.setupdate', 'uses' => 'PaymentOrderController@setUpdate']);//修改付款申请单
            $api->get('/getinfo', ['as' => 'payment.getinfo', 'uses' => 'PaymentOrderController@getInfo']);//付款单详情
            $api->get('/getcode', ['as' => 'payment.getcode', 'uses' => 'PaymentOrderController@getCode']);//获取付款单号
            $api->get('/getweList', ['as' => 'payment.getweList', 'uses' => 'PaymentOrderController@getWeList']);//我的付款单列表
            $api->get('/getweinfo', ['as' => 'payment.getweinfo', 'uses' => 'PaymentOrderController@getInfoOne']);//代办中我申请的付款单详情
            $api->get('/getinfotow', ['as' => 'payment.getinfotow', 'uses' => 'PaymentOrderController@getInfoTow']);//代办中我审核的付款单详情
            $api->get('/withdraw', ['as' => 'payment.withdraw', 'uses' => 'PaymentOrderController@withdraw']);//付款单撤回
            $api->get('/getrelationinfo', ['as' => 'payment.getRelationInfo', 'uses' => 'PaymentOrderController@getRelationInfo']);//查询付款单单跟采购单退货单关联页面的数据


            $api->post('/setaddone', ['as' => 'payment.setaddone', 'uses' => 'PaymentOrderController@setAddOne']);//添加付款申请单
            $api->get('/getoneinfo', ['as' => 'payment.getoneinfo', 'uses' => 'PaymentOrderController@getOneInfo']);//付款单详情
            $api->get('/getpassinfone', ['as' => 'payment.getpassinfone', 'uses' => 'PaymentOrderController@getPassInfoOne']);//我申请的付款单详情
            $api->get('/getpassinfotow', ['as' => 'payment.getpassinfotow', 'uses' => 'PaymentOrderController@getPassInfoTow']);//我审核的付款单详情
            $api->get('/getorder', ['as' => 'payment.getorder', 'uses' => 'PaymentOrderController@getOrder']);//获取供应商 下面的所有采购单跟退货单
            $api->get('/withdrawone', ['as' => 'payment.withdrawone', 'uses' => 'PaymentOrderController@withdrawOne']);//付款单撤回
        });

        //采购单
        $api->group(['prefix' => '/purchase'], function($api){
            $api->post('/setadd', ['as' => 'purchase.setadd', 'uses' => 'PurchaseController@setAdd']);//添加采购申请单
            $api->post('/setupdate', ['as' => 'purchase.setupdate', 'uses' => 'PurchaseController@setUpdate']);//修改采购申请单
            $api->get('/getcode', ['as' => 'purchase.getcode', 'uses' => 'PurchaseController@getCode']);//获取采购申请单编号
            $api->get('/getPurchaseInfo', ['as' => 'purchase.getPurchaseInfo', 'uses' => 'PurchaseController@getPurchaseInfo']);//获取采购申请单详情
            $api->get('/getwelist', ['as' => 'purchase.getwelist', 'uses' => 'PurchaseController@getWeList']);//我申请的采购单列表
            $api->get('/delcost', ['as' => 'purchase.delcost', 'uses' => 'PurchaseController@delCost']);//删除采购单中的费用信息数据
            $api->get('/gettrialinfo', ['as' => 'purchase.gettrialinfo', 'uses' => 'PurchaseController@getTrialPurchaseInfo']);//我审核的采购单详情
            $api->get('/withdraw', ['as' => 'purchase.withdraw', 'uses' => 'PurchaseController@withdraw']);//采购订单撤回
            $api->get('/getinfone', ['as' => 'purchase.getinfone', 'uses' => 'PurchaseController@getPurchaseInfoOne']);//代办中我申请的采购单详情页面
            $api->get('/getpayablemoney', ['as' => 'purchase.getpayablemoney', 'uses' => 'PurchaseController@getPayableMoney']);//获取我在供应商那此前应付多少钱
            $api->get('/getorderlist', ['as' => 'purchase.getorderlist', 'uses' => 'PurchaseController@getOrderList']);//采购单列表
            $api->get('/getuniversalcode', ['as' => 'purchase.getuniversalcode', 'uses' => 'PurchaseController@getUniversalCode']);//采购单列表
            $api->get('/getpurchaselist', ['as' => 'purchase.getpurchaselist', 'uses' => 'PurchaseController@getPurchaseList']);//采购单列表
        });
    });
    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\PAS"], function ($api) {
        $api->group(['prefix' => '/suppliers'], function($api){
            $api->post('/setadd', ['as' => 'supplier.setadd', 'uses' => 'SupplierController@setAdd']);//添加供应商
        });

    });
    //不需要登录的
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\PAS"], function ($api) {
        $api->group(['prefix' => '/suppliers'], function($api){
            $api->post('/setadd', ['as' => 'supplier.setadd', 'uses' => 'SupplierController@setAdd']);//添加供应商
        });

    });
});

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();


Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});
Route::any('/wechat/event', 'WeChatController@event');
Route::get('/wechat/redirect', 'WeChatController@redirect');
Route::get('/wechat/callback', 'WeChatController@callback');
Route::get('/s', 'WeChatController@scan');

// Rpc
Route::get('supplier', 'Rpc\SupplierController@start');
Route::get('rpc/test', 'Rpc\TestController@test');
Route::get('rpc/index', 'Rpc\TestController@index');
Route::get('rpc/getPu', 'Rpc\TestController@getPu');
Route::post('rpc/assets', 'Rpc\AssetsController@start');
Route::post('service', 'Rpc\UserController@start');
Route::post('supplier', 'Rpc\SupplierController@start');
Route::post('rpc/order', 'Rpc\OrderController@start');
Route::post('rpc/goods', 'Rpc\GoodsController@start');
Route::post('rpc/finance', 'Rpc\FinanceController@start');


// normal user
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/test', 'TestController@index');

Route::group(['middleware' => 'auth'], function () {
	
	if(config('app.debug') ===true){ //查看日志
		Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
	}
	
    Route::any('/icon', 'HomeController@icon');
    Route::get('/', 'HomeController@index')->name('home'); //首页
    Route::resource('/file', 'FileController');
    // 获取用户出差申请记录
    Route::get('/user/attendance/travels/{id}', 'Attendance\TravelsController@getApplications')
        ->name('user.attendance.travels');
    // 获取用户财务暂支单
    //Route::get('/user/finance/advancePayment', 'Finance\FinanceApController@getNeedRepayList')
        //->name('user.finance.advancePayment');
});

// 消息模板管理
Route::group(['middleware' => ['auth' => 'admin.permission'], 'prefix' => 'message-template'], function () {
    Route::get('/index', 'MessageTemplateController@index')->name('message.template.index');
    Route::get('/view/{id}', 'MessageTemplateController@view')->name('message.template.view');
    Route::post('/delete/{id}', 'MessageTemplateController@delete')->name('message.template.delete');
    Route::match(['GET', 'POST'], '/create', 'MessageTemplateController@create')->name('message.template.create');
    Route::match(['GET', 'POST'], '/update/{id}', 'MessageTemplateController@update')
        ->name('message.template.update');
    Route::post('/import', 'MessageTemplateController@import')->name('message.template.import');
    Route::post('/export', 'MessageTemplateController@export')->name('message.template.export');
    Route::post('/export-all', 'MessageTemplateController@exportAll')->name('message.template.exportAll');
});

//HR manager
Route::group(['middleware' => ['auth' => 'admin.permission']], function () {
    Route::post('/department/leader', 'DepartmentController@leader');
    //员工
    Route::group(['prefix' => 'users'], function () {
        Route::get('/setPasswordFromWx', 'User\UsersController@setPasswordFromWx')
            ->name('users.wx_setPassword');//企业微信登陆设置密码
        Route::post('/setPasswordFromWx/reset', 'User\UsersController@reset')->name('users.wx_setPassword.reset');
        Route::any('delete/{id?}', 'User\UsersController@destroy')->name("users.delete");   //员工管理-离职
        Route::post('uploadimg/{id}', 'User\UsersController@uploadImg')->name('users.uploadimg'); //员工管理-图片上传
        Route::any('search', 'User\UsersController@search')->name('users.search'); //员工管理-员工搜索
        Route::post('check', 'User\UsersController@userCheck')->name('users.check'); //员工管理-待入职管理-系统账号唯一检测
        Route::post('ajax_search/{id?}', 'User\UsersController@userAjaxSearch')->name('users.ajax_search');//员工管理-汇报领导查询

        Route::get('/{id}/dimission_create', 'User\UsersController@dimissionCreate')
            ->name('dimission.create'); //员工管理-离职信息页
        Route::post('/dimission_store', 'User\UsersController@dimissionStore')->name('dimission.store'); //员工管理-离职信息保存
        Route::post('/dimission_update', 'User\UsersController@dimissionUpdate')
            ->name('dimission.update'); //员工管理-离职信息更新
        Route::get('/{id}/dimission_edit', 'User\UsersController@dimissionEdit')->name('dimission.edit'); //员工管理-离职信息编辑页
        Route::get('dimission/{id}', 'User\UsersController@dimissionShow')->name('dimission.show'); //员工管理-离职信息详情页
        Route::get('/download', 'User\UsersController@downloadRoster')->name('users.download'); //员工管理-花名册导出

        Route::get('setting/{id}/edit', 'User\SettingController@edit')->name('users.setting.edit');  //员工信息设置页
        Route::put('setting/{id}', 'User\SettingController@update')->name('users.setting.update');//员工信息设置更新
        Route::get('data_show', 'User\UsersController@personalDataShow')->name('users.data_show');//员工个人资料展示
        Route::post('add_bank_card', 'User\UsersController@addBankCard')->name('users.add_bank_card');//员工个人资料-银行卡信息-添加银行卡
        Route::post('/admin_add_bank_card', 'User\UsersController@adminAddBankCard')->name('users.admin_add_bank_card');//员工个人资料-银行卡信息-管理员添加银行卡
        Route::post('delete_bank_card/{id}', 'User\UsersController@deleteBankCard')->name('users.delete_bank_card');//员工个人资料-银行卡信息-删除银行卡
        Route::post('add_urgent_user', 'User\UsersController@addUrgentUser')->name('users.add_urgent_user');//员工个人资料-紧急联系人-添加
        Route::post('delete_urgent_user/{id}', 'User\UsersController@deleteUrgentUser')->name('users.delete_urgent_user');//员工个人资料-紧急联系人-删除
        Route::post('add_family', 'User\UsersController@addFamily')->name('users.add_family');//员工个人资料-家庭信息-添加
        Route::post('delete_family/{id}', 'User\UsersController@deleteFamily')->name('users.delete_family');//员工个人资料-家庭信息-删除

    }); // Users group
    Route::resource('users', 'User\UsersController'); // users.index 员工管理-花名册-员工列表页 users.create 员工管理-员工创建页
    // users.store 员工管理-员工创建   users.show 员工管理-员工信息详情页 users.edit 员工管理-员工编辑页 users.update 员工管理-员工信息更新

    //部门员工关系
    Route::group(['prefix' => 'deptuser'], function () {
        Route::get('/depart', 'DepartmentController@depart')->name('dept.depart'); //部门管理-部门列表页
        Route::get('/{id?}', 'DepartmentController@user')->name('dept.user');  //通讯录-部门员工信息列表页
        Route::get('/batch_import', 'DepartmentController@batchImport')->name('dept.batch_import');  //通讯录-部门员工信息列表页
    });

    //待入职员工
    Route::group(['prefix' => 'pendingusers'], function () {
        Route::get('/index', 'User\PendingUsersController@index')->name('pendingusers.index');  //员工管理-待入职管理-待入职员工列表页
        Route::get('/create', 'User\PendingUsersController@create')->name('pendingusers.create'); //员工管理-待入职管理-待入职员工创建页
        Route::get('/{id}/edit', 'User\PendingUsersController@edit')->name('pendingusers.edit'); //员工管理-待入职管理-待入职员工编辑页
        Route::any('/update', 'User\PendingUsersController@update')->name('pendingusers.update'); //员工管理-待入职管理-待入职员工信息更新
        Route::post('/store', 'User\PendingUsersController@store')->name('pendingusers.store'); // 员工管理-待入职管理-待入职员工创建
        Route::post('/delete', 'User\PendingUsersController@delete')->name('pendingusers.delete'); //员工管理-待入职管理-待入职员工删除
        Route::get('/{id}/join', 'User\PendingUsersController@join')->name('pendingusers.join'); //员工管理-待入职管理-待入职员工转入职页
        Route::get('/{id}', 'User\PendingUsersController@show')->name('pendingusers.show'); //员工管理-待入职管理-待入职员工详情页
    });


    //部门
    Route::group(['prefix' => 'departments'], function () {
        Route::get('/', 'DepartmentController@index')->name('departments.index');      //员工管理-人事地图
        Route::post('store', 'DepartmentController@store')->name('departments.store');  //部门管理-部门创建
        Route::post('update', 'DepartmentController@update')->name('departments.update'); //部门管理-部门信息更新
        Route::post('delete', 'DepartmentController@destroy')->name('departments.delete'); //部门管理-部门删除
        Route::get('all/{id?}', 'DepartmentController@all')->name('departments.all');  //部门管理-组织架构信息
        //        Route::get('user/{id?}','DepartmentController@user')->name('departments.user');
        //        Route::post('setleader','DepartmentController@setLeader')->name('departments.setleader');
    });
    //    Route::resource('department', 'DepartmentController');
    //统计报表
    Route::group(['prefix' => 'stats'], function () {
        Route::get('/turnover', 'User\StatisticsController@turnover')->name('stats.turnover');   //员工管理-入离职统计表
    });

    //个人假期
    Route::group(['prefix' => 'vacation'], function () {
        Route::get('/show', 'Attendance\VacationController@show')->name('vacation.show');   //考勤管理-个人假期展示
    });

    //员工假期管理
    Route::group(['prefix' => 'vacationManage'], function () {
        Route::get('/show', 'Attendance\VacationManageController@show')->name('vacationManage.show');  //考勤管理-假期管理首页
        Route::post('/addVacations', 'Attendance\VacationManageController@addVacations')
            ->name('vacationManage.addVacations');  //考勤管理-假期管理批量导入数据
        Route::get('/download', 'Attendance\VacationManageController@downloadRoster')
            ->name('vacationManage.download'); //考勤管理-员工假期管理excel空表下载
        Route::get('/downloadVacationDate', 'Attendance\VacationManageController@downloadVacationDate')
            ->name('vacationManage.downloadVacationDate'); //考勤管理-员工假期管理-员工假期数据下载
        Route::get('/download2', 'Attendance\VacationManageController@downloadRoster2')
            ->name('vacationManage.download2'); //考勤管理-节假日管理，去年假期剩余  excel空表下载
        Route::post('/addLastYearVacations', 'Attendance\VacationManageController@addLastYearVacations')
            ->name('vacationManage.addLastYearVacations');  //考勤管理-假期管理批量导入数据
        Route::get('/search', 'Attendance\VacationManageController@search')
            ->name('vacationManage.search');  //考勤管理-员工假期管理-员工搜索
    });

    //节假日管理
    Route::group(['prefix' => 'holiday'], function () {
        Route::get('/index', 'Attendance\HolidayController@index')->name('holiday.index');   //考勤管理-节假日管理
        Route::post('/addHolidays', 'Attendance\HolidayController@addHolidays')
            ->name('holiday.addHolidays');  //考勤管理-节假日管理批量导入数据
        Route::get('/download', 'Attendance\HolidayController@downloadRoster')
            ->name('holiday.download'); //考勤管理-节假日管理excel空表下载
        Route::get('/chooseTime', 'Attendance\HolidayController@chooseTime')
            ->name('holiday.chooseTime'); //考勤管理-节假日管理-记录展示
        Route::put('/updateHoliday', 'Attendance\HolidayController@updateHoliday')
            ->name('holiday.updateHoliday');  //考勤管理-节假日管理-更新记录
    });

    //财务管理
    Route::group(['prefix' => 'finance'], function () {
        Route::get('flow/index/{type}', 'Finance\FinanceController@flowIndex')->name('finance.flow.index');
        Route::get('flow/show/{id}/{type}', 'Finance\FinanceController@flowShow')->name('finance.flow.show');
        Route::match(['get', 'post'], 'flow/search/{type}', 'Finance\FinanceController@flowSearch')->name('finance.flow.search');
        Route::get('flow/relation_maintain/index', 'Finance\FinanceController@flowRelationMaintainIndex')->name('finance.relation.maintain.index');
        Route::get('flow/relation_maintain/download_stencil', 'Finance\FinanceController@downloadStencil')->name('finance.relation.maintain.download_stencil');
        Route::match(['get', 'post'], 'flow/relation_maintain/map/search/{type}',
            'Finance\FinanceController@mapSearch')->name('finance.relation.maintain.map_search');
        Route::post('flow/relation_maintain/upload', 'Finance\FinanceController@upload')->name('finance.relation.maintain.upload');
    });


    //财务管理权限(******新******)
    /* lee 2019/4/15
    */
    Route::group(['prefix' => 'financialManage'], function () {
        Route::get('/accountSubject', 'Finance\FinanceController@accountSubject')->name('finance.financialManage.accountSubject');
        Route::get('/accountSubjectEdit', 'Finance\FinanceController@accountSubjectEdit')->name('finance.financialManage.accountSubjectEdit');
        Route::get('/accountSubjectAdd', 'Finance\FinanceController@accountSubjectAdd')->name('finance.financialManage.accountSubjectAdd');
        Route::get('/accountSubjectSearch', 'Finance\FinanceController@accountSubjectSearch')->name('finance.financialManage.accountSubjectSearch');
        Route::post('/accountSubjectStore', 'Finance\FinanceController@accountSubjectStore')->name('finance.financialManage.accountSubjectStore');
        Route::get('/accountSubjectDel', 'Finance\FinanceController@accountSubjectDel')->name('finance.financialManage.accountSubjectDel');
        Route::get('/coin', 'Finance\FinanceController@coin')->name('finance.financialManage.coin');
        Route::get('/coinSearch', 'Finance\FinanceController@coinSearch')->name('finance.financialManage.coinSearch');
        Route::get('/coinEdit', 'Finance\FinanceController@coinEdit')->name('finance.financialManage.coinEdit');
        Route::get('/coinAdd', 'Finance\FinanceController@coinAdd')->name('finance.financialManage.coinAdd');
        Route::get('/coinDel', 'Finance\FinanceController@coinDel')->name('finance.financialManage.coinDel');
        Route::post('/coinStore', 'Finance\FinanceController@coinStore')->name('finance.financialManage.coinStore');
        Route::get('/costBudget', 'Finance\FinanceController@costBudget')->name('finance.financialManage.costBudget');
        Route::get('/budgetSetting', 'Finance\FinanceController@budgetSetting')->name('finance.financialManage.budgetSetting');
        Route::get('/budgetAdd', 'Finance\FinanceController@budgetAdd')->name('finance.financialManage.budgetAdd');
        Route::get('/budgetEdit', 'Finance\FinanceController@budgetEdit')->name('finance.financialManage.budgetEdit');
        Route::get('/budgetDel', 'Finance\FinanceController@budgetDel')->name('finance.financialManage.budgetDel');
        Route::post('/budgetStore', 'Finance\FinanceController@budgetStore')->name('finance.financialManage.budgetStore');
        Route::get('/budgetCondition', 'Finance\FinanceController@budgetCondition')->name('finance.financialManage.budgetCondition');
        Route::post('/budgetConditionAdd', 'Finance\FinanceController@budgetConditionAdd')->name('finance.financialManage.budgetConditionAdd');
        Route::post('/budgetConditionSetting', 'Finance\FinanceController@budgetConditionSetting')->name('finance.financialManage.budgetConditionSetting');
        Route::get('/voucher', 'Finance\FinanceController@voucher')->name('finance.financialManage.voucher');
        Route::get('/voucherSetting', 'Finance\FinanceController@voucherSetting')->name('finance.financialManage.voucherSetting');
        Route::get('/voucherEdit', 'Finance\FinanceController@voucherEdit')->name('finance.financialManage.voucherEdit');
        Route::get('/voucherAdd', 'Finance\FinanceController@voucherAdd')->name('finance.financialManage.voucherAdd');
        Route::post('/voucherStore', 'Finance\FinanceController@voucherStore')->name('finance.financialManage.voucherStore');
        Route::get('/voucherSofpzz', 'Finance\FinanceController@voucherSofpzz')->name('finance.financialManage.voucherSofpzz');
        Route::get('/voucherDel', 'Finance\FinanceController@voucherDel')->name('finance.financialManage.voucherDel');
        Route::get('/voucherItemAdd', 'Finance\FinanceController@voucherItemAdd')->name('finance.financialManage.voucherItemAdd');
        Route::get('/voucherItemEdit', 'Finance\FinanceController@voucherItemEdit')->name('finance.financialManage.voucherItemEdit');
        Route::post('/voucherItemStore', 'Finance\FinanceController@voucherItemStore')->name('finance.financialManage.voucherItemStore');
        Route::get('/voucherItemDel', 'Finance\FinanceController@voucherItemDel')->name('finance.financialManage.voucherItemDel');
        Route::get('/balance', 'Finance\FinanceController@balance')->name('finance.financialManage.balance');
        Route::get('/balanceAdd', 'Finance\FinanceController@balanceAdd')->name('finance.financialManage.balanceAdd');
        Route::get('/balanceEdit', 'Finance\FinanceController@balanceEdit')->name('finance.financialManage.balanceEdit');
        Route::get('/balanceDel', 'Finance\FinanceController@balanceDel')->name('finance.financialManage.balanceDel');
        Route::post('/balanceStore', 'Finance\FinanceController@balanceStore')->name('finance.financialManage.balanceStore');
        Route::get('/sofzz', 'Finance\FinanceController@sofzz')->name('finance.financialManage.sofzz');
        Route::get('/sofzzAdd', 'Finance\FinanceController@sofzzAdd')->name('finance.financialManage.sofzzAdd');
        Route::get('/sofzzEdit', 'Finance\FinanceController@sofzzEdit')->name('finance.financialManage.sofzzEdit');
        Route::post('/sofzzStore', 'Finance\FinanceController@sofzzStore')->name('finance.financialManage.sofzzStore');
        Route::get('/sofzzDel', 'Finance\FinanceController@sofzzDel')->name('finance.financialManage.sofzzDel');
        Route::get('/budgetDimension', 'Finance\FinanceController@budgetDimension')->name('finance.financialManage.budgetDimension');
        Route::get('/budgetDimensionAdd', 'Finance\FinanceController@budgetDimensionAdd')->name('finance.financialManage.budgetDimensionAdd');
        Route::get('/budgetDimensionEdit', 'Finance\FinanceController@budgetDimensionEdit')->name('finance.financialManage.budgetDimensionEdit');
        Route::post('/budgetDimensionStore', 'Finance\FinanceController@budgetDimensionStore')->name('finance.financialManage.budgetDimensionStore');
        Route::get('/budgetDimensionDel', 'Finance\FinanceController@budgetDimensionDel')->name('finance.financialManage.budgetDimensionDel');
        Route::get('/budgetCategory', 'Finance\FinanceController@budgetCategory')->name('finance.financialManage.budgetCategory');
        Route::get('/budgetCategoryAdd', 'Finance\FinanceController@budgetCategoryAdd')->name('finance.financialManage.budgetCategoryAdd');
        Route::get('/budgetCategoryEdit', 'Finance\FinanceController@budgetCategoryEdit')->name('finance.financialManage.budgetCategoryEdit');
        Route::post('/budgetCategoryStore', 'Finance\FinanceController@budgetCategoryStore')->name('finance.financialManage.budgetCategoryStore');
        Route::get('/budgetCategoryDel', 'Finance\FinanceController@budgetCategoryDel')->name('finance.financialManage.budgetCategoryDel');
        Route::get('/budgetPoint', 'Finance\FinanceController@budgetPoint')->name('finance.financialManage.budgetPoint');
        Route::post('/getDesCondition', 'Finance\FinanceController@getDesCondition')->name('finance.financialManage.getDesCondition');
        Route::any('/budgetChange', 'Finance\FinanceController@budgetChange')->name('finance.financialManage.budgetChange');
        Route::any('/budgetItemChange', 'Finance\FinanceController@budgetItemChange')->name('finance.financialManage.budgetItemChange');
        Route::post('/desConditionSave', 'Finance\FinanceController@desConditionSave')->name('finance.financialManage.desConditionSave');
        Route::get('/isBudgetDimensionCondition', 'Finance\FinanceController@isBudgetDimensionCondition')->name('finance.financialManage.isBudgetDimensionCondition');
    });


    //客户管理权限(******新******)
    /* lee 2019/4/30
    */
    Route::group(['prefix' => 'customerManage'], function () {
        Route::get('/customerType', 'Customer\CustomerController@customerType')->name('customer.customerManage.customerType');
        Route::get('/customerTypeAdd', 'Customer\CustomerController@customerTypeAdd')->name('customer.customerManage.customerTypeAdd');
        Route::get('/customerTypeEdit', 'Customer\CustomerController@customerTypeEdit')->name('customer.customerManage.customerTypeEdit');
        Route::get('/customerTypeDel', 'Customer\CustomerController@customerTypeDel')->name('customer.customerManage.customerTypeDel');
        Route::post('/customerTypeStore', 'Customer\CustomerController@customerTypeStore')->name('customer.customerManage.customerTypeStore');
        Route::get('/seasPublic', 'Customer\CustomerController@seasPublic')->name('customer.customerManage.seasPublic');
        Route::post('/seasPublicStore', 'Customer\CustomerController@seasPublicStore')->name('customer.customerManage.seasPublicStore');
        Route::get('/avoidance', 'Customer\CustomerController@avoidance')->name('customer.customerManage.avoidance');
        Route::post('/avoidanceStore', 'Customer\CustomerController@avoidanceStore')->name('customer.customerManage.avoidanceStore');
    });

    //职务管理
    Route::resource(
        'position',
        'PositionController'
    );
    Route::get('/position_delete','PositionController@destroy')->name('position.delete');
    Route::get('/Basic/index','BasicController@index')->name('basic.index');
    Route::post('/Basic/update','BasicController@update')->name('basic.update');
});


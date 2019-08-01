<?php
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/login', 'Admin\LoginController@showLoginForm')->name('show-login-form');
    Route::post('/login', 'Admin\LoginController@login')->name('login');
    Route::post('/check', 'Admin\LoginController@checkUserName')->name('check');
    Route::get('/logout', 'Admin\LoginController@logout')->name('logout');
    Route::get('/index', 'Admin\IndexController@index')->name('index');
    Route::any('/abilities/save', 'Admin\AbilitiesController@save')->name('admin.abilities.save');

    //admin.abilities.index 权限管理-权限-权限列表页 admin.abilities.create 权限管理-权限-权限创建页 admin.abilities.store 权限管理-权限-权限创建
    Route::resource(
        'abilities',
        'Admin\AbilitiesController'
    );
    //admin.roles.index 权限管理-角色-角色列表页 admin.roles.create 权限管理-角色-角色创建页 admin.role.store
    //权限管理-角色-角色创建 admin.roles.edit 权限管理-角色-角色编辑页
    Route::resource(
        'roles',
        'Admin\RolesController'
    );
    //admin.users.index 权限管理-管理员-管理员列表页 admin.users.edit 权限管理-管理员-管理员编辑页 admin.users.update 权限管理-管理员-管理员信息更新
    Route::resource(
        'users',
        'Admin\UsersController'
    );
    Route::get('/resetpassword', 'Admin\IndexController@resetPassword')->name('resetpassword'); //管理员修改密码
    Route::post('/resetpasswordstore', 'Admin\IndexController@resetPasswordStore')->name('resetpasswordstore'); //管理员修改密码
    //admin.routes.index 前台权限管理-前台路由-后台路由-角色-职务
    Route::resource(
        'routes',
        'Admin\RoutesController'
    );
    Route::get('/destroy_routes','Admin\RoutesController@destroyRoutes')->name('destroy.route');
    Route::resource(
        'apiroles',
        'Admin\ApiRolesController'
    );
    Route::resource(
        'user',
        'Admin\UserController'
    );
    Route::resource(
        'vueaction',
        'Admin\VueActionController'
    );
});

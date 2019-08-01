<?php
//考勤相关
Route::group(['middleware' => ['auth' => 'admin.permission'],'prefix' => 'attendance', 'namespace' => 'Attendance', 'as' => 'attendance.'], function () {
    Route::any('/checktime/{month?}', 'CheckTimeController@checktime')->name('attendance.checktime'); //考勤管理-我的打卡
    Route::any('/leader_show', 'CheckTimeController@leaderShow')->name('attendance.leader_show'); //考勤管理-领导查看考勤明细

    Route::post('checktime_update', 'CheckTimeController@update')->name('attendance.update'); //考勤管理-考勤补签
    Route::any('/allusers', 'CheckTimeController@allusers')->name('attendance.allusers'); //考勤管理-考勤列表
    Route::any('/allusers_export/{month?}', 'CheckTimeController@allusersExport')->name('attendance.allusers_export'); //考勤管理-考勤列表导出
    Route::any('/refresh_attendance', 'CheckTimeController@refreshAttendance')->name('attendance.refresh_attendance'); //管理员重新统计考勤
    Route::any('/refresh_workflow', 'CheckTimeController@refreshWorkflow')->name('attendance.refresh_workflow'); //管理员重新统计考勤相关的流程
    Route::any('/department_leader/{month?}', 'CheckTimeController@departmentStatistics')->name('attendance.department_leader'); //考勤管理-部门领导报表
    Route::any('/salary_basis/{month?}', 'CheckTimeController@salaryBasisStatistics')->name('attendance.salary_basis'); //考勤管理-薪资核算基础
    Route::any('/salary/download/{month?}', 'CheckTimeController@salaryExport')->name('salary.export'); //考勤管理-薪资核算基础导出

    Route::get('/duty', 'AttendanceGroupController@index')->name('duty.index'); //考勤管理-班值列表
    Route::get('/duty_create', 'AttendanceGroupController@dutyCreate')->name('duty.create'); //考勤管理-班值创建页
    Route::post('/duty_store', 'AttendanceGroupController@dutyStore')->name('duty.store'); //考勤管理-班值创建
    Route::get('/{id}/duty_edit', 'AttendanceGroupController@dutyEdit')->name('duty.edit'); //考勤管理-班值编辑页
    Route::put('/{id}', 'AttendanceGroupController@dutyUpdate')->name('duty.update'); //考勤管理-班值信息更新
    Route::post('/duty_delete', 'AttendanceGroupController@dutyDelete')->name('duty.delete'); //考勤管理-班值信息删除

    Route::any('/appoint/{month?}', 'AttendanceGroupController@appoint')->name('attendance.appoint'); //排班
    Route::post('/import', 'AttendanceGroupController@import')->name('attendance.import'); //导入排班
    Route::post('/import_crew', 'AttendanceGroupController@importCrew')->name('attendance.import_crew'); //导入非客服排班
    Route::get('/white', 'CheckTimeController@white')->name('attendance.white'); //考勤白名单
    Route::post('/white_add', 'CheckTimeController@whiteAdd')->name('attendance.white_add'); //考勤白名添加
    Route::post('/white_delete', 'CheckTimeController@whiteDelete')->name('attendance.white_delete'); //考勤白名单删除

    //薪酬-考勤
    Route::any('/close_index', 'ClosingController@index')->name('closing.index'); //考勤关账-关账首页
    Route::get('/close_reload/{month?}', 'ClosingController@reload')->name('closing.reload'); //考勤关账-重新载入
    Route::get('/close_export/{month?}', 'ClosingController@export')->name('closing.export'); //考勤关账-导出
    Route::post('/close_import/{month?}', 'ClosingController@import')->name('closing.import'); //考勤关账-导入修改后考勤
    Route::post('/close_close/{month?}', 'ClosingController@close')->name('closing.close'); //考勤关账-关账
    Route::get('/close_record/{month?}', 'ClosingController@record')->name('closing.record'); //考勤关账-关账记录
    Route::any('/close_detail/{id?}', 'ClosingController@detail')->name('closing.detail'); //考勤关账-关账记录明细
    Route::get('/close_detail_export/{id?}', 'ClosingController@detailExport')->name('closing.detail_export'); //考勤关账-关账记录明细导出
    Route::get('/close_daily', 'ClosingController@daily')->name('closing.daily'); //考勤关账-关账考勤每日明细
});

Route::get('/contract', 'Contract\ContractController@index')->name('contract.index');
Route::get('/contract/create', 'Contract\ContractController@create')->name('contract.create');
Route::post('/contract/store', 'Contract\ContractController@store')->name('contract.store');
Route::post('/contract/edit', 'Contract\ContractController@edit')->name('contract.edit');
Route::post('/contract/destroy', 'Contract\ContractController@destroy')->name('contract.destroy');
Route::post('/contract/show', 'Contract\ContractController@show')->name('contract.show');
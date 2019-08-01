<?php
/** @var \Dingo\Api\Routing\Route $api */
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    //需要登录的
    /** @var Route $api */
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {

        /** @var Route $api */
        //投票
        $api->group(
            [
                'prefix' => '/attendance',
                'namespace'=> 'Attendance',
//                'middleware'=>['']
            ], function($api){
            /** @var Route $api */
            $api->get('/annual_rule_list', "AnnualRuleListController@index")->name('annual_rule.list');
            $api->post('/annual_rule_add', "AnnualRuleAddController@index")->name('annual_rule.add');
            $api->get('/annual_rule_delete', "AnnualRuleDeleteController@index")->name('annual_rule.delete');
            $api->get('/workflow_create_vacation_patch', "VacationPatchController@index")->name('vacation.patch.show');//补卡展示
            $api->get('/workflow_vacation_leave', "VacationLeaveController@index")->name('vacation.leave');  //请假展示
            $api->post('/vacation_leave_save', "VacationLeaveSaveController@index")->name('vacation.leave.save');  //请假展示
            $api->post('/extra', "VacationExtraController@save")->name('vacation.extra.save');  //加班保存
            $api->post('/workday', "AttendanceDayController@index")->name('vacation.workday');  //工作日和非工作日
            $api->get('/diff_work_time', "DiffWorkTimeController@index")->name('vacation.diff_work_time');  //工作日和非工作日

            $api->post('/vacation_set', 'VacationSetController@index')->name('vacation.set');
            $api->get('/vacation_set_show', 'VacationSetShowController@index')->name('vacation.set.show');
        });
    });



});

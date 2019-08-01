<?php


//工作流
Route::group(['middleware' => ['auth' => 'admin.permission'], 'prefix' => 'workflow', 'namespace' => 'Workflow', 'as' => 'workflow.'],
    function () {

        Route::get('common/work-time', 'CommonController@getWorkTime')->name('common.work-time');
        Route::get('common/date-interval', 'CommonController@getDateInterval')->name('common.date-interval');   //计算两日期间隔
        Route::get('common/work-time-by-hour', 'CommonController@getWorkTimeByHour')->name('common.work-time-by-hour');
        Route::get('common/company-list', 'CommonController@getCompanyList')->name('common.company-list');
        Route::get('common/contracts-list', 'CommonController@getOwnContracts')->name('common.contracts-list');

        Route::post('pass/{id}', 'ProcController@pass')->name('pass'); // 审批-通过
        Route::post('pass-next/{id}', 'ProcController@passAndNext')->name('pass-next'); // 审批-通过
        Route::post('reject/{id}', 'ProcController@unpass')->name('reject'); // 审批-拒绝
        Route::post('reject-next/{id}', 'ProcController@unPassAndNext')->name('reject-next'); // 审批-拒绝-跳转到下一个
        Route::post('proc/pass_all', 'ProcController@pass_all')->name('proc.pass_all'); // 审批-批量通过
        Route::post('proc/unpass_all', 'ProcController@unpass_all')->name('proc.unpass_all'); // 审批-批量拒绝

        Route::get('proc/children', 'ProcController@children')->name('proc.children'); // 流程申请-审批记录
        Route::resource('proc', 'ProcController');
        Route::get('entry/todo-list', 'EntryController@todoList')->name('entry.todo-list'); // 待办事项
        Route::get('entry/store-system', 'EntryController@storeBySystem')->name('entry.store-system'); // 流程申请-重新发起申请,暂不开放
        Route::get('entry/resend', 'EntryController@resend')->name('entry.resend'); // 流程申请-重新发起申请,暂不开放
        Route::get('entry/cancel', 'EntryController@cancel')->name('entry.cancel'); // 流程申请-撤销
        Route::get('entry/my_apply', ['uses' => 'EntryController@myApply', 'as' => 'entry.my_apply']); // 流程申请-我的申请
        Route::get('entry/my_procs', ['uses' => 'EntryController@myProcs', 'as' => 'entry.my_procs']); // 流程申请-待我审批
        Route::get('entry/my_audited',
            ['uses' => 'EntryController@myAudited', 'as' => 'entry.my_audited']); // 流程申请-我审批过的
        Route::get('entry/process_query', 'EntryController@processQuery')->name('entry.process_query'); //流程查询
        Route::resource('entry', 'EntryController');
        Route::get('entry/manager/show/{id}/{userId}', 'EntryController@managerShow')->name('entry.manager.show');
        Route::get('entry', 'EntryController@todoList')->name('entry.index'); // 待办事项


        Route::post('flow/publish', 'FlowController@publish')->name('flow.publish'); // 流程编辑-发布
        Route::get('flow/design/{id}', ['uses' => 'FlowController@design', 'as' => 'flow.design']); // 流程编辑-审批关系编辑
        Route::get('flow/clone_new_version', 'FlowController@cloneNewVersion')->name('flow.clone_new_version'); // 克隆新版本
        Route::put('flow/set_abandon', 'FlowController@setAbandon')->name('flow.set_abandon'); // 流程废弃
        Route::get('flow/export', 'FlowController@export')->name('flow.export'); // 流程导出
        Route::post('flow/import', 'FlowController@import')->name('flow.import'); // 流程导入
        Route::resource('flow', 'FlowController');

        Route::resource('template', 'TemplateController');
        Route::resource('template_form', 'TemplateFormController');

        Route::post('process/condition', 'ProcessController@condition')->name('process.condition'); // 流程编辑-节点条件
        Route::get('process/attribute', 'ProcessController@attribute')->name('process.attribute'); // 流程编辑-节点属性

        Route::post('process/begin', 'ProcessController@setFirst')->name('process.begin'); // 流程编辑-设置第一步节点
        Route::post('process/stop', 'ProcessController@setLast')->name('process.stop'); // 流程编辑-设置为结束节点

        Route::resource('process', 'ProcessController');

        Route::get('/flowlink/auth/dept/{id}', 'FlowlinkController@dept')->name('flowlink.auth.dept'); // 流程编辑-审批权限[部门]
        Route::get('flowlink/auth/role/{id}', 'FlowlinkController@role')->name('flowlink.auth.role'); // 流程编辑-审批权限[角色]
        Route::get('flowlink/auth/emp/{id}', 'FlowlinkController@emp')->name('flowlink.auth.emp'); // 流程编辑-审批权限[个人]
        Route::post('flowlink', 'FlowlinkController@update')->name('flowlink'); // 流程编辑-保存流程设计

        Route::resource('authorize_agent', 'AuthorizeAgentController'); // 审批权限-代理

        Route::resource('role', 'RoleController'); // 审批权限-角色

        Route::resource('role_user', 'RoleUserController'); // 审批权限-角色用户

        Route::get('approval/index', 'ApprovalController@index')->name('approval.index'); // 审批后台管理-流程列表
        Route::get('approval/flow/{id}', 'ApprovalController@flow')->name('approval.flow'); // 审批后台管理-审批列表
        Route::get('approval/flowexport/{id}', 'ApprovalController@flowExport')->name('approval.flow_export'); // 审批后台管理-导出
        Route::get('/hrshow/{id}', ['uses' => 'EntryController@hrShow', 'as' => 'entry.hr_show']); // 审批后台管理-查看流程

        Route::get('entry/data/{id}', ['uses' => 'EntryController@EntryData', 'as' => 'entry.data']); // 获取指定流程

        Route::get('manager', 'ApplyManagerController@index')->name('manager.index'); //流程申请查询
        Route::delete('manager/destroy/{id}', 'ApplyManagerController@destroy')->name('manager.destroy'); //撤销流程申请
    }
);

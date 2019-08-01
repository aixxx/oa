<?php
//财务相关 路由

$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1", 'middleware' => 'auth:api'], function ($api) {
        //之后在这里写api
        $api->group(['prefix' => '/workflow/finance'], function ($api) {
            $api->get('/list', ['as' => 'api.finance.flow.list', 'uses' => 'FinanceController@toDoList']);//我申请的列表
			$api->get('/index', ['as' => 'api.finance.flow.index', 'uses' => 'FinanceController@index']);//我的财务首页
			$api->any('/company_index', 'FinanceController@companyIndex')->name('api.finance.flow.company_index'); //集团财务待办列表
			$api->get('/company_detail', 'FinanceController@companyDetail')->name('api.finance.flow.company_detail'); //集团财务待办详情
            $api->get('/childer', ['as' => 'api.finance.flow.childer', 'uses' => 'FinanceController@childer']);//查看财务下的分账列表
			
			$api->any('/change_status', ['as' => 'api.finance.flow.change_status', 'uses' => 'FinanceController@changeStatus']);//财务改变状态
			$api->any('/update_financial', ['as' => 'api.finance.flow.update_financial', 'uses' => 'FinanceController@updateFinancial']);//财务撤销

            $api->get('/flow_list', ['as' => 'api.finance.flow.list', 'uses' => 'FinanceController@workflowList']);//获取财务或其他相关的创建列表
            $api->get('/flow_create', ['as' => 'api.finance.flow.create', 'uses' => 'FinanceController@createWorkflow']);//财务申请报销
            $api->post('/flow_store', ['as' => 'api.finance.flow.store', 'uses' => 'FinanceController@storeWorkflow']);//财务保存报销
            $api->get('/flow_show', ['as' => 'api.finance.flow.show', 'uses' => 'FinanceController@showWorkflow']);//申请人角色查看详情
            $api->get('/process_query', 'FinanceController@processQuery')->name('api.finance.flow.process_query'); //流程查询
            $api->get('/flow_edit', ['as' => 'api.finance.flow.edit', 'uses' => 'FinanceController@editWorkflow']);//草稿
            $api->get('/flow_auditor', 'FinanceController@myAudited')->name('api.finance.flow.flow_auditor'); //我申请的
            //$api->get('/flow_procs', 'FinanceController@myProcs')->name('api.finance.flow.flow_procs'); //
			$api->get('/voucher', 'FinanceController@voucher')->name('api.finance.flow.voucher'); //我的审核完成的列表，待生成凭证
			
			$api->get('/dept_index', 'FinanceDepartmentController@index')->name('api.finance.flow.dept_index'); //部门财务首页
			$api->get('/dept_list', 'FinanceDepartmentController@list')->name('api.finance.flow.dept_list'); //部门财务列表
			$api->get('/dept_index_statistics', 'FinanceDepartmentController@deptStatistics')->name('api.finance.flow.dept_index_statistics'); //部门财务首页
			
			$api->any('/get_finance_dept', 'FinanceController@getFinanceDept')->name('api.finance.flow.get_finance_dept'); //通过组织id获取所有的部门
			$api->any('/get_limit_price', 'FinanceController@getLimitPrice')->name('api.finance.flow.get_limit_price'); //通过传的条件获取限制价格
			$api->any('/get_init_status', 'FinanceController@initStatus')->name('api.finance.flow.get_init_status'); //初始化进入我的财务显示状态
			$api->any('/get_flow_account', 'FinanceController@getFlowAccount')->name('api.finance.flow.get_flow_account'); //获取银行账户类型
			
			$api->get('/dept_plan', 'FinanceController@deptPlan')->name('api.finance.flow.dept_plan'); //部长专属-经营计划
			$api->get('/flow_edit_show', ['as' => 'api.finance.flow.edit.show', 'uses' => 'FinanceController@editWorkflowShow']);//驳回编辑
			
		
			
			
			
			
            //审批人myAudited
            $api->get('/auditor_flow_show', ['as' => 'api.finance.auditor_flow.show', 'uses' => 'FinanceController@showAuditorWorkflow']);
            $api->post('/pass', ['as' => 'api.finance.flow.pass', 'uses' => 'FinanceController@passWorkflow']);
            $api->post('/reject', ['as' => 'api.finance.flow.reject', 'uses' => 'FinanceController@rejectWorkflow']);

            // 账户信息
            $api->get('/account/index', ['as' => 'api.finance.account.index', 'uses' => 'AccountsController@index']);
            $api->get('/account/list', ['as' => 'api.finance.account.list', 'uses' => 'AccountsController@list']);
			$api->get('/account/test', ['as' => 'api.finance.account.index', 'uses' => 'TestController@testNewAttr']);
        });
    });
    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之后在这里写api
        $api->get('/budgets', ['as' => 'api.finance.flow.budgets', 'uses' => 'FinanceController@budgets']);
        $api->get('/budgets_items', ['as' => 'api.finance.flow.budgets_items', 'uses' => 'FinanceController@budgetsItems']);
    });

});

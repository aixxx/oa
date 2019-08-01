<?php
$api = app("Dingo\Api\Routing\Router");
$api->version('v1', function ($api) {
    $api->group(["namespace" => "App\Http\Controllers\Api\V1\Salary", 'middleware' => 'auth:api'], function ($api) {
        $api->group(['prefix' => '/salary_form'], function ($api) {
            $api->get('/total_salary', ['as' => 'api.salary_form.total_salary', 'uses' => 'SalaryFormController@showTotalSalary']);
            $api->get('/salary_month', ['as' => 'api.salary_form.salary_month', 'uses' => 'SalaryFormController@showSalaryMonth']);
            //计算薪资
            $api->get('/count_refresh', ['as' => 'api.salary_form.refresh', 'uses' => 'SalaryFormController@refresh']);
            $api->get('/sync_attendance', ['as' => 'api.salary_form.sync_attendance', 'uses' => 'SalaryFormController@syncAttendance']);
            $api->get('/sync_social_security', ['as' => 'api.salary_form.sync_social_security', 'uses' => 'SalaryFormController@syncSocialSecurity']);
            $api->get('/sync_tax', ['as' => 'api.salary_form.sync_tax', 'uses' => 'SalaryFormController@syncTax']);
            $api->get('/sync_performance', ['as' => 'api.salary_form.sync_performance', 'uses' => 'SalaryFormController@syncPerformance']);
            $api->get('/payroll_user', ['as' => 'api.salary_form.payroll_user', 'uses' => 'SalaryFormController@payrollUser']);
            $api->get('/cost_list', ['as' => 'api.salary_form.cost_list', 'uses' => 'SalaryFormController@showCostList']);
            $api->get('/personal_form', ['as' => 'api.salary_form.personal_form', 'uses' => 'SalaryFormController@showPersonalForm']);
            $api->get('/create_salary_apply', ['as' => 'api.salary_form.createSalaryRecordApply', 'uses' => 'SalaryFormController@createSalaryRecordApply']);
            $api->get('/passed_salary_group', ['as' => 'api.salary_form.goToPassedSalaryGroup', 'uses' => 'SalaryFormController@goToPassedSalaryGroup']);
            $api->get('/send_salary_form', ['as' => 'api.salary_form.sendSalaryForm', 'uses' => 'SalaryFormController@sendSalaryForm']);
            $api->get('/salary_type_list', ['as' => 'api.salary_form.fetchSalarySyncList', 'uses' => 'SalaryFormController@fetchSalarySyncList']);
            $api->get('/form_status_count', ['as' => 'api.salary_form.fetchSalaryFormStatusCount', 'uses' => 'SalaryFormController@fetchSalaryFormStatusCount']);
            $api->get('/form_status_list', ['as' => 'api.salary_form.fetchSalaryFormStatusList', 'uses' => 'SalaryFormController@fetchSalaryFormStatusList']);
            $api->get('/salary_form_send', ['as' => 'api.salary_form.salaryFormSend', 'uses' => 'SalaryFormController@salaryFormSend']);
            $api->get('/salary_form_confirm', ['as' => 'api.salary_form.salaryFormConfirm', 'uses' => 'SalaryFormController@salaryFormConfirm']);
            $api->get('/salary_form_withdraw', ['as' => 'api.salary_form.salaryFormWithdraw', 'uses' => 'SalaryFormController@salaryFormWithdraw']);
            $api->get('/salary_form_view', ['as' => 'api.salary_form.salaryFormView', 'uses' => 'SalaryFormController@salaryFormView']);
            $api->get('/view_personal_salary_form_list', ['as' => 'api.salary_form.viewPersonalSalaryFormList', 'uses' => 'SalaryFormController@viewPersonalSalaryFormList']);
            $api->get('/view_personal_salary_form', ['as' => 'api.salary_form.viewPersonalSalaryForm', 'uses' => 'SalaryFormController@viewPersonalSalaryForm']);
            $api->get('/status_salary_record', ['as' => 'api.salary_form.fetchSalaryStatus', 'uses' => 'SalaryFormController@fetchSalaryStatus']);
            //fetchSalaryStatus

        });
    });
});

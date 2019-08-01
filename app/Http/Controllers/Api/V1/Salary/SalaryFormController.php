<?php

namespace App\Http\Controllers\Api\V1\Salary;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Salary\SalaryRepository;
use Illuminate\Http\Request;

class SalaryFormController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = app()->make(SalaryRepository::class);
    }

    //薪资已计算页面
    public function showTotalSalary()
    {
        return $this->repository->fetchTotalSalary();
    }

    public function showSalaryMonth()
    {
        return $this->repository->fetchSalaryMonths();
    }

    public function refresh(Request $request)
    {
        return $this->repository->refresh($request);
    }

    public function syncAttendance(Request $request)
    {
        return $this->repository->syncAttendance($request);
    }

    public function syncSocialSecurity(Request $request)
    {
        return $this->repository->syncSocialSecurity($request);
    }

    public function syncTax(Request $request)
    {
        return $this->repository->syncTax($request);
    }

    public function syncPerformance(Request $request){
        return $this->repository->syncPerformance($request);
    }

    //payrollUser
    public function payrollUser(Request $request)
    {
        return $this->repository->payrollUser($request);
    }

    public function showCostList(Request $request)
    {
        return $this->repository->fetchHumanCostList($request);
    }

    public function showPersonalForm(Request $request)
    {
        return $this->repository->fetchPersonalSalaryForm($request);
    }

    public function createSalaryRecordApply(Request $request)
    {
        return $this->repository->createSalaryRecordApply($request);
    }

    public function goToPassedSalaryGroup(Request $request)
    {
        return $this->repository->goToPassedSalaryGroup($request);
    }

    public function sendSalaryForm(Request $request)
    {
        return $this->repository->sendSalaryForm($request);
    }

    public function fetchSalarySyncList(Request $request)
    {
        return $this->repository->fetchSalarySyncList($request);
    }

    public function fetchSalaryFormStatusCount(Request $request)
    {
        return $this->repository->fetchSalaryFormStatusCount($request);
    }

    public function fetchSalaryFormStatusList(Request $request)
    {
        return $this->repository->fetchSalaryFormStatusList($request);
    }

    public function salaryFormSend(Request $request)
    {
        return $this->repository->salaryFormSend($request);
    }

    public function salaryFormWithdraw(Request $request)
    {
        return $this->repository->salaryFormWithdraw($request);
    }

    public function viewPersonalSalaryFormList(Request $request)
    {
        return $this->repository->viewPersonalSalaryFormList($request);
    }

    public function viewPersonalSalaryForm(Request $request)
    {
        return $this->repository->viewPersonalSalaryForm($request);
    }

    public function salaryFormConfirm(Request $request)
    {
        return $this->repository->salaryFormConfirm($request);
    }

    public function salaryFormView(Request $request)
    {
        return $this->repository->salaryFormView($request);
    }

    public function fetchSalaryStatus(Request $request)
    {
        return $this->repository->fetchSalaryStatus($request);
    }
//salaryFormView
}

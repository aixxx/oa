<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\FinanceLogRepository;
use Illuminate\Http\Request;

class FinanceDepartmentController extends BaseController
{
    /**
     * @var FinanceRepository
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(FinanceLogRepository::class);
    }
    //部门财务-首页
    public function index(Request $request){

        return $this->repository->financeDepartmentIndex($request);
    }
    //部门财务-列表
    public function list(Request $request){
        return $this->repository->financeDepartmentList($request);
    }
    //部门列表-首页统计
    public function deptStatistics(Request $request){
        return $this->repository->deptStatistics($request);
    }

}

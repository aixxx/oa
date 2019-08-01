<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\SalaryRepository;
use Request;
use Auth;

class SalaryController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(SalaryRepository::class);
    }

    public function index()
    {
        return $user = $this->repository->query()->get();
    }

    /**
     * @description 创建薪资模板
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function templatecreate()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->repository->salaryCreateTemplate($all, $user);
    }

    /**
     * @description 修改薪资模板
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function templateedit()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->repository->salaryEditTemplate($all, $user);
    }

    /**
     * @description 薪资模板列表
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function templatelists()
    {
        $user = Auth::user();
        return $this->repository->fetchSalaryTemplates($user);
    }

    /**
     * @description 获取薪资模板
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function templateget()
    {
        $id = Request::get('id');
        $user = Auth::user();
        return $this->repository->fetchSalaryTemplate($id, $user);
    }

    /**
     * @description 添加薪资数据
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function adddata()
    {
        $data = Request::all();
        $user = Auth::user();
        return $this->repository->salaryCreateData($data, $user);
    }

    public function getsalaryfield()
    {
        $user = Auth::user();
        return $this->repository->getSalaryField($user);
    }

    public function calculatingsalary()
    {
        $user = Auth::user();
        return $this->repository->calculatingSalary($user);
    }

    public function getuserssalary()
    {
        return $this->repository->getUserSalary(array('1791', '1792'));
    }
}

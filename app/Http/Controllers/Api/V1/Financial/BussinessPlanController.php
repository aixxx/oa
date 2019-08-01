<?php

namespace App\Http\Controllers\Api\V1\Financial;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Financial\BussinessPlanRepository;
use Illuminate\Http\Request;
use Auth;
use App\Constant\ConstFile;

class BussinessPlanController extends BaseController
{
    protected $repository;

    public function __construct(BussinessPlanRepository $repository)
    {
        $this->repository = $repository;
    }


    /*
     * 添加修改计划
     * */
    public function editBussinessPlan(Request $request){
        $user = Auth::user();
        return $this->repository->editBussinessPlan($user, $request->all());
    }


    /*
     * 获取修改计划详情
     * */
    public function editPlanInfo(Request $request){
        $user = Auth::user();
        return $this->repository->editPlanInfo($user, $request->all());
    }


    /*
     * 计划列表
     * */
    public function planList(Request $request){
        $user = Auth::user();
        return $this->repository->planList($user, $request->all());
    }


    /*
     * 添加修改类目计划
     * */
    public function editCategoryPlan(Request $request){
        $user = Auth::user();
        return $this->repository->editCategoryPlan($user, $request->all());
    }


    /*
     * 计划列表
     * */
    public function categoryPlanList(Request $request){
        $user = Auth::user();
        return $this->repository->categoryPlanList($user, $request->all());
    }

    /*
     * 类目列表
     * */
    public function getCategoryPlanList(Request $request){
        $user = Auth::user();
        return $this->repository->getCategoryPlanList($user, $request->all());
    }
    /*
     * 获取修改的类目计划详情
     * */
    public function editCategoryPlanInfo(Request $request){
        $user = Auth::user();
        return $this->repository->editCategoryPlanInfo($user, $request->all());
    }


    /*
     * 经营计划统计
     * */
    public function planStatistics(Request $request){
        return $this->repository->planStatistics($request->all());
    }

}

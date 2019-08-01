<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SuperviseRepository;
use Auth;

class SuperviseController extends BaseController
{
    //
    public $supervise;
    public function __construct(SuperviseRepository $supervise)
    {
        $this->supervise = $supervise;
    }


    /*
     * 督办列表
     * */
    public function superviseList(Request $request){
        $user = Auth::user();
        return $this->supervise->superviseList($request->all(), $user);
    }


    /*
     * 添加取消督办
     * */
    public function addCancelSupervise(Request $request){
        $user = Auth::user();
        return $this->supervise->addCancelSupervise($request->all(), $user);
    }


    /*
     * 统计
     * */
    public function superviseStatistics(Request $request){
        $user = Auth::user();
        return $this->supervise->superviseStatistics($request->all(), $user);
    }


    /*
     * 指派督办
     * */
    public function appointSupervise(Request $request){
        $user = Auth::user();
        return $this->supervise->appointSupervise($request->all(), $user);
    }


    /*
     * 创建子任务
     * */
    public function createChildTask(Request $request)
    {
        $user = Auth::user();
        return $user = $this->supervise->createChildTask($request->all(),$user);
    }


    /*
     * 任务详情
     * */
    public function taskDetail(Request $request){
        $user = Auth::user();
        return $user = $this->supervise->taskDetail($request->all(),$user);
    }
}

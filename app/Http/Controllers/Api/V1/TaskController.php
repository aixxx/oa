<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Repositories\TaskRepository;
use Request;
use Exception;
use Response;
use Auth;


class TaskController extends BaseController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(TaskRepository::class);
    }

    //创建任务
    public function create()
    {
        $all = Request::all();
        $userinfo = Auth::user();
        return $user = $this->repository->create_task($all,$userinfo->id,$userinfo);
    }

    //待处理的接受、拒绝
    public function is_accept(){
        $info = Request::all();
        $userinfo = Auth::user();
        return $user = $this->repository->is_accept($info,$userinfo->id);
    }

    //列表
    public function task_list(){
        $info = Request::all();
        return $this->repository->task_list($info,Auth::id());
    }
    //详情
    public function task_detail(){
        $info = Request::all();
        $userinfo = Auth::user();
        return $this->repository->task_detail($info,$userinfo->id);
    }
    //添加成功信息
    public function handle(){
        $info = Request::all();
        $userinfo = Auth::user();
        return $this->repository->handle($info,$userinfo->id);
    }

    public function task_status(){
        $info = Request::all();
        $userinfo = Auth::user();
        return $this->repository->task_status($info,$userinfo->id);
    }

    public function get_type(){
        $info = Request::all();
        $userinfo = Auth::user();
        return $this->repository->get_type($info,$userinfo->id);
    }

    //搜索列表
    public function search_list(){
        $info = Request::input();
        $userinfo = Auth::user();
        return $this->repository->search_list($info,$userinfo->id);
    }

    //平均分
    public function avg_score(){
        $userinfo = Auth::user();
        return $this->repository->avg_score($userinfo->id, Request::all());
    }

    //催办
    public function urgeSave(){
        return $this->repository->urgeSave(Request::all(), Auth::id());
    }

    //申诉
    public function scoreAppeal(){
        return $this->repository->scoreAppeal(Request::all(), Auth::id());
    }

    //申诉列表
    public function scoreAppealListByUserId(){
        return $this->repository->scoreAppealListByUserId(Request::get('user_id'));
    }

    //我的考察详情
    public function taskAvgScoreByMonth(){
        return $this->repository->taskAvgScoreByMonth(Request::all(), Auth::id());
    }

    //催办byid
    public function urgeSaveById(){
        return $this->repository->urgeSaveById(Request::all(), Auth::id());
    }

    /*
     * 点击待办的通知事项， 改为已读
     * */
    public function changeReadStatus(){
        return $this->repository->changeReadStatus(Request::all(), Auth::id());
    }

}

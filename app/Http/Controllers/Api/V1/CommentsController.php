<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\CommentsRepository;

use Auth;
use Request;

class CommentsController extends BaseController
{

    protected $repository;

    //构造函数
    function __construct() {
        $this->repository = app()->make(CommentsRepository::class);
    }

    //评分
    public function acomments()
    {
        $userinfo = Auth::user();
        $user_id=$userinfo->id;
        return $user = $this->repository->addcomments($user_id);
    }
    
    //获取上月平均分
    public function ascore()
    {
        $userinfo = Auth::user();
        $user_id=$userinfo->id;
        return $user = $this->repository->avescore($user_id);
    }

    //获取评分列表
    public function myscore()
    {
        $userinfo = Auth::user();
        $user_id=$userinfo->id;
        return $user = $this->repository->getscorelist($user_id);
    }

    //根据任务id查出此任务的评分
    public function getoscore()
    {
        return $user = $this->repository->getscorebyoid();
    }

    public function total_comments(){
        $info = Request::all();
        $userinfo = Auth::user();
        $user_id=$userinfo->id;
        return $user = $this->repository->total_comments($info,$user_id);
    }

    public function comment_list(){
        $info = Request::all();
        return $user = $this->repository->comment_list($info);
    }

}
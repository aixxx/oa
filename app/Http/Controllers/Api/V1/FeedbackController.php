<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Repositories\EntryRepository;
use App\Repositories\UsersRepository;
use App\Repositories\Feedback\FeedbackRespository;
use Auth;

class FeedbackController extends BaseController
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;


    //构造函数
    function __construct()
    {
        $this->respository = app()->make(FeedbackRespository::class);
    }

    // 反馈列表
    public function feed()
    {
        // 获取本人用户的信息
        $user = Auth::user();
        $uid = $user->id;
        return $user = $this->respository->feedbacklist($uid);
    }
    // 反馈所有列表
    public function getFeedbackList()
    {
        // 获取本人用户的信息
        $user = Auth::user();
        $uid = $user->id;
        return $user = $this->respository->getFeedbackList($uid);
    }

    // 反馈写入页
    public function feedbackedit()
    {
        // 获取本人用户的信息
        $user = Auth::user();
        $uid = $user->id;
        return $user = $this->respository->feedbackedit($uid);
    }

    // 反馈详情页
    public function feedbackdetail()
    {
        return $user = $this->respository->feedbackdetail();
    }

    // 反馈回复/评论
    public function reply()
    {
        // 获取本人用户的信息
        $user = Auth::user();
        return $user = $this->respository->reply($user);
    }
}
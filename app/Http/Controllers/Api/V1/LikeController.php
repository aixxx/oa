<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\LikeRepository;
use Illuminate\Http\Request;
use Auth;


class LikeController extends BaseController
{
    protected $like;

    //构造函数
    function __construct(LikeRepository $like) {
        $this->like = $like;
    }


    /*
     * 添加取消点赞
     * */
    public function addLike(Request $request){
        $user = Auth::user();
        return $this->like->addLike($request->all(), $user);
    }


    /*
     * 点赞列表
     * */
    public function likeList(Request $request){
        return $this->like->likeList($request->all());
    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\VoteRepository;
use Request;
use Auth;

class VoteController extends BaseController
{
    /**
     * @var mixed
     */
    protected $respository;

    public function __construct()
    {
        $this->respository = app()->make(VoteRepository::class);
    }

    public function index()
    {
    }

    /**
     * @description 投票创建或修改
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function createoreditvote()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->respository->createOrEditVote($all, $user);
    }

    /**
     * @description 投票操作
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function votingoperation()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->respository->votingOperation($all, $user);
    }

    /**
     * @description 投票列表
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getvotelist()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->respository->getvotelist($all, $user);
    }

    /**
     * @description 投票详情
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function show()
    {
        $user = Auth::user();
        $id = Request::get('id');
        $act = Request::get('act');
        switch ($act) {
            case 'message':
                return $user = $this->respository->getVoteInfoUp($id, $user);
            default;
                return $user = $this->respository->getVoteInfo($id, $user);
        }
    }

    /**
     * @description 获取投票初始化数据
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function voteinitialise()
    {
        return $user = $this->respository->voteInitialise();
    }

    /**
     * @description 获取投票选项
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function voteoption()
    {
        $id = Request::get('id');
        $user = Auth::user();
        return $user = $this->respository->getVoteOption($id, $user);
    }
}

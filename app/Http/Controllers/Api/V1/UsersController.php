<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\UsersRepository;
use Auth;
use Illuminate\Http\Request;

class UsersController extends BaseController
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;

    //构造函数
    function __construct()
    {
        $this->respository = app()->make(UsersRepository::class);
    }

    /**
     * @deprecated 查询
     */
    public function userFiles(Request $request)
    {
        $users = Auth::user();
        $id = $request->get('user_id');
        if ($id) {
            $user_id = $id;
        } else {
            $user_id = $users->id;
        }
        return $this->respository->userFilesQuery($user_id);
    }

    /**
     * @deprecated 添加与编辑
     */
    public function userFilesCreate(Request $request)
    {
        $users = Auth::user();
        $id = $request->get('user_id');
        if ($id) {
            $user_id = $id;
        } else {
            $user_id = $users->id;
        }
        return $this->respository->userDetailInfo($request, $user_id);
    }

    /**
     * @deprecated 添加与编辑
     */
    public function userUrgentEdit(Request $request)
    {
        $users = Auth::user();
        $id = $request->get('user_id');
        if ($id) {
            $user_id = $id;
        } else {
            $user_id = $users->id;
        }
        return $this->respository->userDankCard($request, $user_id);
    }

    /**
     * @deprecated 添加与编辑
     */
    public function userFamilyEdit(Request $request)
    {
        $users = Auth::user();
        $id = $request->get('user_id');
        if ($id) {
            $user_id = $id;
        } else {
            $user_id = $users->id;
        }
        return $this->respository->userFamily($request, $user_id);
    }

    /**
     * @deprecated 家庭删除
     */
    public function userDelete()
    {
        $users = Auth::user();
        return $this->respository->userFamilyDelete($users->id);
    }


    /**
     * @deprecated 名片展示
     */
    public function userCard(Request $request)
    {
        $users = Auth::user();
        $id = $request->get('user_id');
        if (!empty($id) && isset($id)) {
            $id = $request->get('user_id');
        } else {
            $id = $users->id;
        }
        return $this->respository->userCard($id);
    }

    /**
     * @deprecated 基本信息修改
     */
    public function profileEdit(Request $request)
    {
        $users = Auth::user();
        $id = $request->get('user_id');
        if (!empty($id) && isset($id)) {
            $id = $request->get('user_id');
        } else {
            $id = $users->id;
        }
        return $this->respository->userCardEdit($request, $id);
    }

    public function isNoPercent(Request $request)
    {
        $users = Auth::user();
        return $this->respository->isNoPercent($request, $users->id);
    }

}
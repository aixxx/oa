<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Repositories\Addwork\AddworkRespository;
use Illuminate\Http\Request;
use App\Repositories\EntryRepository;
use App\Repositories\UsersRepository;
use Auth;
use PhpParser\Node\Stmt\Foreach_;

class AddworkController extends BaseController
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;

    //构造函数
    function __construct() {
        $this->respository = app()->make(AddworkRespository::class);
    }

    // 加班字段呈现
    public function addwork_field(){
        // 获取本人用户的id
        $user = Auth::user();
        $id = $user->id;

        // 获取本人用户的公司id
        $userRespository = app()->make(UsersRepository::class);
        $company = $userRespository->getAllDept('',$user); // 此时为数组


        $rules = AttendanceApiStaff::getUserAttendanceRule($user); // 获取加班规则

        $arr['user_id'] = $id;
        $arr['company_id'] = $company['id'];
        $arr['rules'] = $rules;
        return $user = $this->respository->addwork_field($arr);
    }

    // 加班申请写入
    public function addworks()
    {
        // 获取本人用户的id
        $user = Auth::user();
        $uid = $user->id;

        // 获取本人用户的公司id
        $userRespository = app()->make(UsersRepository::class);
        $company = $userRespository->getAllDept('',$user); // 此时为数组

        // 获取本人用户的部门id
        $userRespository = app()->make(UsersRepository::class);
        $department = $userRespository->getCurrentDept($user); // 此时为数组

        $arr['user_id'] =  $uid;
        $arr['department_id'] =  $department['auto_id'];
        $arr['company_id'] =  $company['id'];

        return $user = $this->respository->addwork($arr);
    }

    // 加班申请列表-提交人视角
    public function addwork_list_submit()
    {
        // 获取本人用户的id
        $user = Auth::user();
        $uid = $user->id;

        return $user = $this->respository->addwork_list_submit($uid);
    }

    // 加班申请列表-审批人视角
    /*public function addwork_list_audit()
    {
        // 获取本人用户的id
        $user = Auth::user();
        $uid = $user->id;

        // 获取本人用户的公司的id
        $userRespository = app()->make(UserRespository::class);
        $company = $userRespository->getAllDept('',$uid);
        $company_id = $company['id'];

        $arr['user_id'] =  $uid;
        $arr['company_id'] =  $company_id;

        return $user = $this->respository->addwork_list_audit($arr);
    }*/

    // 加班申请详情
    public function detail(){
        // 获取本人用户的id
        $user = Auth::user();
        $uid = $user->id;

        return $user = $this->respository->detail($uid);
    }

    // 加班申请详情-审批人视角
    /*public function detail_audit(){
        // 获取本人用户的id
        $user = JWTAuth::parseToken()->authenticate();
        $uid = $user->id;

        return $user = $this->respository->detail_audit($uid);
    }*/

    // 加班申请审批
    public function audit(){
        // 获取本人用户的id
        $user = Auth::user();

        return $user = $this->respository->audit($user);
    }

    // 加班申请撤销
    public function revocation(){
        return $user = $this->respository->revocation();
    }

    // 加班历史记录列表
    public function history_list(){
        // 获取本人用户的id
        $user = Auth::user();
        $uid = $user->id;
        return $user = $this->respository->history_list($uid);
    }

    // 加班申请评论写入
    public function comment(){
        // 获取本人用户的id
        $user =Auth::user();
        return $user = $this->respository->comment($user);
    }
}
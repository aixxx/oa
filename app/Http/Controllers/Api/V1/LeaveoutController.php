<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\LeaveoutRepository;

use Auth;
use Request;

class LeaveoutController extends BaseController
{
   protected $repository;
   
    public function __construct(){
        $this->repository = app()->make(LeaveoutRepository::class);
    }
    //获取公司选择字段
    public function leaveout_field(){
       
        $data=Request::all();

        return $this->repository->leaveout_field();
    }


    //添加外出申请
    public function create_leaveout(){
        $data=Request::all();
        $userinfo = Auth::user();
        return $this->repository->create_leaveout($data,$userinfo->id);
    }
    //已提交 待审核页面
    public function leaveout_check(){
        $data=Request::all();
        $userinfo = Auth::user();
        return $this->repository->leaveout_check($data,$userinfo->id);
    }
    //申请人/人事 外出记录
    public function leaveout_list(){
        $data=Request::all();
        $userinfo =Auth::user();
        return $this->repository->leaveout_list($data,$userinfo->id);
    }
    //审批外出申请
    public function leaveout_shenpi(){
        $data=Request::all();
        $userinfo =Auth::user();
        return $this->repository->leaveout_shenpi($data,$userinfo->id);
    }

    //撤销自己的外出申请
    public function revoke_leaveout(){
        $data=Request::all();
        $userinfo = Auth::user();
        return $this->repository->revoke_leaveout($data,$userinfo->id);
    }
//    //评论申请
//    public function comment_leaveout(){
//        $data=Request::all();
//        $userinfo = Auth::user();
//        return $this->repository->revoke_leaveout($data,$userinfo);
//    }

    //申请人 外勤详情
    public function leaveout_detail(){
        $data=Request::all();
        $userinfo = Auth::user();
        return $this->repository->leaveout_detail($data,$userinfo->id);
    }
}
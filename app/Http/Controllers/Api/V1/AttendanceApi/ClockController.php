<?php

namespace App\Http\Controllers\Api\V1\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\User;
use App\Repositories\AttendanceApi\AttendanceApiClockRespository;
use App\Repositories\AttendanceApi\AttendanceApiCountRespository;
use App\Repositories\AttendanceApi\ClockRespository;
use Request;
use Auth;

class ClockController extends BaseController
{
    public $respository;
    //构造函数
    function __construct()
    {
        $this->respository = app()->make(ClockRespository::class);
    }


    /**
     *   考勤打卡
     */
    public function clock(){
        $user = Auth::user();//::parseToken()->authenticate();
        $clockRespository = app()->make(AttendanceApiClockRespository::class);
        return $data = $clockRespository->clock(Request::all(), $user);
    }

    //根据用户ID获取考勤规则
    public function rules(){
        //$user = Auth::user();
        $data = Request::all();
        if(isset($data['user_id']) && intval($data['user_id'])){
            $user = User::find($data['user_id']);
        }else{
            $user = Auth::user();
        }
        return $this->respository->rules($user);
    }

    //根据日期获取打卡接口
    public function clockInfo(){
        $data = Request::all();
        if(isset($data['user_id']) && intval($data['user_id'])){
            $user = User::find($data['user_id']);
        }else{
            $user = Auth::user();
        }
        return $this->respository->clockInfo($user, $data);
    }

    public function UpdateUserClockForHr(){
        return $this->respository->UpdateUserClockForHr(Request::all());
    }
}
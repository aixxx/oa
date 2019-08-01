<?php

namespace App\Http\Controllers\Api\V1\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Helpers\Dh;
use App\Models\User;
use App\Repositories\AttendanceApi\AttendanceApiCountRespository;
use App\Repositories\AttendanceApi\ClockRespository;
use App\Repositories\AttendanceApi\CountsRespository;
use App\Services\AttendanceApi\CountsService;
use Carbon\Carbon;
use Request;
use Validator;

class CountsController extends BaseController
{
    public $respository;
    //构造函数
    function __construct()
    {
        $this->respository = app()->make(CountsRespository::class);
    }


    /*
     * 获取统计基本信息
     * */
    public function countBasicInfo($user_id){
        $countRespository = app()->make(AttendanceApiCountRespository::class);
        $user = User::find($user_id);
        if(empty($user)){
            return returnJson('用户不存在', ConstFile::API_RESPONSE_FAIL);
        }
        return $countRespository->countBasicInfo($user);
    }

    public function oneDayForHr($user_id){
        $user_id = intval($user_id);
        if(!$user_id) return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);

        $user = User::find($user_id);
        return $this->respository->oneDayForHr($user, Request::all());
    }

    public function oneMonthForHr($user_id){
        $user_id = intval($user_id);
        if(!$user_id) return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);

        $user = User::find($user_id);
        return $this->respository->oneMonthForHr($user, Request::all());
    }

    public function allMonthForHr(){
        return $this->respository->allMonthForHr(Request::all());
    }

    public function getAnomalyByType(){
        return $this->respository->getAnomalyByType(Request::all());
    }

    public function alldayForHr(){
        $data = Request::all();
        $validator = Validator::make($data,['dates' => 'required|date_format:Y-m-d']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(),
                ConstFile::API_RESPONSE_FAIL);
        }
        return $this->respository->alldayForHr(Request::all());

    }


    public function alldayClockInfoForHr(){
        $data = Request::all();
        $validator = Validator::make($data,['dates' => 'required|date_format:Y-m-d']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(),
                ConstFile::API_RESPONSE_FAIL);
        }
        return $this->respository->alldayClockInfoForHr(Request::all());

    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Repositories\AttendanceApi\CountsRespository;
use Request;
use Auth;

class AttendanceApiCountController extends BaseController
{
    //构造函数
    function __construct()
    {

    }

    //个人 - 一天
    public function countUserForDay($id){
        //参数验证
        $id = intval($id);
        if(!$id) return returnJson('ID错误', ConstFile::API_RESPONSE_FAIL);

        $respository = app()->make(CountsRespository::class);
        return $respository->userForDay(User::find($id), Request::all());
    }
}
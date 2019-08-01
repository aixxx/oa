<?php
/**
 * Created by PhpStorm.
 * User: chenzhikui
 * Date: 2019/4/9
 * Time: 2:14 PM
 */

namespace App\Http\Controllers\Api\V1\Attendance;


use App\Http\Controllers\Api\V1\ApiController;
use App\Services\AttendanceApi\AttendanceApiService;
use Illuminate\Http\Request;

class DiffWorkTimeController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function run(Request $request){

        $user_id = \Auth::id();
        $begin = $request->input('begin');
        $end = $request->input('end');
        $res = AttendanceApiService::getWorkTimeByUserAndDate($user_id, $begin, $end);
        $cnt = 0;
        foreach ($res as $re){
            $cnt += ceil($re['times']/60);  //分钟数换成小时
        }
        return $cnt;
    }

}
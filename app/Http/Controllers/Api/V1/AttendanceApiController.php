<?php

namespace App\Http\Controllers\Api\V1;

use App\Constant\ConstFile;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\AttendanceApi\AttendanceApiOvertimeRule;
use App\Models\User;
use App\Models\Vacations\UserVacation;
use App\Models\Vacations\VacationExtraWorkflowPass;
use App\Models\Vacations\VacationOutSideRecord;
use App\Models\Workflow\Proc;
use App\Repositories\AttendanceApi\AttendanceApiCountRespository;
use App\Repositories\AttendanceApi\AttendanceApiOvertimeRuleRespository;
use App\Repositories\AttendanceApi\AttendanceApiRespository;
use App\Repositories\AttendanceApi\AttendanceApiClassesRespository;
use App\Repositories\AttendanceApi\AttendanceApiCycleRespository;
use App\Repositories\AttendanceApi\AttendanceApiSchedulingRespository;
use App\Repositories\AttendanceApi\AttendanceApiClockRespository;
use App\Services\AttendanceApi\AttendanceApiService;
use Carbon\Carbon;
use Request;
use Auth;

class AttendanceApiController extends BaseController
{
    //构造函数
    function __construct() {

    }


    /**
    *   获取考勤人员信息
     */
    public function getAttendanceApiDepartment(){
        $user = Auth::user();
        $respository = app()->make(AttendanceApiRespository::class);
        return $data = $respository->getAttendanceDepartment(Request::all(), $user);
    }

    /**
     *   获取考勤组列表
     */
    public function getAttendanceApi(){
        $user = Auth::user();
        $respository = app()->make(AttendanceApiRespository::class);
        return $data = $respository->getList(Request::all(), $user);
    }

    /*
     * 是否参与其他考勤组
     * */
    public function isUser(){
        $respository = app()->make(AttendanceApiRespository::class);
        return $data = $respository->isUser(Request::get('ids'), Request::get('attendance_id'));
    }

    public function isDepartment(){
        $respository = app()->make(AttendanceApiRespository::class);
        return $data = $respository->isDepartment(Request::get('ids'), Request::get('attendance_id'));
    }

    /**
    *   添加考勤组
     */
    public function addAttendanceApi(){
        $user = Auth::user();
        $respository = app()->make(AttendanceApiRespository::class);
        return $data = $respository->addAttendance(Request::all(), $user);
    }

    /**
    *   根据ID查看
     */
    public function getAttendanceApiById($id){
        $user = Auth::user();
        $respository = app()->make(AttendanceApiRespository::class);
        return $data = $respository->getAttendanceById($id, $user);
    }

    /**
    *   编辑考勤
     */
    public function updateAttendanceApi($id){
        $user = Auth::user();
        $respository = app()->make(AttendanceApiRespository::class);
        return $data = $respository->updateAttendance($id, Request::all(), $user);
    }

    /**
    *   删除考勤
     */
    public function delAttendanceApi($id){
        if(intval($id) == 1)
            return returnJson("默认考勤组不能删除",ConstFile::API_RESPONSE_FAIL);
        $user = Auth::user();
        $respository = app()->make(AttendanceApiRespository::class);
        return $data = $respository->delAttendance($id, $user);
    }

    /**
    *   班次列表
     */
    public function getClasses(){
        $user = Auth::user();
        $classesRespository = app()->make(AttendanceApiClassesRespository::class);
        return $data = $classesRespository->getList(Request::all(), $user);
    }

    /**
     *   根据ID查看
     */
    public function getClassesById($id){
        $user = Auth::user();
        $classesRespository = app()->make(AttendanceApiClassesRespository::class);
        return $data = $classesRespository->getClassesById($id, $user);
    }

    /**
     *   添加班次
     */
    public function addClasses(){
        $user = Auth::user();
        $classesRespository = app()->make(AttendanceApiClassesRespository::class);
        return $data = $classesRespository->addClasses(Request::all(), $user);
    }

    /**
    *   修改班次
     */
    public function updateClasses($id){
        $user = Auth::user();
        $classesRespository = app()->make(AttendanceApiClassesRespository::class);
        return $data = $classesRespository->updateClasses($id, Request::all(), $user);
    }

    /**
    *   删除
     */
    public function delClassesById($id){
        if(intval($id) == 1)
            return returnJson("默认班次不能删除",ConstFile::API_RESPONSE_FAIL);
        $user = Auth::user();
        $classesRespository = app()->make(AttendanceApiClassesRespository::class);
        return $data = $classesRespository->delClassesById($id, $user);
    }

    /**
    *   新增周期
     */
    public function addCycle(){
        $user = Auth::user();
        $cycleRespository = app()->make(AttendanceApiCycleRespository::class);
        return $data = $cycleRespository->addCycle(Request::all(), $user);
    }

    /**
     *   编辑周期
     */
    public function updateCycle($id){
        $user = Auth::user();
        $cycleRespository = app()->make(AttendanceApiCycleRespository::class);
        return $data = $cycleRespository->updateCycle($id, Request::all(), $user);
    }

    /**
    *   查询周期
     */
    public function getCycleById($id){
        $cycleRespository = app()->make(AttendanceApiCycleRespository::class);
        return $data = $cycleRespository->getCycleById($id);
    }

    /**
    *   排班表
     */
    public function schedulingAction($attendance_id){
        $user = Auth::user();
        $schedulingRespository = app()->make(AttendanceApiSchedulingRespository::class);
        return $data = $schedulingRespository->schedulingAction($attendance_id, Request::all(), $user);
    }

    public function getOvertimeRuleList(){
        $list = AttendanceApiOvertimeRule::query()->get();
        return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    public function getOvertimeRuleInfo(){
        $info = AttendanceApiOvertimeRule::query()->find(Request::get('id'));
        return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $info);
    }

    public function DelOvertimeRule(){
        $id = Request::get('id');
        if($id == 1)
            returnJson('默认规则不允许删除', ConstFile::API_RESPONSE_FAIL);
        $res = AttendanceApiOvertimeRule::query()
            ->where('id', $id)
            ->delete();
        return $res
            ? returnJson('操作成功', ConstFile::API_RESPONSE_SUCCESS)
            : returnJson('操作失败', ConstFile::API_RESPONSE_FAIL);
    }

    public function addOvertimeRule(){
        $data = Request::all();
        if(isset($data['id']) && $data['id']){
            return app()->make(AttendanceApiOvertimeRuleRespository::class)
                ->updateOvertimeRule($data['id'], $data);
        }else{
            return app()->make(AttendanceApiOvertimeRuleRespository::class)
                ->addOvertimeRule($data);
        }
    }
}

<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Requests\AttendanceApi\AttendanceApiOvertimeRuleRequest;
use App\Models\AttendanceApi\AttendanceApiOvertimeRule;
use DB;
use \Exception;
use App\Repositories\Repository;

class AttendanceApiOvertimeRuleRespository extends Repository {

    public function model() {
        return AttendanceApiOvertimeRule::class;
    }

    /**
     *   添加加班规则
     */
    public function addOvertimeRule($data){
        try{
            //验证
            $check_result = app()->make(AttendanceApiOvertimeRuleRequest::class)
                ->attendanceOvertimeRuleValidatorForm($data);
            if($check_result !== true) return $check_result;
            $overtime_rule_info = AttendanceApiOvertimeRule::query()->create($data);
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=> $overtime_rule_info->id]);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     *   修改加班规则
     */
    public function updateOvertimeRule($id, $data){
        try{
            //验证
            $check_result = app()->make(AttendanceApiOvertimeRuleRequest::class)
                ->attendanceOvertimeRuleValidatorForm($data);
            if($check_result !== true) return $check_result;
            unset($data['s']);
            AttendanceApiOvertimeRule::query()->where('id', $id)->update($data);
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=> $id]);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}

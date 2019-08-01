<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Models\AttendanceApi\AttendanceApiScheduling;
use Carbon\Carbon;
use DB;
use \Exception;
use App\Repositories\Repository;

class AttendanceApiSchedulingRespository extends Repository {

    public function model() {
        return AttendanceApiScheduling::class;
    }

    /**
    *   设置排班
     */
    public function schedulingAction($attendance_id, $data, $user_id){
        try{
            $attendance_id = intval($attendance_id);
            if(!$attendance_id) return returnJson("考勤组ID错误", ConstFile::API_RESPONSE_FAIL);
            $list = [];
            $YmdHis = date('Y-m-d H:i:s');
            foreach ($data['data'] as $k=>$v) {
                foreach ($v['scheduling'] as $k1=>$v1){
                    $list[] = [
                        'attendance_id' => $attendance_id,
                        'user_id' => $v['user_id'],
                        'dates' => date('Y-m-d', strtotime($v1['dates'])),
                        'classes_id' => intval($v1['classes_id']),
                        'take_effect_dates' => $data['take_effect_dates'],
                        'created_at' => $YmdHis,
                        'updated_at' => $YmdHis,
                        'admin_id' => $user_id,
                    ];
                }
            }
            DB::transaction(function() use($list, $attendance_id){
                AttendanceApiScheduling::query()->where('attendance_id',$attendance_id)->delete();
                AttendanceApiScheduling::query()->insert($list);
            });
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}

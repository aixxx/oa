<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Requests\AttendanceApi\AttendanceApiCycleRequest;
use App\Models\AttendanceApi\AttendanceApiCycle;
use App\Models\AttendanceApi\AttendanceApiCycleContent;
use DB;
use \Exception;
use App\Repositories\Repository;

class AttendanceApiCycleRespository extends Repository {

    public function model() {
        return AttendanceApiCycle::class;
    }

    /**
    *   增加周期
     */
    public function addCycle($data, $user){
        try{
            $check_result = app()->make(AttendanceApiCycleRequest::class)->attendanceCycleValidatorForm($data);
            if($check_result !== true) return $check_result;

            $data['admin_id'] = $user->id;
            $cycle_id = 0;
            DB::transaction(function() use($data, &$cycle_id){
                $cycle = AttendanceApiCycle::create($data);
                $cycle_id = $cycle->id;
                $this->addCycleContent($data['content'], $cycle_id);
            });
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=> $cycle_id]);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
    *   添加周期详细信息
     */
    public function addCycleContent($data, $cycle_id){
        $content = [];
        $YmdHis = date('Y-m-d H:i:s');
        foreach ($data as $k=>$v) {
            $content[] = [
                'sort' => $k + 1,
                'cycle_id' => $cycle_id,
                'classes_id' => $v,
                'created_at' => $YmdHis,
                'updated_at' => $YmdHis,
            ];
        }
        AttendanceApiCycleContent::query()->insert($content);
    }

    /**
     *   修改周期
     */
    public function updateCycle($id, $data, $user){
        try{
            $id = intval($id);
            if(!$id) return returnJson('ID错误', ConstFile::API_RESPONSE_FAIL);

            $check_result = app()->make(AttendanceApiCycleRequest::class)->attendanceCycleValidatorForm($data);
            if($check_result !== true) return $check_result;

            $data['admin_id'] = $user->id;
            DB::transaction(function() use($data, $id){
                $list = [
                    'type' => $data['type'],
                    'title' => $data['title'],
                    'cycle_days' => $data['cycle_days'],
                ];
                AttendanceApiCycle::query()->where('id', $id)->update($list);
                AttendanceApiCycleContent::query()->where('cycle_id', $id)->delete();
                $this->addCycleContent($data['content'], $id);
            });
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=> $id]);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
    *   查询周期
     */
    public function getCycleById($id){
        $id = intval($id);
        if(!$id) return returnJson('ID错误', ConstFile::API_RESPONSE_FAIL);
        $data = AttendanceApiCycle::query()->with('content.classes')->find($id);
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }
}

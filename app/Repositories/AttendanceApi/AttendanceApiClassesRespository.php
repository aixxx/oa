<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Http\Requests\AttendanceApi\AttendanceApiClassesRequest;
use App\Models\AttendanceApi\AttendanceApiClasses;
use App\Repositories\Repository;
use App\Services\AttendanceApi\AttendanceApiService;
use Carbon\Carbon;
use \Exception;

class AttendanceApiClassesRespository extends Repository {

    public function model() {
        return AttendanceApiClasses::class;
    }

    /**
     *   获取班次列表
     */
    public function getList($data, $user){
        try{
            $data = AttendanceApiClasses::query()->get();
            if($data->isEmpty())
                return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $data);

            $data = $data->toArray();
            foreach ($data as $k=>$v){
                $total = 0;
                for($i = 1; $i <= $v['type']; $i++){
                    if($v["work_time_end{$i}"] <= $v["work_time_begin{$i}"]){
                        $begin = Carbon::parse($v["work_time_begin{$i}"]);
                        $end = Carbon::parse($v["work_time_end{$i}"])->addDay();
                        $total += $end->diffInHours($begin);
                    }else{
                        $begin = Carbon::parse($v["work_time_begin{$i}"]);
                        $end = Carbon::parse($v["work_time_end{$i}"]);
                        $total += $end->diffInHours($begin);
                    }
                }
                if($v['is_siesta'] == AttendanceApiService::ATTENDANCE_CLASSES_SIESTA){
                    $total -= Carbon::parse($v["end_siesta_time"])
                        ->diffInHours(Carbon::parse($v["begin_siesta_time"]));
                }
                $data[$k]['total'] = $total;
            }
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);

        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
    *   根据ID查看
     */
    public function getClassesById($id){
        try{
            $id = intval($id);
            $data = AttendanceApiClasses::query()->find($id);
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);

        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     *   添加班次
     */
    public function addClasses($data, $user){
        try{
            //验证
            $check_result = app()->make(AttendanceApiClassesRequest::class)->attendanceClassesValidatorForm($data);
            if($check_result !== true) return $check_result;

            $data['admin_id'] = $user->id;
            self::setEndTime($data);
            AttendanceApiClasses::create($data);

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
    *   修改班次
     */
    public function updateClasses($id, $data, $user){
        try{
            //验证
            $id = intval($id);
            $check_result = app()->make(AttendanceApiClassesRequest::class)->attendanceClassesValidatorForm($data);
            if($check_result !== true) return $check_result;

            $data['admin_id'] = $user->id;
            self::setEndTime($data);
            unset($data['s']);
            AttendanceApiClasses::where('id', $id)->update($data);

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }



    public static function setEndTime(&$data){
        $classes_request = app()->make(AttendanceApiClassesRequest::class);
        switch ($data['type']){
            case AttendanceApiService::ATTENDANCE_CLASSES_ONE:
                $time1 = $classes_request->getAnomalyTime($data['work_time_begin1'],$data['work_time_end1'],
                    $data['clock_time_begin1'],$data['clock_time_end1']);
                $data['work_time_end1'] = $time1['end']->toTimeString();
                break;
            case AttendanceApiService::ATTENDANCE_CLASSES_TWO:
                $time1 = $classes_request->getAnomalyTime($data['work_time_begin1'],$data['work_time_end1'],
                    $data['clock_time_begin1'],$data['clock_time_end1']);
                $data['work_time_end1'] = $time1['end']->toTimeString();
                $time2 = $classes_request->getAnomalyTime($data['work_time_begin2'],$data['work_time_end2'],
                    $data['clock_time_begin2'],$data['clock_time_end2']);
                $data['work_time_end2'] = $time2['end']->toTimeString();
                break;
            case AttendanceApiService::ATTENDANCE_CLASSES_THR:
                $time1 = $classes_request->getAnomalyTime($data['work_time_begin1'],$data['work_time_end1'],
                    $data['clock_time_begin1'],$data['clock_time_end1']);
                $data['work_time_end1'] = $time1['end']->toTimeString();
                $time2 = $classes_request->getAnomalyTime($data['work_time_begin2'],$data['work_time_end2'],
                    $data['clock_time_begin2'],$data['clock_time_end2']);
                $data['work_time_end2'] = $time2['end']->toTimeString();
                $time3 = $classes_request->getAnomalyTime($data['work_time_begin3'],$data['work_time_end3'],
                    $data['clock_time_begin3'],$data['clock_time_end3']);
                $data['work_time_end3'] = $time3['end']->toTimeString();
                break;
        }
    }

    /**
     *   删除
     */
    public function delClassesById($id, $user){
        try{
            $id = intval($id);
            if(!$id) return returnJson('ID错误', ConstFile::API_RESPONSE_FAIL);
            $data = [
                'admin_id' => $user->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ];
            $res = AttendanceApiClasses::where('id', $id)->update($data);
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

}

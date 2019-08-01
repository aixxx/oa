<?php

namespace App\Http\Requests\AttendanceApi;

use App\Http\Helpers\Dh;
use App\Services\AttendanceApi\AttendanceApiService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class AttendanceApiClassesRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }

    public function getAttendanceRules(){
        return [
            'title' => 'required',
            'code' => 'max:10',
            'type' => 'required|numeric|in:1,2,3',
            'work_time_begin1' => 'present|date_format:H:i',
            'work_time_end1' => 'present|date_format:i:s',
            'work_time_begin2' => 'present|date_format:H:i',
            'work_time_end2' => 'present|date_format:i:s',
            'work_time_begin3' => 'present|date_format:H:i',
            'work_time_end3' => 'present|date_format:i:s',
            'is_siesta' => 'required|numeric|in:1,2',
            'clock_time_begin1' => 'numeric',
            'clock_time_begin2' => 'numeric',
            'clock_time_begin3' => 'numeric',
            'clock_time_end1' => 'numeric',
            'clock_time_end2' => 'numeric',
            'clock_time_end3' => 'numeric',
            'elastic_min' => 'numeric',
            'serious_late_min' => 'numeric',
            'absenteeism_min' => 'numeric',
        ];
    }

    public function getAttendanceMessage(){
        return [
            'title.required' => '班次名称不能为空',
            'code.max' => '编号长度最多10位',
            'type.required' => '上下班类别不能为空',
            'type.numeric' => '上下班类别格式错误',
            'type.in' => '上下班类别参数错误',
            'work_time_begin1.required' => '上班时间1不能为空',
            'work_time_begin1.date_format' => '上班时间1格式错误: H:i',
            'work_time_begin2.date_format' => '上班时间2格式错误: H:i',
            'work_time_begin3.date_format' => '上班时间3格式错误: H:i',

            'work_time_end1.required' => '下班时间1不能为空',
            'work_time_end1.date_format' => '下班时间1格式错误: H:i',
            'work_time_end2.date_format' => '下班时间2格式错误: H:i',
            'work_time_end3.date_format' => '下班时间3格式错误: H:i',

            'is_siesta.required' => '是否开启午休不能为空',
            'is_siesta.numeric' => '是否开启午休格式错误',
            'is_siesta.in' => '是否开启午休参数错误',
            'begin_siesta_time.required' => '午休开始时间不能为空',
            'begin_siesta_time.date_format' => '午休开始时间格式错误: H:i',
            'end_siesta_time.required' => '午休结束时间不能为空',
            'end_siesta_time.date_format' => '午休结束时间格式错误: H:i',

            'clock_time_begin1.required' => '打卡时间1段不能为空',
            'clock_time_begin1.date_format' => '打卡时间1段格式错误',
            'clock_time_begin2.required' => '打卡时间2段不能为空',
            'clock_time_begin2.date_format' => '打卡时间2段格式错误',
            'clock_time_begin3.required' => '打卡时间3段不能为空',
            'clock_time_begin3.date_format' => '打卡时间3段格式错误',

            'clock_time_end1.required' => '打卡时间1段不能为空',
            'clock_time_end1.date_format' => '打卡时间1段格式错误',
            'clock_time_end2.required' => '打卡时间2段不能为空',
            'clock_time_end2.date_format' => '打卡时间2段格式错误',
            'clock_time_end3.required' => '打卡时间3段不能为空',
            'clock_time_end3.date_format' => '打卡时间3段格式错误',
            'elastic_min.numeric' => '弹性标准格式错误',
            'serious_late_min.numeric' => '严重迟到标准格式错误',
            'absenteeism_min.numeric' => '旷工标准格式错误',
        ];
    }

    public function attendanceClassesValidatorForm($data){
        $rules = $this->getAttendanceRules();
        $messages = $this->getAttendanceMessage();

        $validator = Validator::make($data, $rules, $messages);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }

        if($data['is_siesta'] == AttendanceApiService::ATTENDANCE_CLASSES_SIESTA){
            //开启午休， 必须设置午休时间
            $rules['begin_siesta_time'] = 'required|date_format:H:i:s';
            $rules['end_siesta_time'] = 'required|date_format:H:i:s';
        }
        if($data['type'] == AttendanceApiService::ATTENDANCE_CLASSES_ONE){
            //一天 一次上下班
            $time1 = $this->getAnomalyTime($data['work_time_begin1'],$data['work_time_end1'],
                $data['clock_time_begin1'],$data['clock_time_end1']);
            //dd($time1);
            if($time1['begin']->gte($time1['end'])) {
                return returnJson('上下班时间冲突', ConstFile::API_RESPONSE_FAIL);
            }
        }elseif ($data['type'] == AttendanceApiService::ATTENDANCE_CLASSES_TWO){
            //一天 两次次上下班
            $time1 = $this->getAnomalyTime($data['work_time_begin1'],$data['work_time_end1'],
                $data['clock_time_begin1'],$data['clock_time_end1']);
            $time2 = $this->getAnomalyTime($data['work_time_begin2'],$data['work_time_end2'],
                $data['clock_time_begin2'],$data['clock_time_end2']);

            if ($time1['begin']->gte($time1['end']))
                return returnJson('第 1 次上下班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time2['begin']->gte($time2['end']))
                return returnJson('第 2 次上下班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time1['end']->gte($time2['begin']))
                return returnJson('第 1 次下班和第 2 次上班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time1['end_clock']->gte($time2['begin_clock']))
                return returnJson('段设置错误 ：第 1 次下班和第 2 次上班时间冲突', ConstFile::API_RESPONSE_FAIL);

        }elseif ($data['type'] == AttendanceApiService::ATTENDANCE_CLASSES_THR){
            //一天 三次上下班
            $time1 = $this->getAnomalyTime($data['work_time_begin1'],$data['work_time_end1'],
                $data['clock_time_begin1'],$data['clock_time_end1']);
            $time2 = $this->getAnomalyTime($data['work_time_begin2'],$data['work_time_end2'],
                $data['clock_time_begin2'],$data['clock_time_end2']);
            $time3 = $this->getAnomalyTime($data['work_time_begin3'],$data['work_time_end3'],
                $data['clock_time_begin3'],$data['clock_time_end3']);

            if ($time1['begin']->gte($time1['end']))
                return returnJson('第 1 次上下班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time2['begin']->gte($time2['end']))
                return returnJson('第 2 次上下班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time3['begin']->gte($time3['end']))
                return returnJson('第 3 次上下班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time1['end']->gte($time2['begin']))
                return returnJson('第 1 次下班和第 2 次上班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time2['end']->gte($time3['begin']))
                return returnJson('第 2 次下班和第 3 次上班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time1['end_clock']->gte($time2['begin_clock']))
                return returnJson('段设置错误 ：第 1 次下班和第 2 次上班时间冲突', ConstFile::API_RESPONSE_FAIL);
            if ($time2['end_clock']->gte($time3['begin_clock']))
                return returnJson('段设置错误 ：第 2 次下班和第 3 次上班时间冲突', ConstFile::API_RESPONSE_FAIL);
        }

        return true;
    }

    public function getAnomalyTime($begin, $end, $clock_begin, $clock_end){
        $begin1 = Carbon::parse($begin);
        $begin1_clock = Carbon::parse($begin)->subMinutes($clock_begin);
        if($end  > "24:00"){
            $work_time_end = str_replace(':','.', $end)  - "24.00";
            $work_time_end = sprintf("%01.2f",$work_time_end);
            $end1 = Carbon::parse($work_time_end)->addDay();
            $end1_clock = Carbon::parse($work_time_end)->addDay()->addMinutes($clock_end);
        }else{
            $end1 = Carbon::parse($end);
            $end1_clock = Carbon::parse($end)->addMinutes($clock_end);
        }
        return [
            'begin' => $begin1,
            'begin_clock' => $begin1_clock,
            'end' => $end1,
            'end_clock' => $end1_clock,
        ];
    }
}

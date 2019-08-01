<?php

namespace App\Http\Requests\AttendanceApi;

use App\Constant\ConstFile;
use Illuminate\Foundation\Http\FormRequest;
use Validator;

class AttendanceApiClockRequest extends FormRequest
{

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

    public function messages(){
        return [

        ];
    }
    public function getAttendanceRules(){
        return [
            'dates' => 'required|date_format:Y-m-d',
            'type' => 'required|numeric|in:1,2',
        ];
    }

    public function getAttendanceMessage(){
        return [
            'dates.required' => '打卡日期不能为空',
            'dates.date_format' => '打卡日期格式错误',
            'type.required' => '打卡类型不能为空',
            'type.numeric' => '打卡类型必须为数字',
            'type.in' => '打卡类型参数错误',
        ];
    }

    public function attendanceClockValidatorForm($data){
        $rules = $this->getAttendanceRules();
        $messages = $this->getAttendanceMessage();

        $validator = Validator::make($data, $rules, $messages);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }

        return true;
    }

    public function attendanceClockInfoValidatorForm($data){
        $rules = [
            'dates' => 'required|date_format:Y-m-d',
        ];
        $messages = [
            'dates.required' => '打卡日期不能为空',
            'dates.date_format' => '打卡日期格式错误',
        ];

        $validator = Validator::make($data, $rules, $messages);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }

        return true;
    }

    public function attendanceUpdateClockValidatorForm($data){
        $rules = [
            'dates' => 'required|date_format:Y-m-d',
            'datetimes'=> 'required|date_format:Y-m-d',
            'type' => 'required|numeric|in:1,2',
        ];
        $messages = [
            'dates.required' => '打卡日期不能为空',
            'dates.date_format' => '打卡日期格式错误',
            'datetimes.required' => '打卡日期不能为空',
            'datetimes.date_format' => '打卡日期格式错误',
            'type.required' => '打卡类型不能为空',
            'type.numeric' => '打卡类型必须为数字',
            'type.in' => '打卡类型参数错误',
        ];

        $validator = Validator::make($data, $rules, $messages);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }

        return true;
    }
}
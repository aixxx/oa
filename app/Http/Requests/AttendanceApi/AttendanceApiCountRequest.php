<?php

namespace App\Http\Requests\AttendanceApi;

use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class AttendanceApiCountRequest extends FormRequest
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
    public function getAttendanceRules(){
        return [
            'dates' => 'required|date_format:Y-m',
        ];
    }

    public function getAttendanceMessage(){
        return [
            'dates.required' => '月份不能为空',
            'dates.date_format:Y-m' => '月份格式错误',
        ];
    }

    public function attendanceCountMyByIdValidatorForm($data){
        $validator = Validator::make($data, $this->getAttendanceRules(), $this->getAttendanceMessage());
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }

    public function attendanceCountMyDayByIdValidatorForm($data){
        $validator = Validator::make($data, ['dates' => 'required|date_format:Y-m-d',], $this->getAttendanceMessage());
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }
}
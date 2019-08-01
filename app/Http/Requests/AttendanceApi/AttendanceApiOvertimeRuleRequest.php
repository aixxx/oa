<?php

namespace App\Http\Requests\AttendanceApi;

use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class AttendanceApiOvertimeRuleRequest extends FormRequest
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
            'title' => 'required',
            'is_working_overtime' => 'required|numeric|in:1,0',
            'working_overtime_type' => 'required|numeric|in:1,2,3',
            'working_begin_time' => 'required|numeric',
            'working_min_overtime' => 'required|numeric',
            'is_rest_overtime' => 'required|numeric|in:1,0',
            'rest_overtime_type' => 'required|numeric|in:1,2,3',
            'rest_min_overtime' => 'required|numeric',
        ];
    }

    public function getAttendanceMessage(){
        return [

        ];
    }

    public function attendanceOvertimeRuleValidatorForm($data){
        $validator = Validator::make($data, $this->getAttendanceRules());
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }
}
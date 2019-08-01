<?php

namespace App\Http\Requests\AttendanceApi;

use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class AttendanceApiCycleRequest extends FormRequest
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
            'type' => 'required|numeric|in:1,2,3',
            'cycle_days' => 'required|numeric',
            'content' => 'required|array',
        ];
    }

    public function getAttendanceMessage(){
        return [
            'title.required' => '周期名称不能为空',
            'type.required' => '周期类型不能为空',
            'type.numeric' => '周期类型必须为数字',
            'type.in' => '周期类型参数错误',
            'cycle_days.required' => '周期天数不能为空',
            'cycle_days.numeric' => '周期天数必须为数字',
            'content.required' => '详细设置不能为空',
            'content.array' => '详细设置格式错误',
        ];
    }

    public function attendanceCycleValidatorForm($data){
        if(count($data['content']) != $data['cycle_days']) {
            return returnJson('参数错误: 详细设置与天数不一致', ConstFile::API_RESPONSE_FAIL);
        }
        $validator = Validator::make($data, $this->getAttendanceRules(), $this->getAttendanceMessage());
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }
}
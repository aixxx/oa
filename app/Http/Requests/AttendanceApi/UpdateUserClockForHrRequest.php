<?php

namespace App\Http\Requests\AttendanceApi;

use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class UpdateUserClockForHrRequest extends FormRequest
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
    public function getRules(){
        return [
            'user_id' => 'required|numeric',
            'classes_id' => 'required|numeric',
            'dates' => 'required|date_format:Y-m-d',
            'work_time' => 'required',
            'type' => 'required|numeric|in:1,2',
            'anomaly_type' => 'required|numeric|in:0,1,2,3,4,5',
            'anomaly_id' => 'required|numeric',
            'anomaly_time' => 'numeric',
            'clock_nums' => 'required|numeric|in:1,2,3',
        ];
    }


    public function add($data){
        $validator = Validator::make($data, $this->getRules());
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }
}
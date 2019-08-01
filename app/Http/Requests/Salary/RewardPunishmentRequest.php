<?php

namespace App\Http\Requests\Salary;

use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class RewardPunishmentRequest extends FormRequest
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
            'title' => 'required',
            'type' => 'required|numeric|in:1,2',
            'user_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'money' => 'required|numeric',
            'dates' => 'required|date_format:Y-m-d',
        ];
    }


    public function add($data){
        $validator = Validator::make($data, $this->getRules());
        if($validator->fails()){
            return returnJson($validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }

    public function addByTask($data){
        $validator = Validator::make($data, [
            'title' => 'required',
            'id' => 'required|numeric',
            'money' => 'required|numeric',
        ]);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }
}
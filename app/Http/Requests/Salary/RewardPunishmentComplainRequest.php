<?php

namespace App\Http\Requests\Salary;

use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class RewardPunishmentComplainRequest extends FormRequest
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
            'pr_id' => 'required|numeric',
            'remark' => 'required',
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
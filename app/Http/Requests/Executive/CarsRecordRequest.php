<?php

namespace App\Http\Requests\Executive;

use App\Models\Executive\CarsRecord;
use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;
use Exception;

class CarsRecordRequest extends FormRequest
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
            'type' => 'required|numeric|in:1,2,3,4,5,6,7',
            'status' => 'required',
            'address' => 'required',
            'dates' => 'required|date_format:Y-m-d',
        ];
    }


    public function add($data){
        $validator = Validator::make($data, $this->getRules());
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }

    public function getList($data){
        $validator = Validator::make($data, [
            'type' => 'required|numeric|in:1,2,3,4,5,6,7',
            'cars_id' => 'required|numeric',
        ]);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }
        return true;
    }

}
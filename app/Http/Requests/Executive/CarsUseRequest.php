<?php

namespace App\Http\Requests\Executive;

use App\Models\Executive\CarsRecord;
use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;
use Exception;

class CarsUseRequest extends FormRequest
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
            //'cars_id' => 'required|numeric',
            //'driver_id' => 'required|numeric',
            //'department_id' => 'required|numeric',
            'people_number' => 'required|numeric',
            'begin_time' => 'required|date_format:Y-m-d',
            'end_time' => 'required|date_format:Y-m-d',
            'mileage' => 'required|numeric',
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
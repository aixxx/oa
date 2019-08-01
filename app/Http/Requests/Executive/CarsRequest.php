<?php

namespace App\Http\Requests\Executive;

use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class CarsRequest extends FormRequest
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
            'car_number' => 'required',
            'color' => 'required',
            'brand' => 'required',
            'type' => 'required',
            'displacement' => 'required',
            'seat_size' => 'required',
            'fuel_type' => 'required',
            'engine_number' => 'required',
            'car_status' => 'required|numeric',
            'driver_id' => 'required|numeric',
            //'department_id' => 'required|numeric',
            'buy_money' => 'numeric',
            'buy_date' => 'date_format:Y-m-d',
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
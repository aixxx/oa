<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/6/13
 * Time: 17:18
 */

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'name' => 'required',
            'legal_person' => 'required',
            'capital' => 'required|integer',
        ];

    }

    /**
     * 自定义错误信息
     * @return array
     */

    public function messages()
    {
        return [
            'name.required' => '公司名必填',
            'legal_person.required' => '法人必填',
            'capital.required' => '注册资本必填',
            'capital.integer' => '注册资本必须为整数'
        ];
    }
}
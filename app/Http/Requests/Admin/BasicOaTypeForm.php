<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BasicOaTypeForm  extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $all = $this->all();
		$id = $this->get("id");

		$arr = [
			'title' => 'required|max:50|unique:basic_oa_type,title'.($id ? ",$id" : ''),
			'code' => 'required|max:50|unique:basic_oa_type,code'.($id ? ",$id" : ''),
		];
       


        return $arr;
    }

    public function messages() {
        $all = $this->all();

        $arr = [
            'title.required' => '名称不能为空',
			'title.max' => '名称不能超过50个字',
			'title.unique' => '名称已存在，不能重复',
            'code.required' => '编号名称不能为空',
			'code.max' => '编号不能超过50个字',
			'code.unique' => '编号已存在，不能重复',
            
        ];

        return $arr;
    }

}

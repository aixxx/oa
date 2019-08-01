<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVueActionRequest  extends FormRequest {

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
            'title' => 'required|max:50|unique:api_vue_action,title'.($id ? ",$id" : ''),
            'vue_path' => 'required|max:50|unique:api_vue_action,vue_path'.($id ? ",$id" : ''),
        ];
        return $arr;
    }

    public function messages() {
        $all = $this->all();

        $arr = [
            'title.required' => '名称不能为空',
            'title.max' => '名称不能超过50个字',
            'title.unique' => '名称已存在，不能重复',
            'vue_path.required' => '编号名称不能为空',
            'vue_path.max' => '编号不能超过50个字',
            'vue_path.unique' => '编号已存在，不能重复',

        ];

        return $arr;
    }

}

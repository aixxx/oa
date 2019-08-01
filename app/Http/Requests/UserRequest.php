<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'mobile'             => 'required|regex:/^1[3456789]\d{9}$/',
            'english_name'       => 'required|min:3',
            'employee_num'       => 'required',
            'company_id'         => 'required',
            'departments'        => 'required',
            'email'              => 'required|regex:/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',
            'position'           => 'required',
            'gender'             => 'required',
            'pri_dept_id'        => 'required',
            'name'               => 'required',
            'is_sync_wechat'     => 'required',
            'join_at'            => 'required',
            'work_address'       => 'required',
            'first_chinese_name' => 'required',
            'last_chinese_name'  => 'required',
            'isleader'           => "required",
            'work_type'          => "required",
//            'superior_leaders'   => 'required',
        ];
    }


    /**
     * 自定义错误信息
     * @return array
     */

     public function messages()
     {
         return [
             'mobile.required'             => '手机号必填',
             'mobile.regex'                => '手机格式不正确',
             'english_name.required'       => '英文名必填',
             'english_name.min'            => '英文名最小长度是3个单词',
             'employee_num.required'       => '员工编号必填',
             'email.required'              => '邮箱必填',
             'email.regex'                 => '邮箱格式不正确',
             'position.required'           => '职位必填',
             'gender.required'             => '性别必填',
             'company_id.required'         => '所属公司必填',
             'departments.required'        => '所属部门必填',
             "pri_dept_id.required"        => "主部门必填",
             'name.required'               => '系统唯一账号必填',
             'is_sync_wechat.required'     => '是否同步必填',
             'join_at.required'            => '入职时间必填',
             'work_address.required'       => '工作地点必填',
             'first_chinese_name.required' => '姓必填',
             'last_chinese_name.required'  => '名必填',
             'isleader.required'           => "是否领导必填",
             'work_type.required'          => "班值类型必填",
//             'superior_leaders.required'   => '上级领导必填'
         ];
     }
}

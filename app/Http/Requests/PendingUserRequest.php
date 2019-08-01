<?php
/**
 * Created by PhpStorm.
 * User: qsq_lipf
 * Date: 18/6/27
 * Time: 下午12:25
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PendingUserRequest extends FormRequest
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
            'email' => 'required',
            'chinese_name' => 'required',
            'password' => 'required',
//            'given_name'     => 'required',
//            'family_name'    => 'required',
//            'english_name'   => 'required|min:3',
//            'email'          => 'required',
//            'mobile'         => 'required',
//            'position'       => 'required',
//            'join_at'        => 'required',
//            'company_id'     => 'required',
//            'department_id'  => 'required',
//            'work_address'   => 'required',
//            'gender'         => 'required',
//            'is_sync_wechat' => 'required',
//            'is_leader'      => 'required',
        ];
    }

    /**
     * 自定义错误信息
     * @return array
     */

    public function messages()
    {
        return [
            'email.required' => '邮箱必填',
            'chinese_name.required' => '姓名必填',
            'password.required' => '密码必填',
//            'mobile.required' => '手机号必填',
//            'mobile.regex' => '手机格式不正确',
//            'english_name.required' => '英文名必填',
//            'english_name.min' => '英文名最小长度是3个单词',
//            'employee_num.required' => '员工编号必填',
//            'email.regex' => '邮箱格式不正确',
//            'position.required' => '职位必填',
//            'gender.required' => '性别必填',
//            'company_id.required' => '所属公司必填',
//            'department_id.required' => '所属部门必填',
//            'name.required' => '企业微信账号必填',
//            'family_name.required' => '中文姓必填',
//            'given_name.required' => '中文名必填',
//            'is_sync_wechat.required' => '是否同步必填',
//            'is_leader.required' => '是否高管必填',
//            'join_at.required' => '入职时间必填',
//            'work_address.required' => '工作地点必填',
        ];
    }
}
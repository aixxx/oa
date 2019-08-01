<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
            'oldpassword' => 'required',
            'password' => 'required|max:20',
            'password_confirmation' => 'required|max:20',

        ];
    }


    /**
     * 自定义错误信息
     * @return array
     */

    public function messages()
    {
        return [
            'oldpassword.required' => '旧密码必填',
            'password.required' => '新密码必填',
            'password_confirmation.required' => '确认密码必填',
            'password.max' => '新密码长度不能超过20个字符',
            'password_confirmation.max' => '确认密码长度不能超过20个字符',
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Log;
use App;
use Auth;
use Hash;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ResetPasswordRequest;

class IndexController extends Controller
{
    protected $redirectTo = '/';

    protected $operateLog;

    /**
     * LoginController constructor.
     */

    public function __construct()
    {
        $this->middleware('adminAuth:admin');
        $this->operateLog = App::make('operatelog');
    }

    public function index()
    {
        return redirect()->route('admin.users.index');
    }

    public function resetPassword(Request $request)
    {
        return view('admin.resetpassword', compact('request'));
    }

    public function resetPasswordStore(ResetPasswordRequest $request)
    {
        if ($request) {
            $oldPassword = $request->input('oldpassword');

            $admin = Admin::fetchAdmin(Auth('admin')->user()->name);
            if (!$admin) {
                $message = '用户名或密码错误';
                return redirect(route('admin.resetpassword'))->with('message', $message)->withInput();
            }

            $password        = $request->input('password');
            $passwordConfirm = $request->input('password_confirmation');

            if ($password != $passwordConfirm) {
                $message = '两次输入的密码不一致';
                return redirect(route('admin.resetpassword'))->with('message', $message)->withInput();
            }

            if (!$this->isPassword($password)) {
                $message = '密码不符合规则';
                return redirect(route('admin.resetpassword'))->with('message', $message)->withInput();
            }

            if (!Hash::check($oldPassword, $admin->password)) {
                $message = '旧密码输入错误';
                return redirect(route('admin.resetpassword'))->with('message', $message)->withInput();
            }

            $result = $admin->update(['password' => bcrypt($password)]);

            if (!$result) {
                $message = '旧密码输入错误';
                return redirect(route('admin.resetpassword'))->with('message', $message)->withInput();
            }
        }

        $message = '重置密码成功请重新登录';
        Auth::guard('admin')->logout();
        return redirect(route('admin.login'))->with('message', $message);
    }

    public function isPassword($value)
    {
        $matchNumber  = "/[\d+]/";
        $matchWord    = "/[a-zA-Z]+/";
        $matchSpecial = "/[!@#$%\^$&*()*]+/";
        $v            = trim($value);
        if (empty($v)) {
            return false;
        }
        $len = strlen($value);
        if ($len < 8 || $len > 16) {
            return false;
        }

        return preg_match($matchNumber, $v) && preg_match($matchWord, $v) && preg_match($matchSpecial, $v);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\AuthenticatesLogoutService;
use Auth;
use App;
use Log;
use Hash;
use Cache;
use Response;
use App\Models\Admin;
use App\Services\Message\MessageService;

class LoginController extends Controller
{
    protected $redirectTo = '/admin/index';

    /**
     * LoginController constructor.
     */

    use AuthenticatesUsers, AuthenticatesLogoutService {
        AuthenticatesLogoutService::logout insteadof AuthenticatesUsers;
    }

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
        $this->operatelog = App::make('operatelog');
    }


    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function showLoginForm(Request $request)
    {
        return view('admin.login', compact('request'));
    }

    public function login(LoginRequest $request)
    {
        $name     = $request->input('name');
        $password = $request->input('password');
        $admin    = Admin::fetchAdmin($name);

        if (!$admin) {
            return redirect()->back()->with('message', '用户名或密码错误')->withInput($request->input());
        }

        if (!Hash::check($password, $admin->password)) {
            return redirect()->back()->with('message', '用户名或密码错误')->withInput($request->input());
        }

        if (Auth::guard('admin')->attempt(['name' => $name, 'password' => $password])) { // 登陆验证
            Log::info(Carbon::today()->toDateTimeString() . '用户' . $request->input('name') . '登录成功');
            return redirect($this->redirectTo);
        } else {
            Log::info(Carbon::today()->toDateTimeString() . '用户' . $request->input('name') . '登录失败');
            return redirect()->back()->with('message', '很抱歉，您的用户名和密码不匹配')->withInput($request->input());
        }
    }

    public function username()
    {
        return 'name';
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WeChatController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Kuainiu\KuainiuConnectProvider;
use Socialite;
use Session;
use Cookie;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * LoginController constructor.
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('checklogin');
        $this->operatelog = \App::make('operatelog');
    }

    /**
     * 显示登录界面
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        $redirect_uri = Session::pull('url.intended', $request->getSchemeAndHttpHost());
        session(['redirect_uri' => $redirect_uri]);
        $wechat = new WeChatController();
        if ($wechat->inWxwork($request)) {
            //如果在企业微信中，自动登录
            $request->query->add(['redirect_uri' => $redirect_uri]);

            return $wechat->redirect($request);
        }
        if (config('app.env') != 'local') {
            $oauth_redirect_uri = '';
        } else {
            $oauth_redirect_uri = '';
        }

        return view('auth.login', compact('request', 'redirect_uri', 'oauth_redirect_uri'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $data = [
                'operate_user_id' => auth()->id(),
                'action' => 'login_sys',
                'type' => null,
                'object_id' => auth()->id(),
                'object_name' => auth()->user()->name,
                'content' => null,
            ];
            $result = $this->operatelog->save($data);
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::findByEmail($request->get('email'))->first();
        $passwordTips = $user ? $user->password_tips : null;

        if ($passwordTips) {
            $errorInfo = [
                $this->username() => [trans('auth.failed')],
                'password_tips' => $passwordTips,
            ];
        } else {
            $errorInfo = [
                $this->username() => [trans('auth.failed')],
            ];
        }

        throw ValidationException::withMessages($errorInfo);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $user
     *
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($redirect_uri = $request->input('redirect_uri', session('redirect_uri'))) {
            return redirect()->away($redirect_uri);
        } else {
            return false;
        }
    }
}

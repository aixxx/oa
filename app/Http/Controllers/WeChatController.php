<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeChatController extends Controller
{

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function event(Request $request)
    {
        $app = app('wechat.work.contacts');

        $server = $app->server;

        Log::debug($request);

        $server->push(function ($message) {
            //Log::debug($message);
        });

        return $server->serve();
    }

    /*
     * 扫码登录授权
     */
    public function scan(Request $request)
    {
        $work = app('wechat.work.agent');
        $work->oauth->agent($work['config']['agent_id'])->redirect(url('/wechat/callback'))->send();
    }

    public function redirect(Request $request)
    {
        $work = app('wechat.work.agent');
        $callback_url = config('app.domain_env') == 'stage' ? '' : '';

        if ($redirect_uri = $request->input('redirect_uri')) {
            session(['redirect_uri' => $redirect_uri]);
            $callback_url .= '?redirect_uri=' . rawurlencode($redirect_uri);
        }
        //如果微信浏览器，无需扫码
        $scope    = $this->inWxwork($request) ? ['snsapi_base'] : [];
        $response = $work->oauth->agent($work['config']['agent_id'])->scopes($scope)->redirect(url($callback_url));
        return $response;
    }

    public function inWxwork(Request $request)
    {
        return strpos($request->server->get('HTTP_USER_AGENT'), 'wxwork') !== false;
    }

    public function callback(Request $request)
    {
        $work        = app('wechat.work.agent');
        $remote_user = $work->oauth->stateless()->agent($work['config']['agent_id'])->user()->original;

        abort_if($remote_user['errcode'] !== 0, 403, '无法获取企业微信用户信息, 请重试' . $remote_user['errcode']);

        $user = User::findByName($remote_user['UserId']??'');
        abort_if(empty($user), 403, '用户信息不同步，请稍后再试。');

        auth()->login($user, true);

        if (!Certificate::firstCofferCert($user->id)) { //如果当前员工没有证书则跳转至设置密码页面
            $redirect_uri = route('users.wx_setPassword');
        } else {
            $redirect_uri = $request->input('redirect_uri', session('redirect_uri', $request->getSchemeAndHttpHost()));
        }
        return redirect()->away($redirect_uri);
    }
}

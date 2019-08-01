<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class ClientCertAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->secure()) {
            return response()->view('errors.400', ['error' => 'The Client Certificate middleware requires a HTTPS connection.'], 400);
        }

        // the DN from the client certificate contains the CN, which forms the username
        $dn = $request->server('SSL_CLIENT_S_DN');
        preg_match('/cn=([^,\/]+)/i', $dn, $matches);
        $cn = isset($matches[1]) ? $matches[1] : null;
        if (!$cn) {
            return response()->view('errors.400', ['error' => 'Failed to get the certificate CN - did you provide a certificate in the request?'], 400);
        }

        //TODO: 判断当前登录用户是否与证书一致

        $user = User::where('name', $cn)->first();

        abort_if(!$user,403,'User not exists.');

        // authenticate the user
        Auth::login($user);

        return $next($request);
    }
}

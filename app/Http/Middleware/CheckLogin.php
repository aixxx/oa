<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $requestData = $request->all();
        if (isset($requestData['email']) && $requestData['email'])
        {
            $user = User::where('email','=',$requestData['email'])->first();
            if (!$user)
            {
                return $next($request);
            } elseif (($user->status != User::STATUS_JOIN) && ($user->status != User::STATUS_PENDING_JOIN)) {
                abort(403,"抱歉，您权限不足无法登录系统！");
            }
        }
        return $next($request);
    }
}

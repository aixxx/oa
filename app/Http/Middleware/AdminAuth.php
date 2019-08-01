<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AdminAuth
{
    public function handle($request, Closure $next, $guard)
    {
       /* if ((Auth::check())) {
            abort('403', '非法操作');
        }*/
        if (!Auth::guard($guard)->check()) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}

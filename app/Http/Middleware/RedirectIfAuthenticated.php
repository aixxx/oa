<?php

namespace App\Http\Middleware;

use Auth;
use Route;
use Closure;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $routeName = Route::currentRouteName();
            if ($routeName == 'password.request' || $routeName == 'admin.resetpasswordstore') {
                session()->flush();
                return redirect()->route($routeName);
            }
            $redirectUrl = 'admin' == $guard ? '/admin/index' : '/';
            return redirect($redirectUrl);
        }

        return $next($request);
    }
}

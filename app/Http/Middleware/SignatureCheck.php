<?php
namespace App\Http\Middleware;


use Closure;

class SignatureCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $signKey
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response;
    }


}

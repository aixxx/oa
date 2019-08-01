<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\OperateLog;
use Illuminate\Http\Request;

class CertMiddleware
{


    public function __construct()
    {
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @author hurs
     */
    public function handle($request, Closure $next)
    {
        $cert = $request->input('_cert', $request->cookie('cert'));
        if (!$cert) {
            return redirect(route('coffer.cert.index', ['redirect_uri' => $request->getUri()]));
        }

        $response = $next($request);
        return $response;
    }

}

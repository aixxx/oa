<?php

namespace App\Http\Middleware;

use Route;
use Closure;
use App\Models\OperateLog;

class AfterMiddleware
{
    protected $userLog;

    public function __construct()
    {
        $this->userLog = App()->make('operatelog');
    }

    public function handle($request, Closure $next, $guard = null)
    {
        $requestData      = $request->all();
        $currentRouteName = Route::currentRouteName();
        $operateLogConfig = $guard == 'admin' ? config('operatelog.admin') : config('operatelog.default');

        if (isset($operateLogConfig[$currentRouteName])) {
            $object_id   = isset($requestData['id']) ? $requestData['id'] : null;
            $object_name = isset($requestData['name']) ? $requestData['name'] : null;
            $operateData = OperateLog::joinLogData(
                Auth()->guard($guard)->id(),
                $operateLogConfig[$currentRouteName]['action'],
                $operateLogConfig[$currentRouteName]['type'],
                $object_id,
                $object_name,
                $requestData
            );
            $this->userLog->save($operateData);
        }

        $response = $next($request);
        return $response;
    }
}

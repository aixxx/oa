<?php

namespace App\Exceptions;

use App\Models\Roles;
use App\Services\WorkflowMessageService;
use Exception;
use App\Http\Helpers\ApiException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;
use HurryHandleException;
use DevFixException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)){
            app('sentry')->captureException($exception);
        }
        
        parent::report($exception);
    }
//    public function report(Exception $exception)
//    {
//        if (config('sentry.dsn') && app()->bound('sentry') && $this->shouldReport($exception)) {
//            app('sentry')->captureException($exception);
//        }
//        try {
//            Log::error($exception->getMessage() . "\t" . $exception->getTraceAsString());
//        } catch (Exception $e) {
//        }
//        try {
//            if ($exception instanceof HurryHandleException) {
//                WorkflowMessageService::warningException($exception, HurryHandleException::getFixRole(), $exception->getUrl());
//            }
//        } catch (Exception $e) {
//            $this->report(new DevFixException('发送报警通知失败:' . $e->getMessage(), $e->getCode(), $e));
//        }
//        parent::report($exception);
//    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // 将方法拦截到自己的ExceptionReport
        $reporter  = ApiException::make($exception);
        $exception = $reporter->packException($exception);
        if ($reporter->shouldReturn()) {
            return $reporter->report();
        }

        return parent::render($request, $exception);
    }
}

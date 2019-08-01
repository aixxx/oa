<?php

namespace App\Http\Helpers;

use Exception;
use DevFixException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use UserFixException;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Services\Common\MessageService;

class ApiException
{
    use ApiResponse;

    /**
     * @var Exception
     */
    public $exception;
    /**
     * @var Request
     */
    public $request;

    /**
     * @var
     */
    protected $report;

    /**
     * ExceptionReport constructor.
     * @param Request $request
     * @param Exception $exception
     */
    function __construct(Request $request, Exception $exception)
    {
        $this->request   = $request;
        $this->exception = $exception;
    }

    /**
     * @var array
     */
    public $doReport = [
        AuthenticationException::class => ['未授权', 401],
        ModelNotFoundException::class  => ['数据记录未找到', 404],
        QueryException::class          => ['系统繁忙，请稍后重试', 500],
        PostTooLargeException::class   => ['Your file is too large', 500],
    ];

    public $knowException = [
        UserFixException::class,
        DevFixException::class,
        ValidationException::class,
        HttpResponseException::class,
        MethodNotAllowedHttpException::class,
        NotFoundHttpException::class,
        NotFoundHttpException::class,
    ];
    const UNKNOWN_MESSAGE = '系统错误，请稍后重试';
    const UNKNOWN_CODE    = 500;

    public function packException(Exception $exception)
    {
        $pack = true;
        foreach ($this->knowException as $e) {
            if ($exception instanceof $e) {
                $pack = false;
            }
        }
        foreach (array_keys($this->doReport) as $e) {
            if ($exception instanceof $e) {
                $pack = false;
            }
        }
        if ($pack && !config('app.debug')) {
            $exception       = new DevFixException(self::UNKNOWN_MESSAGE, self::UNKNOWN_CODE, $exception);
            $this->exception = $exception;
        }
        return $exception;
    }

    /**
     * @return bool
     */
    public function shouldReturn()
    {

        if (!($this->request->wantsJson() || $this->request->ajax())) {
            return false;
        }

        foreach (array_keys($this->doReport) as $report) {

            if ($this->exception instanceof $report) {

                $this->report = $report;
                return true;
            }
        }

        return false;

    }

    /**
     * @param Exception $e
     * @return static
     */
    public static function make(Exception $e)
    {

        return new static(\request(), $e);
    }

    /**
     * @return mixed
     */
    public function report()
    {

        $message = $this->doReport[$this->report];

        return $this->failed($message[0], $message[1]);

    }

}
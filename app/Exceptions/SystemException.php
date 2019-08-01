<?php

namespace App\Exceptions;

use App\Constant\ConstFile;
use Exception;
use Throwable;

class SystemException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $code = ConstFile::API_RESPONSE_FAIL;
        $message = ConstFile::API_RESPONSE_FAIL_MESSAGE;
        parent::__construct($message, $code, $previous);
    }
}

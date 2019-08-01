<?php

namespace App\Exceptions;

use App\Constant\ConstFile;
use Exception;
use Throwable;

class DiyException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function instance($message, $code = ConstFile::API_RESPONSE_FAIL, Throwable $previous = null){
        return new self($message, $code, $previous);
    }
}

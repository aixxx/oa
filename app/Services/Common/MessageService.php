<?php

namespace App\Services\Common;

use Symfony\Component\HttpFoundation\Response;

class MessageService
{
    public static function generateResponse($mes, $code = Response::HTTP_FORBIDDEN)
    {
        abort($code, $mes);
    }
}

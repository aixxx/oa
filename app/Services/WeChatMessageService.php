<?php

namespace App\Services;

use EasyWeChat\Kernel\Messages\Message;

class WeChatMessageService extends Message
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
<?php

namespace App\Notifications\message;

use App\Contracts\MessageContract;
use App\Models\Workflow\WorkflowMessage;
use DevFixException;

class WeChat implements MessageContract
{
    /**
     * @param $weChatInfo
     *
     * @return bool
     * @throws \Exception
     */
    public static function add($weChatInfo)
    {
        $message = new WorkflowMessage();
        $message->forceFill($weChatInfo);
        if (!$message->save()) {
            throw new DevFixException('创建企业微信消息失败');
        }

        return true;
    }
}


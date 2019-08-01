<?php

namespace App\Notifications\message;

use App\Contracts\MessageContract;
use App\Models\Workflow\WorkflowMessage;
use DevFixException;
class Email implements MessageContract
{
    /**
     * @param $mailInfo
     *
     * @return bool
     * @throws \Exception
     */
    public static function add($mailInfo)
    {
        $message = new WorkflowMessage();
        $message->forceFill($mailInfo);
        if (!$message->save()) {
            throw new DevFixException('创建邮件失败');
        }

        return true;
    }
}


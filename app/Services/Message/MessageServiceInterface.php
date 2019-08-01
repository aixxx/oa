<?php
namespace App\Services\Message;

/**
 * Interface MessageServiceInterface
 *
 * @package App\Services\Message
 */
interface MessageServiceInterface
{
    /**
     * @param \App\Services\Message\MessageRequest $messageRequest
     *
     * @return \App\Services\Message\MessageResponse
     */
    public function send(MessageRequest $messageRequest);
}

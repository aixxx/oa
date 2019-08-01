<?php
namespace App\Services\Message;

use App\Constant\CodeConstant;
use App\Mail\MessageMail;
use Carbon\Carbon;
use Mail;

/**
 * Class EmailMessageService
 *
 * @package App\Services\Message
 */
class EmailMessageService implements MessageServiceInterface
{
    /**
     * @param \App\Services\Message\MessageRequest $messageRequest
     *
     * @return \App\Services\Message\MessageResponse
     */
    public function send(MessageRequest $messageRequest)
    {
        if (empty($messageRequest->cc['email'])) {
            $response = Mail::to($messageRequest->to['email'])
                ->send(new MessageMail($messageRequest));
        } else {
            $response = Mail::to($messageRequest->to['email'])
                ->cc($messageRequest->cc['email'])
                ->send(new MessageMail($messageRequest));
        }

        return new MessageResponse([
            'code'    => CodeConstant::RESPONSE_SUCCESS,
            'message' => '已发送',
            'data'    => $response,
            'sentAt'  => Carbon::now()->toDateTimeString(),
        ]);
    }
}

<?php

namespace App\Mail;

use App\Services\Message\MessageRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class MessageMail
 *
 * @package App\Mail
 */
class MessageMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $messageRequest;

    public function __construct(MessageRequest $messageRequest)
    {
        $this->messageRequest = $messageRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('workflow.emails.mail')
            ->from(config('mail.from'))
            ->cc($this->messageRequest->cc)
            ->subject($this->messageRequest->title)
            ->with(['content' => $this->messageRequest->content]);
    }
}

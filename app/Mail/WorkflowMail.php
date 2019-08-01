<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkflowMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $mes;

    /**
     * WorkflowMail constructor.
     *
     * @param \App\Models\Workflow\WorkflowMessage $message
     */
    public function __construct($message)
    {
        $this->mes = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = config('mail.from');
        $mes  = $this->mes;
        return $this
            ->from($from)
            ->subject($mes['title'])
            ->view('workflow.emails.mail')
            ->with([
                'content' => $mes['content'],
            ]);
    }
}

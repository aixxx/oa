<?php
namespace App\Jobs;

use App\Constant\CodeConstant;
use App\Constant\CommonConstant;
use App\Models\MessageLog;
use App\Services\Message\MessageRequest;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class MessageQueue
 *
 * @package App\Jobs
 */
class MessageQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageRequest;

    public $tries = 3;

    /**
     * MessageQueue constructor.
     *
     * @param \App\Services\Message\MessageRequest $messageRequest
     */
    public function __construct(MessageRequest $messageRequest)
    {
        $this->messageRequest = $messageRequest;
        $this->connection     = 'database';
        $this->queue          = BaseQueue::QUEUE_ORDER_HIGH_THREE;
    }

    public function handle()
    {
        $messageLog = new MessageLog();
        $messageLog->fill([
            'template_key'     => $this->messageRequest->templateKey,
            'push_type'        => $this->messageRequest->type,
            'sent_content_md5' => md5($this->messageRequest->content),
            'sent_to'          => json_encode($this->messageRequest->to, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'sent_cc'          => json_encode($this->messageRequest->cc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
        $messageResponse = app()->make($this->messageRequest->type)->send($this->messageRequest);
        Log::info('发送消息结果', json_decode(json_encode(
            $messageResponse,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ), true));
        if ($messageResponse->code != CodeConstant::RESPONSE_SUCCESS) {
            $messageLog->sent_status = CommonConstant::MESSAGE_SEND_STATUS_FAILED;
            $messageLog->sent_at     = $messageResponse->sentAt;
            $messageLog->save();
            throw new Exception(json_encode($messageResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $messageLog->sent_status = CommonConstant::MESSAGE_SEND_STATUS_SUCCESSFUL;
            $messageLog->sent_at     = $messageResponse->sentAt;
            $messageLog->save();
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(Exception $exception)
    {
        Log::error('消息发送失败', [
            'message' => $exception->getMessage(),
            'trace'   => $exception->getTrace(),
        ]);
    }
}

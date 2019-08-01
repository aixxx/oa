<?php
namespace App\Services\Message;

use App\Constant\CodeConstant;
use Carbon\Carbon;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\TextCard;

/**
 * Class WechatMessageService
 *
 * @package App\Services\Message
 */
class WechatMessageService implements MessageServiceInterface
{
    const CODE_SUCCESS = 0;

    /**
     * @param \App\Services\Message\MessageRequest $messageRequest
     *
     * @return \App\Services\Message\MessageResponse
     */
    public function send(MessageRequest $messageRequest)
    {
        if (!empty($messageRequest->url)) {
            $message = new TextCard([
                'title'       => $messageRequest->title,
                'description' => $messageRequest->content,
                'url'         => $messageRequest->url,
            ]);
        } else {
            $message = new Text($messageRequest->content);
        }

        $result = app('wechat.work.agent')->messenger
            ->message($message)
            ->ofAgent(config('wechat.work.agent.agent_id'))
            ->toUser($messageRequest->to['wechat'])
            ->send();

        if (isset($result['errcode'])) {
            $code = $result['errcode'] == self::CODE_SUCCESS ?
                CodeConstant::RESPONSE_SUCCESS : CodeConstant::RESPONSE_FAIL;
        } else {
            $code = CodeConstant::RESPONSE_FAIL;
        }

        return new MessageResponse([
            'code'    => $code,
            'message' => $result['errmsg'] ?? '',
            'data'    => $result,
            'sentAt'  => Carbon::now()->toDateTimeString(),
        ]);
    }
}

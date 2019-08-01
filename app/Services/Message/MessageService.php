<?php
namespace App\Services\Message;

use App\Jobs\MessageQueue;
use App\Models\MessageTemplate;

/**
 * Class MessageService
 *
 * @package App\Services\Message
 */
class MessageService
{
    /**
     * @param string $key
     * @param array  $params
     */
    public static function send($key, $params = [])
    {
        $templates = MessageTemplate::activeAll($key);

        foreach ($templates as $template) {
            if (empty($params['to']['email']) && empty($params['to']['wechat'])) {
                continue;
            }
            $title   = app(StringCompilerEngine::class)->renderString($template->template_title, $params);
            $title   = sprintf('【%s】%s', $template->template_sign, $title);
            $content = app(StringCompilerEngine::class)->renderString($template->template_content, $params);

            $messageRequest = new MessageRequest([
                'templateKey' => $template->template_key,
                'type'        => $template->template_push_type,
                'title'       => $title,
                'content'     => $content,
                'url'         => $params['url'] ?? config('app.url'),
                'to'          => $params['to'],
                'cc'          => $params['cc'] ?? [],
            ]);

            MessageQueue::dispatch($messageRequest);
        }
    }
}

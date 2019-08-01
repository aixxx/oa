<?php
namespace App\Services\Message;

use App\Constant\CommonConstant;

/**
 * Class MessageRequest
 *
 * @package App\Services\Message
 *
 * @property string $templateKey 模板键值
 * @property string $type        类型
 * @property string $title       标题
 * @property string $content     内容
 * @property string $url         跳转链接
 * @property array  $to          接收者
 * @property array  $cc          抄送者
 */
class MessageRequest
{
    public $templateKey;
    public $type    = CommonConstant::MESSAGE_PUSH_TYPE_WECHAT;
    public $title   = '';
    public $content = '';
    public $url     = '';
    public $to      = [
        'email'  => [],
        'wechat' => [],
    ];
    public $cc      = [
        'email'  => [],
        'wechat' => [],
    ];

    /**
     * MessageRequest constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }
}

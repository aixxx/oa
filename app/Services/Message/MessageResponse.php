<?php
namespace App\Services\Message;

use App\Constant\CodeConstant;

/**
 * Class MessageResponse
 *
 * @package App\Services\Message
 */
class MessageResponse
{
    // 返回码
    public $code = CodeConstant::RESPONSE_FAIL;
    // 返回消息
    public $message = '';
    // 发送时间
    public $sentAt;
    // 返回数据
    public $data = [];

    /**
     * MessageResponse constructor.
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

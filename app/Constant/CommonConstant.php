<?php
namespace App\Constant;

/**
 * Class CommonConstant
 *
 * @package     App\Constant
 * @description 公共常量类
 */
class CommonConstant
{
    // 标记完成常量
    const FLAG_IS_FINISHED = 1;
    // 标记未完成常量
    const FLAG_IS_NOT_FINISHED = 0;
    // 删除标记
    const FLAG_IS_DELETED     = 1;
    const FLAG_IS_NOT_DELETED = 0;
    // 状态：可用
    const STATUS_ACTIVE = 'active';
    // 状态：不可用
    const STATUS_INACTIVE = 'inactive';
    const STATUS_MAPPING  = [
        self::STATUS_ACTIVE   => '激活',
        self::STATUS_INACTIVE => '未激活',
    ];
    // 消息推送类型
    const MESSAGE_PUSH_TYPE_EMAIL   = 'email';
    const MESSAGE_PUSH_TYPE_WECHAT  = 'wechat';
    const MESSAGE_PUSH_TYPE_SYSTEM  = 'system';
    const MESSAGE_PUSH_TYPE_SMS     = 'sms';
    const MESSAGE_PUSH_TYPE_MAPPING = [
        self::MESSAGE_PUSH_TYPE_EMAIL  => '电子邮件',
        self::MESSAGE_PUSH_TYPE_WECHAT => '企业微信',
        self::MESSAGE_PUSH_TYPE_SYSTEM => '系统通知',
        self::MESSAGE_PUSH_TYPE_SMS    => '手机短信',
    ];

    // 消息类型
    const MESSAGE_TYPE_FINANCE    = 'finance';
    const MESSAGE_TYPE_ATTENDANCE = 'attendance';
    const MESSAGE_TYPE_ASSET      = 'asset';
    const MESSAGE_TYPE_LEGAL      = 'legal';
    const MESSAGE_TYPE_OTHER      = 'other';
    const MESSAGE_TYPE_MAPPING    = [
        self::MESSAGE_TYPE_FINANCE    => '财务',
        self::MESSAGE_TYPE_ATTENDANCE => '考勤',
        self::MESSAGE_TYPE_ASSET      => '固定资产',
        self::MESSAGE_TYPE_LEGAL      => '法务',
        self::MESSAGE_TYPE_OTHER      => '其他',
    ];

    // 消息发送状态
    const MESSAGE_SEND_STATUS_SUCCESSFUL = 1;
    const MESSAGE_SEND_STATUS_FAILED     = -1;
}

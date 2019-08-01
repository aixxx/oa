<?php
/**
 * 财务系统对接相关配置
 * User: aike
 * Date: 2018/7/24
 * Time: 下午11:34
 */

return [
    /**
     * 财务系统的地址
     */
    'base_url' => env('CAPITAL_BASE_URI'),

    /**
     * 签名key
     */
    'sign_key' => env('CAPITAL_API_SIGN_KEY', 'FX9xJP9fSzorEJ1RKRd1QGUMQIveipKx'),

    /**
     * 支付回调
     */
    'pay_callback_url' => env('CAPITAL_PAY_CALL_BACK_URL'),
];

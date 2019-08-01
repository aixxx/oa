<?php
namespace App\Services\pay;

use App\Http\Helpers\StringHelper;
use App\Models\Workflow\Entry;

/**
 * Created by PhpStorm.
 * User: aike
 * Date: 2018/7/25
 * Time: 下午8:39
 */

abstract class PayCreateBase
{
    const TRADE_REPORT_TYPE_PUBLIC = 2; // 对公
    const TRADE_REPORT_TYPE_PRIVATE = 1; // 对私

    // 支付状态回调
    const PAY_RES_STATUS_SUCCESS = 0; // 成功
    const PAY_RES_STATUS_ERROD   = 1; // 失败

    /**
     * 将数据封装成财务系统接口需要的格式
     * @param array $data
     * @param null  $keyPrefix
     *
     * @return array
     */
    protected function formatRequestParams(array $data, $keyPrefix = null)
    {
        return [
            'type' => 'Withdraw',
            'key'  => StringHelper::genUniqueString($keyPrefix),
            'from_system' => 'OA',
            'data' => $data
        ];
    }

    /**
     * 将申请单数据转换为付款请求接口参数
     * @param                            $orderNo
     * @param \App\Models\Workflow\Entry $entry
     *
     * @return mixed
     */
    abstract public function entryToPayParams($orderNo, Entry $entry);

    /**
     * 支付回调
     * @param $param
     */
    public function payCallBack($request)
    {

    }
}

<?php
namespace App\Services\pay;
use DevFixException;
/**
 * Created by PhpStorm.
 * User: aike
 * Date: 2018/7/25
 * Time: 下午8:43
 */

class PayCreateFactory
{
    private static $createrMap = [
        'fee_expense' => ExpensesReimburse::class,
    ];

    /**
     * 生成转换对象
     * @param $flowNo
     *
     * @return ExpensesReimburse
     * @throws \Exception
     */
    public static function getPayCreate($flowNo)
    {
        if (!isset(self::$createrMap[$flowNo])) {
            throw new DevFixException(sprintf('生成支付映射实例错误,无效的flowNo:%s', $flowNo));
        }

        return new self::$createrMap[$flowNo];
    }
}

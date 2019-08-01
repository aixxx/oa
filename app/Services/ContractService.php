<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/7/30
 * Time: 下午5:04
 */

namespace App\Services;

use App\Models\Contract;

class ContractService
{
    public static function validContactNumber($userId, $number)
    {
        return Contract::validContactNumber($userId, $number);
    }

    public static function getCompanyNameList()
    {
        return Contract::getCompanyIdNameList();
    }
}

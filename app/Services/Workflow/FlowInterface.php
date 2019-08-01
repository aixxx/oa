<?php
namespace App\Services\Workflow;

use App\Models\Workflow\Entry;

/**
 * Created by PhpStorm.
 * User: aike
 * Date: 2018/7/30
 * Time: 下午5:08
 */

interface FlowInterface
{
    /**
     * 校验表单数据合法性
     * @param \App\Models\Workflow\Entry $entry
     *
     * @throws \Exception
     */
    public function checkValidate(Entry $entry);
}

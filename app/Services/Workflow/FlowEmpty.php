<?php
namespace App\Services\Workflow;

use App\Http\Helpers\StringHelper;
use App\Models\Workflow\Entry;


/**
 * 费用报销表单校验
 * User: aike
 * Date: 2018/7/30
 * Time: 下午5:08
 */
class FlowEmpty implements FlowInterface
{
    /**
     * 校验表单数据合法性
     * @param \App\Models\Workflow\Entry $entry
     *
     * @throws \Exception
     */
    public function checkValidate(Entry $entry)
    {
    }
}

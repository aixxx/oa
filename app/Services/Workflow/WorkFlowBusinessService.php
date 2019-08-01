<?php

namespace App\Services\Workflow;

use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * User: aike
 * Date: 2018/7/30
 * Time: 下午5:34
 */
class WorkFlowBusinessService
{
    private static $businessMap = [

    ];

    private $flow; // 工作流
    private $entry; // 申请单

    public function __construct(Entry $entry)
    {
        $this->flow  = $entry->flow;
        $this->entry = $entry;
    }

    public function checkValidate()
    {
        $flowBusiness = static::getFlowBusiness($this->flow);

        $flowBusiness->checkValidate($this->entry);
    }

    /**
     * @param Flow $flow
     *
     * @return FlowExpensesReimburse|FlowInterface|FlowEmpty
     * @throws \Exception
     * @author hurs
     */
    public static function getFlowBusiness(Flow $flow)
    {
        if (!isset(static::$businessMap[$flow->flow_no])) {
            return new FlowEmpty();
        } else {
            return new static::$businessMap[$flow->flow_no];
        }
    }
}

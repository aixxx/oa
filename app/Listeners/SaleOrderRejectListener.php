<?php

namespace App\Listeners;

use App\Events\ExtraAuditEvent;
use App\Models\Workflow\Proc;
use App\Models\PAS\SaleOrder;


/**
 * 采购单审批不通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class SaleOrderRejectListener
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ExtraAuditEvent  $event
     * @return mixed
     */
    public function handle($event)
    {
        $procsId = $event->procsId;
        $process = Proc::where('id', $procsId)->first();
        $entry_id = $process->entry_id;
        try {
            $where['entrise_id']=$entry_id;
            $id = SaleOrder::where($where)->value('id');

            $data['status']=3;
            $data['updated_at']=date('Y-m-d H:i:s',time());
            SaleOrder::where('id', $id)->update($data);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('采购单申请审批驳回', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

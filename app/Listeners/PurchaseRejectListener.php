<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\Meeting\Meeting;
use App\Models\PAS\Purchase\Purchase;
use App\Models\Workflow\Proc;

/**
 * 采购单审批不通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class PurchaseRejectListener
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
            $date['status']=Purchase::API_STATUS_FAIL;
            $date['updated_at'] = date('Y-m-d H:i:s', time());
            Purchase::where($where)->update($date);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('采购单申请审批驳回', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

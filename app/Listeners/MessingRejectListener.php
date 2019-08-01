<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\Meeting\Meeting;
use App\Models\Workflow\Proc;

/**
 * 会议审批不通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class MessingRejectListener
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
            $data['status']=Meeting::API_STATUS_REFUSE;
            $data['updated_at']=date('Y-m-d H:i:s',time());
            Meeting::where($where)->update($data);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('会议申请审批驳回', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

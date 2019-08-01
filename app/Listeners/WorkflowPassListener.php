<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\Message\Message;
use App\Models\PAS\Purchase\PaymentOrder;
use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\Purchase\PurchasePayableMoney;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\Workflow\Proc;

/**
 * 工作流审核全部通过发送系统消息
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class WorkflowPassListener
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
        $process = Proc::with('entry')->findOrFail($event->procsId);
        //dd($process->toArray());
        try {
            $data = [
                'receiver_id' => $process->entry->user_id,//接收者（申请人）
                'sender_id' => $process->user_id,//发送这（最后审批人）
                'content'=> $process->entry->title.'审核通过',//内容（审批title）
                'type' => 4,		//4：审批全部通过 5：审批驳回
                'relation_id' => $process->entry_id,		//workflow_entries 的 id
                'created_at'=>date('Y-m-d H:i:s',time()),
                'updated_at'=>date('Y-m-d H:i:s',time())
            ];
            Message::insert($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}

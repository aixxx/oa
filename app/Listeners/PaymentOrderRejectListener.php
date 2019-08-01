<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\PAS\Purchase\PaymentOrder;
use App\Models\PAS\Purchase\PaymentOrderContent;
use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\Workflow\Proc;

/**
 * 付款单审批不同过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class PaymentOrderRejectListener
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
            $data['status']=PaymentOrder::STATUS_RETURNED;
            $data['updated_at'] = date('Y-m-d H:i:s', time());


            DB::transaction(function() use($data,$where) {
                $id = PaymentOrder::where($where)->value('id');
                PaymentOrder::where($where)->update($data);
                $countArr= PaymentOrderContent::where('po_id',$id)->get(['p_id','type']);
                if($countArr){
                    $countArr=$countArr->toArray();
                    foreach ($countArr as $key =>$las){
                        $whres['id']=$las['p_id'];
                        $datas['p_status']=0;
                        if($las['type']==1){
                            Purchase::where($whres)->update($datas);
                        }else{
                            ReturnOrder::where($whres)->update($datas);
                        }
                    }
                }
            });

        } catch (\Exception $exception) {
            report($exception);
            Log::error('采购单申请审批驳回', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

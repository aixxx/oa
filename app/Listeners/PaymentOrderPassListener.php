<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\PAS\Purchase\PaymentOrder;
use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\Purchase\PurchasePayableMoney;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\Workflow\Proc;

/**
 * 付款单审批通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class PaymentOrderPassListener
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
            $date['status']=PaymentOrder::STATUS_SUCCESS;
            $date['updated_at'] = date('Y-m-d H:i:s', time());

            DB::transaction(function() use($date,$where) {
                $p_code = PaymentOrder::where($where)->value('p_code');
                PaymentOrder::where($where)->update($date);
                $whres['code'] = $p_code;
                $datas['p_status'] = 0;
                Purchase::where($whres)->update($datas);
                $info =PaymentOrder::where($where)->first(['supplier_id','money']);
                $money=PurchasePayableMoney::where('supplier_id',$info->supplier_id)->first(['id','money']);
                if($money){
                    $money =  $money->money - $info->money;
                    $datas['money']=$money;
                    $datas['updated_at'] = date('Y-m-d H:i:s', time());
                    PurchasePayableMoney::where('id',$money->id)->update($datas);
                }else{
                    $money =$info->money;
                    $datas['money']=$money;
                    $datas['created_at'] = date('Y-m-d H:i:s', time());
                    $datas['updated_at'] = date('Y-m-d H:i:s', time());
                    PurchasePayableMoney::insert($datas);
                }
            });
        } catch (\Exception $exception) {
            report($exception);
            Log::error('采购单申请审批通过', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

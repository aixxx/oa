<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\Purchase\PurchasePayableMoney;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

/**
 * 采购单批通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class ReturnOrderPassListener
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
            $info =ReturnOrder::where($where)->first(['supplier_id','money']);

            $money=PurchasePayableMoney::where('supplier_id',$info->supplier_id)->first(['id','money']);

            if($money){
                $moneys =  $money->money - $info->money;
                $datas['money']=$moneys;
                $datas['updated_at'] = date('Y-m-d H:i:s', time());
                PurchasePayableMoney::where('id',$money->id)->update($datas);
            }else{
                $money =$money->money;
                $datas['money']=$money;
                $datas['created_at'] = date('Y-m-d H:i:s', time());
                $datas['updated_at'] = date('Y-m-d H:i:s', time());
                PurchasePayableMoney::insert($datas);
            }
            $date['status']=ReturnOrder::STATUS_PASS_OK;
            $date['updated_at'] = date('Y-m-d H:i:s', time());
            ReturnOrder::where($where)->update($date);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('采购单申请审批通过', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}
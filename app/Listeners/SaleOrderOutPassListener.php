<?php

namespace App\Listeners;

use App\Events\ExtraAuditEvent;

use App\Models\PAS\SaleOutWarehouse;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;


/**
 * 销售出库单批通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class SaleOrderOutPassListener
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
        //$entry_id = 2992;
        try {
            //销售单审批通过，更新商品销量
            $where['entrise_id']=$entry_id;
            $id = SaleOutWarehouse::where($where)->value('id');

            $date['status']=4;
            $date['updated_at'] = date('Y-m-d H:i:s', time());
            SaleOutWarehouse::where('id', $id)->update($date);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('销售出库单申请审批通过', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

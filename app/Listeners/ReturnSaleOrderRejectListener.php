<?php

namespace App\Listeners;

use App\Models\PAS\SaleReturnOrder;
use App\Models\PAS\SaleReturnOrderGoods;
use App\Events\ExtraAuditEvent;
use App\Models\Workflow\Proc;
use App\Repositories\PAS\GoodsRepository;
use DB;

/**
 * 采购单审批不通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class ReturnSaleOrderRejectListener
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
            $info = SaleReturnOrder::where($where)->with('return_goods')->first(['id','goods_num'])->toArray();
            DB::transaction(function() use($info,$where) {
                SaleReturnOrderGoods::where('return_order_id', $info['id'])->update(['status'=>0]);

                $up_sale_goods = [];
                foreach($info['return_goods'] as $v){
                    $up_sale_goods[] = [
                        'id' => $v['sale_order_goods_id'],
                        'apply_back_num' => '`apply_back_num`-'.$v['return_num']
                    ];
                }
                if(!empty($up_goods_data)){
                    app()->make(GoodsRepository::class)->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
                }
                $date['status']=3;
                $date['updated_at'] = date('Y-m-d H:i:s', time());
                SaleReturnOrder::where($where)->update($date);
            });
        } catch (\Exception $exception) {
            report($exception);
            Log::error('采购单申请审批驳回', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

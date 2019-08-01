<?php

namespace App\Listeners;

use App\Events\ExtraAuditEvent;
use App\Models\PAS\SaleReturnInWarehouse;
use App\Models\PAS\SaleReturnInWarehouseGoods;
use App\Models\Workflow\Proc;
use App\Repositories\PAS\GoodsRepository;
use DB;

/**
 * 退货入库单审批不通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class ReturnSaleOrderInRejectListener
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
            $info = SaleReturnInWarehouse::where($where)->with('in_goods')->first();

            DB::transaction(function() use($info, &$res) {
                if($info['entrise_id']){
                    $data['status']=3;
                    $data['updated_at']=date('Y-m-d H:i:s',time());
                    $res = SaleReturnInWarehouse::where('id', $info['id'])->update($data);
                    $res = SaleReturnInWarehouseGoods::where('in_id', $info['id'])->update(['status'=>0]);
                    $up_sale_goods = [];
                    foreach($info['in_goods'] as $v){
                        $up_sale_goods[] = [
                            'id' => $v['return_order_goods_id'],
                            'apply_in_num' => '`apply_in_num`-'.$v['in_num']
                        ];
                    }
                    if(!empty($up_sale_goods)){
                        app()->make(GoodsRepository::class)->updateBatch('pas_sale_return_order_goods', $up_sale_goods, 0);
                    }
                }
            });
        } catch (\Exception $exception) {
            report($exception);
            Log::error('退货入库单申请审批驳回', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

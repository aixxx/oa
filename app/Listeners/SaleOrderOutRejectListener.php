<?php

namespace App\Listeners;

use App\Models\PAS\SaleOutWarehouse;
use App\Models\PAS\SaleOutWarehouseGoods;
use App\Events\ExtraAuditEvent;
use App\Models\Workflow\Proc;
use App\Repositories\PAS\GoodsRepository;
use DB;

/**
 * 销售出库单审批不通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class SaleOrderOutRejectListener
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
            $info = SaleOutWarehouse::where($where)->with('out_goods')->first();

            DB::transaction(function() use($info, &$res) {
                if($info['entrise_id']){
                    $data['status']=3;
                    $data['updated_at']=date('Y-m-d H:i:s',time());
                    $res = SaleOutWarehouse::where('id', $info['id'])->update($data);
                    $res = SaleOutWarehouseGoods::where('out_id', $info['id'])->update(['status'=>0]);
                    $up_sale_goods = [];
                    foreach($info['out_goods'] as $v){
                        $up_sale_goods[] = [
                            'id' => $v['sale_order_goods_id'],
                            'apply_out_num' => '`apply_out_num`-'.$v['out_num']
                        ];
                    }
                    if(!empty($up_sale_goods)){
                        app()->make(GoodsRepository::class)->updateBatch('pas_sale_order_goods', $up_sale_goods, 0);
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

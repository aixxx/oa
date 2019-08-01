<?php

namespace App\Listeners;

use App\Events\ExtraAuditEvent;
use App\Models\PAS\SaleReturnOrder;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;
use App\Repositories\PAS\GoodsRepository;
use DB;

/**
 * 销售退货单批通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class ReturnSaleOrderPassListener
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
        //$entry_id = 3003;
        try {
            //销售退货单审批通过，更新商品退货量，不更新销量
            $where['entrise_id']=$entry_id;
            $info = SaleReturnOrder::where($where)->with('return_goods')->first(['id','goods_num'])->toArray();
            DB::transaction(function() use($info,$where) {
                if(!empty($info) && !empty($info['return_goods'])){
                    $num = $up_goods_data = $goods =[];
                    foreach($info['return_goods'] as $v){
                        $num[$v['goods_id']][] = $v['return_num'];
                        $up_goods_data[] = [
                            'id' => $v['sale_order_goods_id'],
                            'back_num' => '`back_num`+' . $v['return_num'],
                            'apply_back_num' => '`apply_back_num`-' . $v['return_num']
                        ];
                    }
                    foreach ($num as $k => $v){
                        $goods[] = [
                            'goods_id' => $k,
                            'back_num' => '`back_num`+' . array_sum($v),
                        ];
                    }
                    app()->make(GoodsRepository::class)->updateBatch('pas_goods', $goods, 0);
                    if(!empty($up_goods_data)){
                        app()->make(GoodsRepository::class)->updateBatch('pas_sale_order_goods', $up_goods_data, 0);
                    }
                }
                $date['status']=4;
                $date['updated_at'] = date('Y-m-d H:i:s', time());
                SaleReturnOrder::where($where)->update($date);
            });
        } catch (\Exception $exception) {
            report($exception);
            Log::error('销售退货单申请审批通过', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

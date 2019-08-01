<?php

namespace App\Listeners;

use App\Events\ExtraAuditEvent;

use App\Models\PAS\SaleOrder;
use App\Models\PAS\SaleOrderGoods;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;
use App\Repositories\PAS\GoodsRepository;
use DB;

/**
 * 销售单批通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class SaleOrderPassListener
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
            $info = SaleOrder::where($where)->with('goods')->first(['id','goods_num'])->toArray();

            DB::transaction(function() use($info,$where) {
                if(!empty($info)){
                    $num = [];
                    foreach($info['goods'] as $v){
                        $num[$v['goods_id']][] = $v['num'];
                    }

                    $goods = [];
                    foreach ($num as $k => $v){
                        $goods[] = [
                            'goods_id' => $k,
                            'sales_num' => '`sales_num`+' . array_sum($v),
                        ];
                    }
                    app()->make(GoodsRepository::class)->updateBatch('pas_goods', $goods, 0);
                }

                $date['status']=4;
                $date['updated_at'] = date('Y-m-d H:i:s', time());
                SaleOrder::where($where)->update($date);
                SaleOrderGoods::where('order_id', $info['id'])->update(['status'=>1]);
            });
        } catch (\Exception $exception) {
            report($exception);
            Log::error('销售单申请审批通过', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

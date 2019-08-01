<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\Meeting\Meeting;
use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\Workflow\Proc;

/**
 * 采购单审批不通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class ReturnOrderRejectListener
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
            $data['status']=ReturnOrder::STATUS_RETURN;
            $data['updated_at'] = date('Y-m-d H:i:s', time());


            DB::transaction(function() use($data,$where) {
                $info = ReturnOrder::where($where)->first(['id','type']);
                ReturnOrder::where($where)->update($data);

                $list = DB::table('pas_warehousing_apply_content as a')
                    ->leftJoin('pas_purchase_commodity_content as b' ,'a.pcc_id','b.id')
                    ->where('a.p_id','=',$info->id)
                    ->where('a.type','=',2)//退货商品数据
                    ->get(['a.number','b.id','b.r_number']);
                //var_dump($list);die;
                foreach($list as $key=>$values){
                    $whereone['id']=$values->id;
                    if($info->type==1){
                        $dataOnes['rw_number']=$values->rw_number-$values->number?$values->rw_number-$values->number:0;
                    }else{
                        $dataOnes['r_number']=$values->r_number-$values->number?$values->r_number-$values->number:0;
                    }

                    $dataOnes['updated_at']=date('Y-m-d H:i:s',time());
                    PurchaseCommodityContent::where($whereone)->update($dataOnes);
                }
            });

        } catch (\Exception $exception) {
            report($exception);
            Log::error('采购单申请审批驳回', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

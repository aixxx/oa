<?php

namespace App\Listeners;

use App\Events\FeeExpenseStatusEvent;
use App\Models\Financial;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeeExpenseStatusListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FeeExpenseStatusEvent  $event
     * @return void
     */
    public function handle($event)
    {
        $procsId = $event->procsId;
        $process = Proc::where('id', $procsId)->orderBy('id', 'desc')->first();

        $process_id = $process->process_id;
        $user_id = $process->entry->user_id;
        $entry_id = $process->entry->id;
        $query = Financial::where(['entry_id'=>$entry_id,'user_id'=>$user_id]);
        $finance = $query->first();

        $expense_amount = $finance->expense_amount;
        // 暂时想不出 更好的解决办法  通过判断 小于号 选择status
        $fLinks = Flowlink::where(['flow_id'=> $process->entry->flow_id,'process_id'=>$process_id])->get();
        $finance->status = 2;
        foreach ($fLinks as $fLink) {
            if($fLink['expression'] != '') {
                if($fLink['expression'] != '' && (eval("return ".$fLink['expression']. ";") && strpos($fLink['expression'],'<'))) {
                    $finance->status = 3;
                }
            }
        }
        $finance->save();
    }
}

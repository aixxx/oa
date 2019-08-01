<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:31
 */

namespace App\Listeners;

use App\Events\WageEndEvent;
use App\Models\Describe\Describe;
use App\Models\User;
use App\Models\Workflow\WorkflowUserSync;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class FeeExpenseListener
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
     * @param  BusinessTripEvent $event
     * @return void
     */
    public function handle($event)
    {
        //$procsId = $event->procsId;
        //$process = Proc::where('id', $procsId)->orderBy('id', 'desc')->first();

        //$process_id = $process->process_id;
        //$user_id = $process->entry->user_id;
        //$entry_id = $process->entry->id;
        //$query = Financial::where(['entry_id'=>$entry_id,'user_id'=>$user_id]);
        //$finance = $query->first();
		//$finance->status = 3;

        //$finance->save();
    
    }
}

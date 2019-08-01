<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:31
 */

namespace App\Listeners;

use App\Events\InspectorStartEvent;
use App\Models\User;
use App\Models\Inspector\Inspector;
use App\Models\Workflow\Proc;
use App\Constant\ConstFile;
use App\Models\Workflow\WorkflowUserSync;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Auth;

class InspectorEndListener
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
        //
        $procsId = $event->procsId;
        $process = Proc::where('id', $procsId)->first();
        $entry_data = $process->entry->entry_data->toArray();
//        foreach ($entry_data as $key => $val) {
//            $data[$val['field_name']] = $val['field_value'];
//        }
        try {
            Inspector::where('entry_id',$process->entry_id)->update(['state'=>1]);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('督查审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}
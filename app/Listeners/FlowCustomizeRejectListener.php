<?php

namespace App\Listeners;

use App\Events\FlowExtraLogEvent;
use App\Models\Company;
use App\Models\Executive\Cars;
use App\Models\Vacations\VacationExtraRecord;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Proc;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use DB;

class FlowCustomizeRejectListener
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
     * @param  FlowExtraLogEvent  $event
     * @return void
     */
    public function handle($event)
    {
        $procsId = $event->procsId;
        $process = Proc::where('id', $procsId)->first();
        $entry_id = $process->entry_id;

        $flow = $process->flow;
        try {
            $data['status'] = Entry::STATUS_REJECTED;
            $data['updated_at'] = Carbon::now()->toDateTimeString();
            DB::table($flow->flow_no)
                ->where('entrise_id', $entry_id)
                ->update($data);
            switch ($flow->flow_no){
                case Entry::WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_USE:
                case Entry::WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_APPOINT:
                    $list = DB::table($flow->flow_no)
                        ->leftJoin('executive_cars_use_relation_car as b',$flow->flow_no.'.id', '=', 'b.cars_use_id')
                        ->where('entrise_id', $entry_id)
                        ->pluck('b.cars_id')
                        ->all();
                    Cars::updateCarStatus($list, Cars::CAR_STATUS_NORMAL);
                    break;
                default:
                    break;
            }
        } catch (\Exception $exception) {
            report($exception);
            Log::error($flow->flow_no.'审批驳回', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

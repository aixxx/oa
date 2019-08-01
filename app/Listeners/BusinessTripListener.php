<?php

namespace App\Listeners;

use App\Events\BusinessTripEvent;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BusinessTripListener
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
     * @param  BusinessTripEvent  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $procsId = $event->procsId;
        $process = Proc::where('id', $procsId)->first();
        $entry_data = $process->entry->entry_data;

        $userId = $process->user_id;
        $end_time = $begin_time = '';
        foreach ($entry_data as $entry_datum)
        {
            if($entry_datum->field_name == 'begin_time'){
                $begin_time = $entry_datum->field_value;
            }elseif ($entry_datum->field_name == 'end_time'){
                $end_time = $entry_datum->field_value;
            }elseif ($entry_datum->field_name == 'time_sub_by_hour'){
                $time_sub_by_hour = intval($entry_datum->field_value) * 60;
            }
            else{
                continue;
            }
        }
        $anomalyRes = AttendanceApiAnomaly::workflowCheck($userId, $begin_time, $end_time,
            AttendanceApiAnomaly::TRIP);

    }
}

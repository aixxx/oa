<?php

namespace App\Listeners;


use App\Events\OutsidePunchEvent;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use Log;

class OutsidePunchListener
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
     * @param  OutsidePunchEvent  $event
     */
    public function handle($event)
    {
        $process = Proc::where('id', $event->procs_id)->first();
        $entry_data = $process->entry->entry_data;
        $begin_time = '';
        $end_time = '';
        foreach ($entry_data as $entry_datum)
        {
            if($entry_datum->field_name == "begin_time"){
                $begin_time = $entry_datum->field_value;
            }elseif ($entry_datum->field_name == 'end_time'){
                $end_time = $entry_datum->field_value;
            }else{
                continue;
            }
        }
//        Log::debug('outside punch event:', $data);
        //修改考勤记录为正常
        $userId = $process->user_id;
        $anomalyRes = AttendanceApiAnomaly::workflowCheck($userId, $begin_time, $end_time, AttendanceApiAnomaly::LEAVEOUT);
    }
}

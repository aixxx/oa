<?php

namespace App\Listeners;

use App\Events\VacationPatchEvent;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\Workflow\Proc;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VacationPatchListener
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
     * @param  VacationPatchEvent  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $process = Proc::where('id', $event->procsId)->first();
        $entry_data = $process->entry->entry_data;
        $patch_time = '';
        $patch_type = '';
        foreach ($entry_data as $entry_datum)
        {
            if($entry_datum->field_name == "patch_time"){
                $patch_time = Carbon::parse(str_replace('.','-',$entry_datum->field_value));
            }elseif($entry_datum->field_name == 'patch_type'){
                $patch_type = $entry_datum->field_value;
            }else{
                continue;
            }
        }
//        $type = 2;
//        if($patch_type == '上班'){
//            $type = 1;
//        }
        $patch_type = $patch_type ?: 1;
        $userId = $process->user_id;
        AttendanceApiAnomaly::addClock($patch_type,
            $patch_time->toDateString(), $patch_time->toDateTimeString(), $userId);
    }
}

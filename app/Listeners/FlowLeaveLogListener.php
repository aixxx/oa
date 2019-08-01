<?php

namespace App\Listeners;

use App\Events\FlowLeaveLogEvent;
use App\Models\Attendance\VacationType;
use App\Models\Company;
use App\Models\Vacations\VacationLeaveRecord;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FlowLeaveLogListener
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
     * @param  FlowLeaveLogEvent  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $procsId = $event->procsId;

        $process = Proc::where('id', $procsId)->first();
        $entry = $process->entry;
        $entry_data = $entry->entry_data;
        /** @var Company $company */
        $company = $process->user->company;

        $data = [
            'uid' => $entry->user_id,
            'company_id' => $company->id,
            'entry_id' => $entry->id,
        ];
        foreach ($entry_data as $datum){
            if($datum->field_name == 'vacation_type'){
                $data[$datum->field_name] = isset(VacationType::$_name_ids[$datum->field_value]) ?
                    VacationType::$_name_ids[$datum->field_value] : 8;
            }else{
                $data[$datum->field_name] = $datum->field_value;
            }

        }

        $record = VacationLeaveRecord::query()->create($data);
    }
}

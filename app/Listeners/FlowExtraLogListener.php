<?php

namespace App\Listeners;

use App\Events\FlowExtraLogEvent;
use App\Models\Company;
use App\Models\Vacations\VacationExtraRecord;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FlowExtraLogListener
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
            $data[$datum->field_name] = $datum->field_value;
        }

        $record = VacationExtraRecord::query()->create($data);
    }
}

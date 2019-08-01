<?php

namespace App\Listeners;

use App\Events\FlowBusinessTripLogEvent;
use App\Models\Company;
use App\Models\Vacations\VacationBusinessTripRecord;
use App\Models\Vacations\VacationTripRecord;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FlowBusinessTripLogListener
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
     * @param  FlowBusinessTripLogEvent  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $procsId = $event->procsId;

        $process = Proc::where('id', $procsId)->first();
        $entry = $process->entry;
        $entry_data = $process->entry->entry_data;
        /** @var Company $company */
        $company = $process->user->company;

        $data = [
            'uid' => $entry->user_id,
            'company_id' => $company->id,
            'entry_id' => $entry->id,
        ];
        $trips = [];
        //[{"fd_begin_time":"2019-06-02 AM","fd_end_time":"2019-06-02 PM","fd_go_and_back":"单程","fd_traffic":"飞机","fd_start_of":"北京市,市辖区,东城区","fd_purpose":"北京市,市辖区,东城区","fd_time_sub_by_day":1}]
        foreach ($entry_data as $datum){
            if($datum->field_name == 'trips'){
                $trips = json_decode($datum->field_value, JSON_OBJECT_AS_ARRAY);
                continue;
            }
            if($datum->field_name == 'examine'){
                continue;
            }
            $data[$datum->field_name] = $datum->field_value;
        }

        $record = VacationBusinessTripRecord::query()->create($data);
        $arr = [
            'business_trip_id' => $record->id,
            'fd_traffic' => isset($trips['fd_traffic']) ?? '',
            'fd_go_and_back' => isset($trips['fd_go_and_back']) ?? '',
            'fd_start_of' => isset($trips['fd_start_of']) ?? '',
            'fd_purpose' => isset($trips['fd_purpose']) ?? '',
            'fd_begin_time' => isset($trips['fd_begin_time']) ?? '',
            'fd_end_time' => isset($trips['fd_end_time']) ?? '',
            'fd_time_sub_by_day' => isset($trips['fd_time_sub_by_day']) ?? '',
        ];
        VacationTripRecord::query()->create($arr);
    }
}

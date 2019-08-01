<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:31
 */

namespace App\Listeners;

use App\Events\InteStartEvent;
use App\Models\Intelligence\IntelligenceUsers;
use App\Models\Intelligence\Intelligence;
use App\Models\User;
use App\Models\Workflow\Proc;
use App\Constant\ConstFile;
use App\Models\Workflow\WorkflowUserSync;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Auth;

class InteStartListener
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
        foreach ($entry_data as $key => $val) {
            $data[$val['field_name']] = $val['field_value'];
        }
        try {
            $arr = [
                'inte_content' => isset($data['inte_content'])?$data['inte_content']:"",
                'inte_demand' => isset($data['inte_demand'])?$data['inte_demand']:"",
                'file_upload' => isset($data['file_upload'])?$data['file_upload']:"",
                'time' => isset($data['time'])?$data['time']:"",
                'bank' => isset($data['bank'])?$data['bank']:"",
                'card_num' => isset($data['card_num'])?$data['card_num']:"",
                'auditstate' => IntelligenceUsers::STATUS_AUDIT_IN,
                'entry_id' => $process->entry_id
            ];
            IntelligenceUsers::where(['user_id'=> $data['user_id'],'inte_id'=>$data['inte_id']])->update($arr);
            Intelligence::where('id',$data['inte_id'])->update(['state'=>3]);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('转正审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}
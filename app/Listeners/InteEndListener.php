<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:31
 */

namespace App\Listeners;

use App\Events\InteEndEvent;
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

class InteEndListener
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
            $res = IntelligenceUsers::where(['user_id' => $data['user_id'], 'inte_id' => $data['inte_id']])
                ->update(['auditstate' => IntelligenceUsers::STATUS_COMPLETE]);
            if ($res) {
                $arr = Intelligence::find($data['inte_id'], ['id','auditNum','userNum']);
                if ($arr->userNum - $arr->auditNum == 1) {
                    Intelligence::where('id', $data['inte_id'])->update(['state' => 4]);
                }
                $auditNum = $arr->auditNum + 1;
                Intelligence::where('id', $data['inte_id'])->update(['auditNum' => $auditNum]);
            }
        } catch (\Exception $exception) {
            report($exception);
            Log::error('转正审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:31
 */

namespace App\Listeners;

use App\Events\PositiveRejectEvent;
use App\Models\Describe\Describe;
use App\Models\User;
use App\Constant\ConstFile;
use App\Models\Workflow\WorkflowUserSync;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Auth;

class PositiveRejectListener
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
//        $procsId = $event->procsId;
//
//        $process = Proc::where('id', $procsId)->first();
//        $entry_data = $process->entry->entry_data->toArray();
//        foreach ($entry_data as $key => $val) {
//            $data[$val['field_name']] = $val['field_value'];
//        }
//        try {
////            $res = WorkflowUserSync::where('user_id', $data['user_id'])->update();
////            if (!$res) {
////                throw new \Exception('保存数据失败:' . json_encode($res, JSON_UNESCAPED_UNICODE));
////            }
//
//        } catch (\Exception $exception) {
//            report($exception);
//            Log::error('转正审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
//            throw $exception;
//        }
    }
}
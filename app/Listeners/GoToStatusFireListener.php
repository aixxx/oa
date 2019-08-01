<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Events\GoToStatusFireEvent;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;
use Exception;

class GoToStatusFireListener
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

    public function handle(GoToStatusFireEvent $event)
    {
        try {
            $process = Proc::with('entry')->findOrFail($event->procId);
            $data = [
                'entry_id' => $process->entry_id,
                'apply_user_id' => $process->entry->user_id,
                'user_id' => $process->entry->user_id,
                'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_FIRED,
                'content_json' => json_encode(['content' => '开除员工,员工进入被开除状态'], JSON_UNESCAPED_UNICODE)
            ];

            $re = (new WorkflowUserSync())->fill($data)->save();
            throw_if(!$re, new Exception('开除员工:数据同步失败'));
        } catch (Exception $e) {
            Log::error('同步数据到快照表出错:' . $e->getMessage());
        }
    }
}

<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;
use DB;
use Exception;
use App\Models\Workflow\WorkflowUserSync;

class GoToStatusConfirmLeaveListener
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

    public function handle($event)
    {
        try {
            $process = Proc::with('entry')->findOrFail($event->procsId);
            $data    = [
                'entry_id'      => $process->entry_id,
                'apply_user_id' => $process->entry->user_id,
                'user_id'       => $process->entry->user_id,
                'status'        => ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE_UNDER_CONFIRM_LEAVE,
                'content_json'  => json_encode(['content' => '离职交接流程审批完,进入离职最终确认状态'], JSON_UNESCAPED_UNICODE)
            ];
            DB::transaction(function () use ($data, $process) {
                $re = WorkflowUserSync::where('entry_id', $process->entry_id)
                    ->where('user_id', $process->entry->user_id)
                    ->where('status', ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_UNDER_HAND_OVER)
                    ->delete();
                throw_if(!$re, new Exception('删除待交接状态数据失败'));
                $res = (new WorkflowUserSync())->fill($data)->save();
                throw_if(!$res, new Exception('待离职确认:数据同步失败'));
            });

        } catch (Exception $e) {
            Log::error('同步数据到快照表出错:' . $e->getMessage());
        }
    }
}

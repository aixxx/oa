<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;
use DB;
use Exception;
use App\Models\Workflow\WorkflowUserSync;

class GoToWantedHandOverListener
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
            $process = Proc::with('entry')->find($event->procsId);
            $data    = [
                'entry_id'      => $process->entry_id,
                'apply_user_id' => $process->entry->user_id,
                'user_id'       => $process->entry->user_id,
                'status'        => ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_UNDER_HAND_OVER,
                'content_json'  => json_encode(['content' => '离职流程审批完,进入待交接状态'], JSON_UNESCAPED_UNICODE)
            ];
            DB::transaction(function () use ($data, $process) {
                $re = WorkflowUserSync::where('entry_id', $process->entry_id)
                    ->where('user_id', $process->entry->user_id)
                    ->where('status', ConstFile::WORKFLOW_USER_SYNC_STATUS_FIRED)
                    ->orWhere('status', ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE)
                    ->delete();
                throw_if(!$re, new Exception('删除待离职数据失败:开除或者主动离职'));
                $res = (new WorkflowUserSync())->fill($data)->save();
                throw_if(!$res, new Exception('待交接数据同步失败'));
            });

        } catch (Exception $e) {
            Log::error('同步数据到快照表出错:' . $e->getMessage());
        }
    }
}

<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Models\User;
use App\Models\Workflow\Proc;
use App\Models\Workflow\WorkflowUserSync;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use DB;

class GoToWantedContractListener
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
            $data = [
                'apply_user_id' => $process->entry->user_id,
                'user_id' => $process->entry->user_id,
                'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_PENDING_CONTRACT,
                'content_json' => json_encode(['content' => '入职流程审批完,进入待签合同状态'], JSON_UNESCAPED_UNICODE),
            ];

            DB::transaction(function () use ($process, $data) {
                $re = WorkflowUserSync::where('entry_id', $process->entry_id)
                    ->where('user_id', $process->entry->user_id)
                    ->where('status', ConstFile::WORKFLOW_USER_SYNC_STATUS_PENDING_ENTRY)
                    ->delete();
                throw_if(!$re, new Exception('待合同:删除待入职数据没有成功'));
                $res = (new WorkflowUserSync())->fill($data)->save();
                throw_if(!$res, new Exception('待合同:同步数据到快照表没有成功'));
                $result = User::findOrFail($process->entry->user_id)->update(['status'=>User::STATUS_JOIN]);
                throw_if(!$result, new Exception('待合同:更新用户状态为 入职 没有成功'));
            });
        } catch (Exception $e) {
            Log::error('同步数据到快照表出错:' . $e->getMessage());
        }
    }
}

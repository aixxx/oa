<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Events\GoToStatusActiveLeaveEvent;
use App\Events\Event;
use App\Models\Contract\Contract;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Workflow\WorkflowUserSync;
use App\Models\User;

class GoToStatusActiveLeaveListener
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
                'entry_id' => $process->entry_id,
                'apply_user_id' => $process->entry->user_id,
                'user_id' => $process->entry->user_id,
                'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_ACTIVE_LEAVE,
                'content_json' => json_encode(['content' => '主动离职,员工进入主动离职状态'], JSON_UNESCAPED_UNICODE)
            ];
            $user = User::findOrFail($process->entry->user_id);
            throw_if(!$user, new Exception(sprintf('ID为%s的用户不存在', $process->entry->user_id)));
            throw_if(User::STATUS_JOIN != $user->status, new Exception(sprintf('ID为%s的用户目前不是在职状态', $process->entry->user_id)));
            $re = (new WorkflowUserSync())->fill($data)->save();
            throw_if(!$re, new Exception('员工主动离职数据同步失败'));
        } catch (Exception $e) {
            Log::error('同步数据到快照表出错:' . $e->getMessage());
        }
    }
}

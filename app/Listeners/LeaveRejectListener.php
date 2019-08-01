<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Workflow\WorkflowUserSync;
use App\Models\User;

class LeaveRejectListener
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
            $re = WorkflowUserSync::where('entry_id', $process->entry_id)
                ->where('user_id', $process->entry->user_id)
                ->delete();
            throw_if(!$re, new Exception('删除待入职数据同步记录没有成功'));
        } catch (Exception $e) {
            Log::error('同步数据到快照表出错:' . $e->getMessage());
        }
    }
}

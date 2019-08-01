<?php

namespace App\Listeners;

use App\Models\Salary\SalaryRecord;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use DB;

class PassSalaryRecordListener
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
            SalaryRecord::where('entry_id', $process->entry_id)
                ->where('status_entry', SalaryRecord::STATUS_IN_HAND)
                ->update(['status_entry' => SalaryRecord::STATUS_PASS]);
        } catch (Exception $e) {
            Log::error('同步数据到快照表出错:' . $e->getMessage());
        }
    }
}

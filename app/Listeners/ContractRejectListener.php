<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Events\ContractRejectEvent;
use App\Events\Event;
use App\Models\Contract\Contract;
use App\Models\User;
use App\Models\Workflow\Proc;
use App\Models\Workflow\WorkflowUserSync;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ContractRejectListener
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
     * @param  Event  $event
     * @return void
     */
    public function handle(ContractRejectEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $contractData = array();
        $contractData['entrise_id'] = $process->entry_id;
        $contractData['status'] = ConstFile::CONTRACT_STATUS_THR;
        try {
            \DB::beginTransaction();
            Contract::workflowImport($contractData);
            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollback();
            report($exception);
            Log::error('合同审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

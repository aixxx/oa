<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Events\ContractPassEvent;
use App\Events\Event;
use App\Models\Contract\Contract;
use App\Models\User;
use App\Models\Workflow\Proc;
use App\Models\Workflow\WorkflowUserSync;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ContractPassListener
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
    public function handle(ContractPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $contract = Contract::where(['entrise_id'=>$process->entry_id])->first();
        $contractData = array();
        $contractData['entrise_id'] = $process->entry_id;
        $contractData['status'] = ConstFile::CONTRACT_STATUS_TWO;
        $userData = array();
        $userData['entrise_id'] = $process->entry_id;
        $userData['contract_status'] = ConstFile::USERS_CONTRACT_STATUS_SIGNED;
        $userData['status'] = User::STATUS_JOIN;
        try {

            \DB::beginTransaction();
            (new WorkflowUserSync())->where(['user_id'=>Q($contract,'user_id')])->delete();
            if(Q($contract,'probation') == ConstFile::CONTRACT_PROBATION_ONE){
                $data = [
                    'apply_user_id' => \Auth::id(),
                    'user_id' => Q($contract,'user_id'),
                    'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_PAY_PACKAGE,
                    'content_json' => json_encode(['content' => '合同审批完成未选择试用期，直接进入薪资包状态'], JSON_UNESCAPED_UNICODE)
                ];
                $userData['is_positive'] = User::STATUS_IS_POSITIVE;
            } else {
                $data = [
                    'apply_user_id' => \Auth::id(),
                    'user_id' => Q($contract,'user_id'),
                    'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_WAITING_TO_RUEN_POSITIVE,
                    'content_json' => json_encode(['content' => '合同审批完成，进入转正状态'], JSON_UNESCAPED_UNICODE)
                ];
            }
            (new WorkflowUserSync)->fill($data)->save();
            Contract::workflowImport($contractData);
            Contract::workflowUserContract($userData);
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('合同审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\ContractRejectEvent;
use App\Events\CorporateAssetsRepairRejectEvent;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsRepairRejectListener
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @param ContractRejectEvent $event
     * @throws \Exception
     */
    public function handle(CorporateAssetsRepairRejectEvent $event)
    {
        $process = Proc::where('id', $event->procId)->first();
        try {
            \DB::beginTransaction();
            (new CorporateAssetsSync())->where(['entry_id'=>Q($process,'entry_id'),'type'=>CorporateAssetsConstant::ASSETS_RELATION_TYPE_REPAIR])->delete();
            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollback();
            report($exception);
            Log::error('资产借用', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

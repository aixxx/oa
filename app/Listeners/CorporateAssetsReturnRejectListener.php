<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\ContractRejectEvent;
use App\Events\CorporateAssetsReturnRejectEvent;
use App\Events\CorporateAssetsUseRejectEvent;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsReturnRejectListener
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
    public function handle(CorporateAssetsReturnRejectEvent $event)
    {
        $process = Proc::where('id', $event->procId)->first();
        try {
            \DB::beginTransaction();
            (new CorporateAssetsSync())->where(['entry_id'=>Q($process,'entry_id'),'type'=>CorporateAssetsConstant::ASSETS_RELATION_TYPE_RETURN])->delete();
            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollback();
            report($exception);
            Log::error('资产归还', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

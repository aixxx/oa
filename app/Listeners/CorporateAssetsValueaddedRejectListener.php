<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\ContractRejectEvent;
use App\Events\CorporateAssetsValueaddedRejectEvent;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsValueaddedRejectListener
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
    public function handle(CorporateAssetsValueaddedRejectEvent $event)
    {
        $process = Proc::where('id', $event->procId)->first();
        try {
            \DB::beginTransaction();
            (new CorporateAssetsSync())->where(['entry_id'=>Q($process,'entry_id'),'type'=>CorporateAssetsConstant::ASSETS_RELATION_TYPE_VALUEADDED])->delete();
            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollback();
            report($exception);
            Log::error('资产增值', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

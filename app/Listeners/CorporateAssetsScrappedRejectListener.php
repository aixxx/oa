<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\ContractRejectEvent;
use App\Events\CorporateAssetsScrappedRejectEvent;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsScrappedRejectListener
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
    public function handle(CorporateAssetsScrappedRejectEvent $event)
    {
        $process = Proc::where('id', $event->procId)->first();
        try {
            \DB::beginTransaction();
            (new CorporateAssetsSync())->where(['entry_id'=>Q($process,'entry_id'),'type'=>CorporateAssetsConstant::ASSETS_RELATION_TYPE_SCRAPPED])->delete();
            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollback();
            report($exception);
            Log::error('资产报废', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

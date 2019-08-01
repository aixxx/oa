<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\ContractRejectEvent;
use App\Events\CorporateAssetsUseRejectEvent;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsUseRejectListener
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
    public function handle(CorporateAssetsUseRejectEvent $event)
    {
        $process = Proc::where('id', $event->procId)->first();
        $assetslist = CorporateAssets::whereHas('hasOneCorporateAssetsSyncUse', function ($query) use ($process) {
            $query->where('entry_id', $process->entry_id);
        })->get();
        try {
            \DB::beginTransaction();
            (new CorporateAssetsSync())->where(['entry_id'=>Q($process,'entry_id'),'type'=>CorporateAssetsConstant::ASSETS_RELATION_TYPE_USE])->delete();
            Collect($assetslist)->each(function ($item, $key) {
                $assetsData['status'] = CorporateAssetsConstant::ASSETS_STATUS_IDLE;
                CorporateAssets::query()->where('id',$item->id)->update($assetsData);
            });
            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollback();
            report($exception);
            Log::error('资产领用', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

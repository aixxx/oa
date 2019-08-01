<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\ContractRejectEvent;
use App\Events\CorporateAssetsBorrowRejectEvent;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsBorrow;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsBorrowRejectListener
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
    public function handle(CorporateAssetsBorrowRejectEvent $event)
    {
        $process = Proc::where('id', $event->procId)->first();
        $assetslist = CorporateAssets::whereHas('hasOneCorporateAssetsSyncBorrow', function ($query) use ($process) {
            $query->where('entry_id', $process->entry_id);
        })->get();
        try {
            \DB::beginTransaction();
            (new CorporateAssetsSync())->where(['entry_id' => Q($process, 'entry_id'), 'type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_BORROW])->delete();
            Collect($assetslist)->each(function ($item, $key) {
                $assetsData['status'] = CorporateAssetsConstant::ASSETS_STATUS_IDLE;
                CorporateAssets::query()->where('id',$item->id)->update($assetsData);
            });
            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollback();
            report($exception);
            Log::error('资产借用', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

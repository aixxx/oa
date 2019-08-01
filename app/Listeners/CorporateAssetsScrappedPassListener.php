<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\CorporateAssetsScrappedPassEvent;
use App\Events\Event;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Assets\CorporateAssetsScrapped;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsScrappedPassListener
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
    public function handle(CorporateAssetsScrappedPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $assets_id = CorporateAssets::whereHas('hasOneCorporateAssetsSyncScrapped',function($query)use($process){
            $query->where('entry_id',$process->entry_id);
        })->pluck('id');
        $scrapped = CorporateAssetsScrapped::where('entry_id',Q($process,'entry_id'))->first();
        $user = $scrapped->hasOneUser;
        $assetsData['id'] = $assets_id;
        $assetsData['status'] = CorporateAssetsConstant::ASSETS_STATUS_SCRAPPED;
        (new CorporateAssetsSync())->whereIn('assets_id', $assets_id)->whereIn('type', [CorporateAssetsConstant::ASSETS_RELATION_TYPE_SCRAPPED])->delete();

        try {
            \DB::beginTransaction();
            Collect($assets_id)->each(function ($item, $key) use ($process,$scrapped,$user) {
                $data['assets_id'] = $item;
                $data['event_id'] = Q($scrapped,'id');
                $data['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_SCRAPPED;
                $data['remarks'] = Q($scrapped,'remarks');
                $data['apply_user_id'] = Q($scrapped,'apply_user_id');
                $data['user_id'] = Q($scrapped,'user_id');
                $data['entry_id'] = Q($process,'entry_id');
                $data['user_name'] = $user->chinese_name;
                $data['type_name'] = CorporateAssetsConstant::$assets_relation_type[CorporateAssetsConstant::ASSETS_RELATION_TYPE_SCRAPPED];
                CorporateAssetsRelation::create($data);
            });
            CorporateAssets::workflowImport($assetsData);
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('资产报废', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

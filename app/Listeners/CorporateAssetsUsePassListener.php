<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\CorporateAssetsUsePassEvent;
use App\Events\Event;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Assets\CorporateAssetsUse;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsUsePassListener
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
    public function handle(CorporateAssetsUsePassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $assets_id = CorporateAssets::whereHas('hasOneCorporateAssetsSyncUse',function($query)use($process){
            $query->where('entry_id',$process->entry_id);
        })->pluck('id');

        $use = CorporateAssetsUse::where('entry_id',Q($process,'entry_id'))->first();
        $user = $use->hasOneUser;
        $assetsData['id'] = $assets_id;
        $assetsData['status'] = CorporateAssetsConstant::ASSETS_STATUS_USING;

        try {
            \DB::beginTransaction();
            Collect($assets_id)->each(function ($item, $key) use ($process,$use,$user) {
                $data['assets_id'] = $item;
                $data['event_id'] = Q($use,'id');
                $data['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_USE;
                $data['remarks'] = Q($use,'remarks');
                $data['apply_user_id'] = Q($use,'apply_user_id');
                $data['user_id'] = Q($use,'user_id');
                $data['entry_id'] = Q($process,'entry_id');
                $data['user_name'] = $user->chinese_name;
                $data['type_name'] = CorporateAssetsConstant::$assets_relation_type[CorporateAssetsConstant::ASSETS_RELATION_TYPE_USE];
                CorporateAssetsRelation::create($data);
            });
            CorporateAssetsSync::query()->where(['entry_id' => Q($process, 'entry_id'),'type'=>CorporateAssetsConstant::ASSETS_RELATION_TYPE_USE])->update(['user_id' => $use['user_id']]);
            CorporateAssets::workflowImport($assetsData);
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('资产领用', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

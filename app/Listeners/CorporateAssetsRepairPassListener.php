<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\CorporateAssetsRepairPassEvent;
use App\Events\Event;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Assets\CorporateAssetsRepair;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsRepairPassListener
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
    public function handle(CorporateAssetsRepairPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $assets_id = CorporateAssets::whereHas('hasOneCorporateAssetsSyncRepair',function($query)use($process){
            $query->where('entry_id',$process->entry_id);
        })->pluck('id');
        $repair = CorporateAssetsRepair::where('entry_id',Q($process,'entry_id'))->first();
        $user = $repair->hasOneUser;
        $assetsData['id'] = $assets_id;
        $assetsData['status'] = CorporateAssetsConstant::ASSETS_STATUS_REPAIR;
        try {
            \DB::beginTransaction();
            Collect($assets_id)->each(function ($item, $key) use ($process,$repair,$user) {
                $data['assets_id'] = $item;
                $data['event_id'] = Q($repair,'id');
                $data['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_REPAIR;
                $data['remarks'] = Q($repair,'remarks');
                $data['apply_user_id'] = Q($repair,'apply_user_id');
                $data['user_id'] = Q($repair,'user_id');
                $data['entry_id'] = Q($process,'entry_id');
                $data['user_name'] = $user->chinese_name;
                $data['type_name'] = CorporateAssetsConstant::$assets_relation_type[CorporateAssetsConstant::ASSETS_RELATION_TYPE_REPAIR];
                CorporateAssetsRelation::create($data);
            });
            CorporateAssetsSync::query()->where(['entry_id' => Q($process, 'entry_id'),'type'=>CorporateAssetsConstant::ASSETS_RELATION_TYPE_REPAIR])->update(['user_id' => $repair['user_id']]);
            CorporateAssets::workflowImport($assetsData);
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('资产送修', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

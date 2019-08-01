<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\CorporateAssetsDepreciationPassEvent;
use App\Events\Event;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsDepreciation;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CorporateAssetsDepreciationPassListener
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
     * @param  Event $event
     * @return void
     */
    public function handle(CorporateAssetsDepreciationPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $assetslist = CorporateAssets::whereHas('hasOneCorporateAssetsSyncDepreciation', function ($query) use ($process) {
            $query->where('entry_id', $process->entry_id);
        })->get();
        $depreciation = CorporateAssetsDepreciation::where('entry_id', Q($process, 'entry_id'))->first();

        $user = $depreciation->hasOneUser;
        $assets_id = array_column($assetslist->toArray(), 'id');
        (new CorporateAssetsSync())->whereIn('assets_id', $assets_id)->whereIn('type', [CorporateAssetsConstant::ASSETS_RELATION_TYPE_DEPRECIATION])->delete();
        try {
            \DB::beginTransaction();
            Collect($assetslist)->each(function ($item, $key) use ($process, $depreciation, $user) {
                $data['assets_id'] = $item->id;
                $data['event_id'] = Q($depreciation, 'id');
                $data['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_DEPRECIATION;
                $data['remarks'] = Q($depreciation, 'remarks');
                $data['apply_user_id'] = Q($depreciation, 'apply_user_id');
                $data['user_id'] = Q($depreciation, 'user_id');
                $data['entry_id'] = Q($process, 'entry_id');
                $data['user_name'] = $user->chinese_name;
                $data['type_name'] = CorporateAssetsConstant::$assets_relation_type[CorporateAssetsConstant::ASSETS_RELATION_TYPE_DEPRECIATION];
                CorporateAssetsRelation::create($data);
                $remaining_at = Carbon::createFromTimeString($item->remaining_at)->addMonths($item->depreciation_interval);
                $remaining_time = Carbon::createFromTimeString($item->remaining_at)->addMonths($item->depreciation_interval)->timestamp;
                $depreciation_cycle = Carbon::createFromTimeString($item->buy_time)->addMonths($item->depreciation_cycle)->timestamp;
                $assetsData['remaining_at'] = $remaining_at;
                if ($remaining_time >= $depreciation_cycle) {
                    $assetsData['depreciation_status'] = CorporateAssetsConstant::ASSETS_DEPRECIATION_STATUS_NO;
                }
                CorporateAssets::query()->where('id', $item->id)->update($assetsData);
            });
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('资产增值', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

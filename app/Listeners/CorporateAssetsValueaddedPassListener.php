<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\CorporateAssetsValueaddedPassEvent;
use App\Events\Event;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Assets\CorporateAssetsValueadded;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsValueaddedPassListener
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
    public function handle(CorporateAssetsValueaddedPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $assets_id = CorporateAssets::whereHas('hasOneCorporateAssetsSyncValueadded',function($query)use($process){
            $query->where('entry_id',$process->entry_id);
        })->pluck('id');
        $valueadded = CorporateAssetsValueadded::where('entry_id',Q($process,'entry_id'))->first();
        $user = $valueadded->hasOneUser;
        (new CorporateAssetsSync())->whereIn('assets_id', $assets_id)->whereIn('type', [CorporateAssetsConstant::ASSETS_RELATION_TYPE_VALUEADDED])->delete();
        try {
            \DB::beginTransaction();
            Collect($assets_id)->each(function ($item, $key) use ($process,$valueadded,$user) {
                $data['assets_id'] = $item;
                $data['event_id'] = Q($valueadded,'id');
                $data['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_VALUEADDED;
                $data['remarks'] = Q($valueadded,'remarks');
                $data['apply_user_id'] = Q($valueadded,'apply_user_id');
                $data['user_id'] = Q($valueadded,'user_id');
                $data['entry_id'] = Q($process,'entry_id');
                $data['user_name'] = $user->chinese_name;
                $data['type_name'] = CorporateAssetsConstant::$assets_relation_type[CorporateAssetsConstant::ASSETS_RELATION_TYPE_VALUEADDED];
                CorporateAssetsRelation::create($data);
            });
            //CorporateAssetsSync::query()->where(['entry_id' => Q($process, 'entry_id'), 'type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_VALUEADDED])->update(['user_id' => Q($valueadded,'user_id')]);
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('资产增值', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

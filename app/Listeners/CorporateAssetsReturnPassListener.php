<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\CorporateAssetsReturnPassEvent;
use App\Events\Event;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Assets\CorporateAssetsReturn;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsReturnPassListener
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
    public function handle(CorporateAssetsReturnPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $assets_id = CorporateAssets::whereHas('hasOneCorporateAssetsSyncReturn', function ($query) use ($process) {
            $query->where('entry_id', $process->entry_id);
        })->pluck('id');
        $return = CorporateAssetsReturn::where('entry_id', Q($process, 'entry_id'))->first();
        $user = $return->hasOneUser;
        $assetsData['id'] = $assets_id;
        $assetsData['status'] = CorporateAssetsConstant::ASSETS_STATUS_IDLE;

        try {
            \DB::beginTransaction();
            (new CorporateAssetsSync())->whereIn('assets_id', $assets_id)->whereIn('type', [CorporateAssetsConstant::ASSETS_RELATION_TYPE_USE, CorporateAssetsConstant::ASSETS_RELATION_TYPE_BORROW, CorporateAssetsConstant::ASSETS_RELATION_TYPE_TRANSFER, CorporateAssetsConstant::ASSETS_RELATION_TYPE_RETURN])->delete();

            Collect($assets_id)->each(function ($item, $key) use ($process, $return, $user) {
                $data['assets_id'] = $item;
                $data['event_id'] = Q($return, 'id');
                $data['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_RETURN;
                $data['remarks'] = Q($return, 'remarks');
                $data['apply_user_id'] = Q($return, 'apply_user_id');
                $data['user_id'] = Q($return, 'user_id');
                $data['entry_id'] = Q($process, 'entry_id');
                $data['user_name'] = $user->chinese_name;
                $data['type_name'] = CorporateAssetsConstant::$assets_relation_type[CorporateAssetsConstant::ASSETS_RELATION_TYPE_RETURN];
                CorporateAssetsRelation::create($data);
            });
            CorporateAssets::workflowImport($assetsData);
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('资产归还', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

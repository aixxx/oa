<?php

namespace App\Listeners;

use App\Constant\CorporateAssetsConstant;
use App\Events\CorporateAssetsTransferPassEvent;
use App\Events\CorporateAssetsUsePassEvent;
use App\Events\Event;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsBorrow;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Assets\CorporateAssetsTransfer;
use App\Models\Workflow\Proc;
use Illuminate\Support\Facades\Log;

class CorporateAssetsTransferPassListener
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
    public function handle(CorporateAssetsTransferPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $assets_id = CorporateAssets::whereHas('hasOneCorporateAssetsSyncTransfer', function ($query) use ($process) {
            $query->where('entry_id', $process->entry_id);
        })->pluck('id');
        $transfer = CorporateAssetsTransfer::where('entry_id', Q($process, 'entry_id'))->first();
        $user = $transfer->hasOneUser;
        $assetsData['id'] = $assets_id;
        $assetsData['status'] = CorporateAssetsConstant::ASSETS_STATUS_USING;
        try {
            \DB::beginTransaction();
            (new CorporateAssetsSync())->whereIn('assets_id', $assets_id)->whereIn('type', [CorporateAssetsConstant::ASSETS_RELATION_TYPE_USE, CorporateAssetsConstant::ASSETS_RELATION_TYPE_BORROW])->delete();
            Collect($assets_id)->each(function ($item, $key) use ($process, $transfer, $user) {
                $data['assets_id'] = $item;
                $data['event_id'] = Q($transfer, 'id');
                $data['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_TRANSFER;
                $data['remarks'] = Q($transfer, 'remarks');
                $data['apply_user_id'] = Q($transfer, 'apply_user_id');
                $data['user_id'] = Q($transfer, 'transfer_to_user_id');
                $data['entry_id'] = Q($process, 'entry_id');
                $data['user_name'] = $user->chinese_name;
                $data['type_name'] = CorporateAssetsConstant::$assets_relation_type[CorporateAssetsConstant::ASSETS_RELATION_TYPE_TRANSFER];
                CorporateAssetsRelation::create($data);
            });
            CorporateAssetsSync::query()->where(['entry_id' => Q($process, 'entry_id'), 'type' => CorporateAssetsConstant::ASSETS_RELATION_TYPE_TRANSFER])->update(['user_id' => $transfer['transfer_to_user_id']]);
            CorporateAssets::workflowImport($assetsData);
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('资产调拨', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

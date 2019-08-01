<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Constant\CorporateAssetsConstant;
use App\Events\ContractPassEvent;
use App\Events\CorporateAssetsBorrowPassEvent;
use App\Events\Event;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsBorrow;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Assets\CorporateAssetsSync;
use App\Models\Contract\Contract;
use App\Models\User;
use App\Models\Workflow\Proc;
use App\Models\Workflow\WorkflowUserSync;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CorporateAssetsBorrowPassListener
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
    public function handle(CorporateAssetsBorrowPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $assets_id = CorporateAssets::whereHas('hasOneCorporateAssetsSyncBorrow', function ($query) use ($process) {
            $query->where('entry_id', $process->entry_id);
        })->pluck('id');
        $borrow = CorporateAssetsBorrow::where('entry_id', Q($process, 'entry_id'))->first();
        $user = $borrow->hasOneUser;
        $assetsData['id'] = $assets_id;
        $assetsData['status'] = CorporateAssetsConstant::ASSETS_STATUS_USING;

        try {
            \DB::beginTransaction();
            Collect($assets_id)->each(function ($item, $key) use ($process, $borrow, $user) {
                $data['assets_id'] = $item;
                $data['event_id'] = Q($borrow, 'id');
                $data['type'] = CorporateAssetsConstant::ASSETS_RELATION_TYPE_BORROW;
                $data['remarks'] = Q($borrow, 'remarks');
                $data['apply_user_id'] = Q($borrow, 'apply_user_id');
                $data['user_id'] = Q($borrow, 'user_id');
                $data['entry_id'] = Q($process, 'entry_id');
                $data['user_name'] = $user->chinese_name;
                $data['type_name'] = CorporateAssetsConstant::$assets_relation_type[CorporateAssetsConstant::ASSETS_RELATION_TYPE_BORROW];
                CorporateAssetsRelation::create($data);
            });
            CorporateAssetsSync::query()->where(['entry_id' => Q($process, 'entry_id'),'type'=>CorporateAssetsConstant::ASSETS_RELATION_TYPE_BORROW])->update(['user_id' => $borrow['user_id']]);
            CorporateAssets::workflowImport($assetsData);
            \DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            Log::error('合同审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}

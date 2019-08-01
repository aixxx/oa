<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:31
 */

namespace App\Listeners;

use App\Events\AdministrativeContractCentreEvent;
use App\Models\Describe\Describe;
use App\Models\User;
use App\Constant\ConstFile;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\Proc;
use App\Models\Administrative\Contract;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Auth;

class AdministrativeContractCentreListener
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
     * @param  BusinessTripEvent $event
     * @return void
     */
    public function handle($event)
    {
        //
        $procsId = $event->procsId;

        $process = Proc::where('id', $procsId)->first();
//        $entry_data = $process->entry->entry_data->toArray();
        try {
            $arr = Contract::where('entry_id',$process->entry_id)->first(['entry_id','process_userId']);
            $set = $arr->process_userId.Auth::id().'&';
            Contract::where('entry_id',$process->entry_id)->update(['process_userId'=>$set]);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('行政合同', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}
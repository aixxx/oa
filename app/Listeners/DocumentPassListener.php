<?php

namespace App\Listeners;

use App\Constant\ConstFile;
use App\Events\ContractPassEvent;
use App\Events\Event;
use App\Models\Contract\Contract;
use App\Models\Document\Document;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class DocumentPassListener
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
    public function handle(ContractPassEvent $event)
    {
        //
        $process = Proc::where('id', $event->procId)->first();
        $contractData = array();
        $entryId = $process->entry_id;
        Document::where('entry_id','=',$entryId)->update([
            'status'=>1,
            'updated_at'=>date("Y-m-d H:i:s"),
        ]);
    }
}

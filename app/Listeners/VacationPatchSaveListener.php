<?php

namespace App\Listeners;

use App\Events\VacationPatchSaveEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VacationPatchSaveListener
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
     * @param  VacationPatchSaveEvent  $event
     * @return void
     */
    public function handle($event)
    {
        //
       $procsId =$event->procsId;
       dd($procsId);
    }
}

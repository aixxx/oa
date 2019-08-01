<?php

namespace App\Listeners;

use App\Events\CreateUserAccountEvent;
use App\Models\Workflow\Proc;
use App\UserAccount\Account;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use PHPUnit\Framework\MockObject\Stub\Exception;

class CreateUserAccountEventListener
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
     * @param  CreateUserAccountEvent $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $process = Proc::with('entry')->findOrFail($event->procsId);
            $user_id = $process->entry->user_id;
            $account = new Account();
            $res     = $account->setUser($user_id)->create();
            //throw_if(!$res, new Exception('同步用户账户数据出错'));
        } catch (Exception $e) {
            Log::error('同步用户账户数据出错:' . $e->getMessage());
        }

    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/19
 * Time: 13:39
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class LeaveRejectEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $procsId;

    /**
     * Create a new event instance.
     *
     * @param $procsID
     */
    public function __construct($procsID)
    {
        //
        $this->procsId = $procsID;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
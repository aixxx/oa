<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CorporateAssetsBorrowPassEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $procId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($procId)
    {
        $this->procId = $procId;
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

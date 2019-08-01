<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class AttendanceLeaveEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $procsId;

    /**
     * Create a new event instance.
     *
     * @param $procsId
     */
    public function __construct($procsId)
    {
        //
        $this->procsId = $procsId;
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

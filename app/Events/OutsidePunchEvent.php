<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class OutsidePunchEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public  $procs_id;

    /**
     * Create a new event instance.
     *
     * @param $procs_id
     */
    public function __construct($procs_id)
    {
        //
        $this->procs_id = $procs_id;
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

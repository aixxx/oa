<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReturnSaleOrderPassEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $procsId;

    /**  采购单审核通过监听事件
     * Create a new event instance.
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

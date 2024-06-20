<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $chatData;

    public function __construct($chatData)
    {
        $this->chatData = $chatData;
        // dd($chatData);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastWith(): array
    {
        return ['chat' => $this->chatData];
    }

    public function broadcastAs(): string
    {
        return 'getChatMessage';
    }

    public function broadcastOn(): array
    {
        return [
            // new Channel('broadcastMessage'.$this->chatData->sender_id),
            new Channel('broadcastMessage'),
        ];
    }
}

<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $chatMessage;

    public function __construct(Chat $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ticket.' . $this->chatMessage->ticket_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->chatMessage->id,
            'ticket_id' => $this->chatMessage->ticket_id,
            'user_id' => $this->chatMessage->user_id,
            'message' => $this->chatMessage->message,
            'is_read' => $this->chatMessage->is_read,
            'created_at' => $this->chatMessage->created_at,
            'user' => [
                'id' => $this->chatMessage->user->id,
                'name' => $this->chatMessage->user->name,
                'role' => $this->chatMessage->user->role,
            ],
        ];
    }
}

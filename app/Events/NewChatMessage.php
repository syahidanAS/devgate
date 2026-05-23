<?php

namespace App\Events;

use App\Models\ChatSessionMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatSessionMessage $message;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatSessionMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $session = $this->message->session;
        return [
            new \Illuminate\Broadcasting\Channel('chat.session.' . $this->message->chat_session_id),
            new \Illuminate\Broadcasting\Channel('admin.chat.' . $session->category),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewChatMessage';
    }
}

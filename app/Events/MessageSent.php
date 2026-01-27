<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        // Channel khusus berdasarkan ID percakapan
        return [
            new PrivateChannel('chat.' . $this->message->conversation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id'              => $this->message->id,
                'user_id'         => $this->message->user_id, // Sesuaikan dari gambar DB
                'conversation_id' => $this->message->conversation_id,
                'body'            => $this->message->body, // Di DB kamu namanya 'body'
                'created_at'      => $this->message->created_at->format('H:i'),
                'user' => [
                    'name'         => $this->message->user->name,
                    'foto_profile' => $this->message->user->foto_profile ?? null
                ],
            ],
        ];
    }
}

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
        // Sangat Penting: Pastikan relasi di-load agar broadcastWith tidak error/kosong
        $this->message = $message->load(['sender', 'receiver']);
    }

    public function broadcastOn(): array
    {
        // Menggunakan array agar sesuai dengan standar Laravel terbaru
        return [
            new PrivateChannel('chat.' . $this->message->receiver_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id'         => $this->message->id,
                'sender_id'   => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'message'     => $this->message->message,
                'created_at'  => $this->message->created_at->diffForHumans(),
                'sender' => [
                    'name'         => $this->message->sender->name,
                    'foto_profile' => $this->message->sender->foto_profile ?? null
                ],
                // Tambahkan data receiver jika dibutuhkan di frontend
            ],
        ];
    }
}

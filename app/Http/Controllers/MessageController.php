<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\UserTyping;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function index($user_id = null)
    {
        $authId = auth()->id();

        // 1. Ambil semua daftar percakapan untuk sidebar
        $conversations = Conversation::with(['sender', 'receiver', 'messages'])
            ->where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->orderBy('last_message_at', 'desc')
            ->get();

        $activeChat = null;

        // 2. Jika ada user_id yang diklik, cari atau buat percakapannya
        if ($user_id) {
            $activeChat = Conversation::where(function ($q) use ($authId, $user_id) {
                $q->where('sender_id', $authId)->where('receiver_id', $user_id);
            })->orWhere(function ($q) use ($authId, $user_id) {
                $q->where('sender_id', $user_id)->where('receiver_id', $authId);
            })->first();

            if (!$activeChat) {
                $activeChat = Conversation::create([
                    'sender_id' => $authId,
                    'receiver_id' => $user_id,
                    'last_message_at' => now(),
                ]);
                // Refresh daftar agar chat baru muncul di sidebar
                return redirect()->route('chat.index', $user_id);
            }
        }

        return view('chat.index', compact('conversations', 'activeChat'));
    }

    public function store(Request $request, $conversation_id)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation_id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        // Update timestamp percakapan agar naik ke paling atas di sidebar
        $message->conversation()->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => $message,
            // Format waktu untuk dikirim ke UI
            'time' => $message->created_at->format('H:i')
        ]);
    }
}

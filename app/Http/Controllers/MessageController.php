<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Str;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;


class MessageController extends Controller
{
    public function index($user_id = null)
    {
        $authId = Auth::id();

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
        try {
            $request->validate(['body' => 'required|string']);

            $message = Message::create([
                'conversation_id' => $conversation_id,
                'user_id' => Auth::id(),
                'body' => $request->body,
            ]);

            $conversation = $message->conversation;
            $conversation->update(['last_message_at' => now()]);

            // LOGIKA RECIPIENT: Sesuaikan dengan kolom di tabel conversations Anda
            $recipientId = ($conversation->sender_id == Auth::id())
                ? $conversation->receiver_id
                : $conversation->sender_id;

            $recipient = User::find($recipientId);

            if ($recipient && $recipient->id !== Auth::id()) {
                $recipient->notify(new GeneralNotification([
                    'title'   => 'Pesan Baru dari ' . Auth::user()->name,
                    'message' => Str::limit($message->body, 50),
                    'icon'    => '💬',
                    'color'   => 'bg-blue-100 text-blue-700',
                    'url'     => route('chat.index', ['user_id' => Auth::id()]),
                ]));
            }

            // Jalankan Broadcast
            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'time' => $message->created_at->format('H:i')
            ]);
        } catch (\Exception $e) {
            // Jika ada error, kirim pesan errornya ke console browser
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

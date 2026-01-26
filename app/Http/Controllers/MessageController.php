<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Notifications\GeneralNotification;

class MessageController extends Controller
{
    public function index($id = null)
    {
        $user = auth()->user();

        Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(5));

        $contacts = collect();
        $activeContact = null;
        $messages = collect();

        // 1. Logic filter kontak tetap sama
        if ($user->role === 'buyer') {
            $contacts = User::where('role', 'seller')->get();
        } elseif ($user->role === 'seller') {
            $contacts = User::whereIn('role', ['admin', 'seller', 'buyer'])->where('id', '!=', $user->id)->get();
        } elseif ($user->role === 'admin') {
            $contacts = User::where('role', 'seller')->get();
        }

        if ($id) {
            $activeContact = User::findOrFail($id);

            // 2. Ambil riwayat percakapan
            $messages = Message::where(function ($q) use ($user, $id) {
                $q->where('sender_id', $user->id)->where('receiver_id', $id);
            })->orWhere(function ($q) use ($user, $id) {
                $q->where('sender_id', $id)->where('receiver_id', $user->id);
            })->orderBy('created_at', 'asc')->get();

            // 3. LOGIKA PEMBERSIH: Tandai pesan dari lawan bicara ke SAYA sebagai dibaca
            Message::where('sender_id', $id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // 4. LOGIKA PEMBERSIH LONCENG: Tandai notifikasi terkait chat ini sebagai dibaca
            $user->unreadNotifications()
                ->where('data->url', route('chat.index', $id))
                ->get()
                ->each->markAsRead();
        }

        return view('chat.index', compact('contacts', 'activeContact', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:1000',
        ]);

        $sender   = auth()->user();
        $receiver = User::findOrFail($request->receiver_id);

        // --- 1. FILTER ROLE ---
        $rules = [
            'buyer'  => ['seller'],
            'seller' => ['buyer', 'admin', 'seller'],
            'admin'  => ['seller'],
        ];

        if (! in_array($receiver->role, $rules[$sender->role] ?? [])) {
            return back()->with('error', 'Anda tidak diizinkan mengirim pesan ke user ini.');
        }

        // --- 2. SIMPAN PESAN (SATU KALI) ---
        $message = Message::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'message'     => $request->message,
        ]);

        // --- 3. CEK APAKAH RECEIVER SEDANG AKTIF DI CHAT INI ---
        $activeChat = cache('chat_active_' . $receiver->id);

        if ($activeChat != $sender->id) {
            $receiver->notify(new GeneralNotification([
                'title'   => 'Pesan Baru 📩',
                'message' => $sender->name . ': ' . Str::limit($request->message, 45),
                'icon'    => '<i class="bi bi-chat-right-text-fill"></i>', // Hilangkan tag <i>, kirim class saja
                'color'   => 'bg-blue-100 text-blue-600',
                'url'     => route('chat.index', $sender->id),
            ]));
        }

        // --- 4. BROADCAST REALTIME ---
        broadcast(new MessageSent($message))->toOthers();

        return back()->with('success', 'Pesan terkirim!');
    }

    public function activity(Request $request)
    {
        cache()->put(
            'chat_active_' . auth()->id(),
            $request->receiver_id,
            now()->addMinutes(2)
        );

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Events\MessageRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function index($id = null)
    {
        $user = auth()->user();

        // 1. Update status online
        Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(5));

        // 2. Eager Loading untuk menghindari N+1 Query di Sidebar
        // Kita ambil kontak beserta pesan terakhir mereka
        $contacts = User::where( 'id', '!=', $user->id)
            ->withCount(['receivedMessages as unread_count' => function ($q) use ($user) {
                $q->where('receiver_id', $user->id)->where('is_read', false);
            }])
            ->get();

        $activeContact = null;
        $messages = collect();

        if ($id) {
            $activeContact = User::findOrFail($id);

            // 3. Grouped Query untuk pesan agar lebih aman
            $messages = Message::where(function ($query) use ($user, $id) {
                $query->where(function ($q) use ($user, $id) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $id);
                })->orWhere(function ($q) use ($user, $id) {
                    $q->where('sender_id', $id)
                        ->where('receiver_id', $user->id);
                });
            })->orderBy('created_at', 'asc')->get();

            // 4. Update is_read hanya jika ada pesan yang belum dibaca (Efisiensi Database)
            $unreadMessages = Message::where('sender_id', $id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false);

            if ($unreadMessages->exists()) {
                $unreadMessages->update(['is_read' => true]);

                // // Pastikan Event MessageRead sudah menerima data yang benar
                // broadcast(new MessageRead($id, $user->id))->toOthers();
            }
        }

        return view('chat.index', compact('contacts', 'activeContact', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        // 1. Validasi input agar tidak error database
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        try {
            $message = Message::create([
                'sender_id'   => Auth::id(),
                'receiver_id' => $validated['receiver_id'],
                'message'     => $validated['message'],
                'is_read'     => false,
            ]);

            // 2. Load relasi agar data di Event lengkap (sesuai broadcastWith)
            $message->load(['sender', 'receiver']);

            // 3. Broadcast
            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'status' => 'success',
                'message' => $message->message, // Sesuai dengan kebutuhan JS insertAdjacentHTML
                'data' => $message
            ]);
        } catch (\Exception $e) {
            // Jangan return seluruh object $e di produksi karena berbahaya
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

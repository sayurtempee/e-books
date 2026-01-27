<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'last_message_at',
    ];
    // Relasi ke semua pesan di dalam percakapan ini
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Untuk mendapatkan lawan bicara (interlocutor)
    public function getInterlocutorAttribute()
    {
        // Mengambil objek User lengkap dari relasi yang sudah di-load
        return $this->sender_id === Auth::id() ? $this->receiver : $this->sender;
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}

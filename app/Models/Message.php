<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Message extends Model
{
    use Notifiable;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read'
    ];
}

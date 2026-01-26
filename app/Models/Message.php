<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
protected $fillable = [
    'sender_id',
    'receiver_id',
    'message',
    'is_read',
];

protected $casts = [
    'is_read' => 'boolean',
];

/**
 * Relasi ke User sebagai Pengirim
 */
public function sender(): BelongsTo
{
    return $this->belongsTo(User::class, 'sender_id');
}

/**
 * Relasi ke User sebagai Penerima
 */
public function receiver(): BelongsTo
{
    return $this->belongsTo(User::class, 'receiver_id');
}
}

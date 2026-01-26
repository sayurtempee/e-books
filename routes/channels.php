<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (string) $user->id === (string) $id;
});

Broadcast::channel('chat.{userId}', function (User $user, $userId) {
    // Memastikan perbandingan tipe data konsisten (integer)
    return (int) $user->id !== (int) $userId;
});

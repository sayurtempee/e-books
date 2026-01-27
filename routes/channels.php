<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (string) $user->id === (string) $id;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);
    return $conversation && ($user->id === $conversation->sender_id || $user->id === $conversation->receiver_id);
});

<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // We changed $user->id to $user->user_id to match your database schema
    return (int) $user->user_id === (int) $id;
});
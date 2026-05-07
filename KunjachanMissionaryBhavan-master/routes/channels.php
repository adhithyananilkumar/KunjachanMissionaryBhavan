<?php

use Illuminate\Support\Facades\Broadcast;

// Default private notifications channel used by Laravel Notifications broadcasting
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('institution.{institutionId}', function ($user, $institutionId) {
    return (int) ($user->institution_id ?? 0) === (int) $institutionId;
});

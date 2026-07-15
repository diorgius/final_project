<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('offers.admin', function ($user) {
    return $user->role === 'admin';
});

Broadcast::channel('offers.advertiser', function ($user) {
    return $user->role === 'advertiser';
});

Broadcast::channel('offers.webmaster', function ($user) {
    return $user->role === 'webmaster';
});

Broadcast::channel('offers.admin.{id}', function ($user, $id) {
    return $user->role === 'admin'
        && $user->id == $id;
});

Broadcast::channel('offers.advertiser.{id}', function ($user, $id) {
    return $user->role === 'advertiser'
        && $user->id == $id;
});

Broadcast::channel('offers.webmaster.{id}', function ($user, $id) {
    return $user->role === 'webmaster'
        && $user->id == $id;
});
<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class UpdateLastLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $user->timestamps = false;
        $user->forceFill(['last_login' => now()])->saveQuietly();
    }
}

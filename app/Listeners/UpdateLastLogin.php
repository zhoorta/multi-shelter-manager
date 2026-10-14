<?php

namespace App\Listeners;

use App\Http\Middleware\ReadOnlyWhileImpersonating;
use Illuminate\Auth\Events\Login;

class UpdateLastLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (session()->has(ReadOnlyWhileImpersonating::SESSION_KEY)) {
            return;
        }

        $user = $event->user;
        $user->timestamps = false;
        $user->forceFill(['last_login' => now()])->saveQuietly();
    }
}

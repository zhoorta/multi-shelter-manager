<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePublicPortalEnabled
{
    /**
     * Redirect to the login page when the public portal is disabled for this installation,
     * leaving the application as a backoffice only.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.public_portal_enabled')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}

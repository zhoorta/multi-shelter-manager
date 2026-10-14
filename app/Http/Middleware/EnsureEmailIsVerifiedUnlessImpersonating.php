<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedUnlessImpersonating extends EnsureEmailIsVerified
{
    /**
     * An admin viewing the app as an invited user who has not set a password
     * yet still gets in, so a new shelter can be checked before its manager
     * arrives. The view is read-only, so nothing is verified by doing so.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if ($request->hasSession() && $request->session()->has(ReadOnlyWhileImpersonating::SESSION_KEY)) {
            return $next($request);
        }

        return parent::handle($request, $next, $redirectToRoute);
    }
}

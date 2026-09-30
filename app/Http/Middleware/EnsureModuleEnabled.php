<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    /**
     * Answer 404 when the shelter the user is acting within has switched the
     * module off, so a saved link or typed URL behaves as if the page does not exist.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        abort_unless($request->user()?->currentShelterHasModule($module), 404);

        return $next($request);
    }
}

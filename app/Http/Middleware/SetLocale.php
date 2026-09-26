<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the visitor's language: the logged-in user's saved choice, then the
     * one picked in this session, falling back to the application default.
     *
     * The browser's Accept-Language is deliberately ignored, so the login page
     * and the backoffice always open in the same (installation) language until
     * someone picks another one.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $available = array_keys(config('app.available_locales'));

        $locale = collect([$request->user()?->locale, $request->session()->get('locale')])
            ->first(fn (?string $candidate): bool => in_array($candidate, $available, true));

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}

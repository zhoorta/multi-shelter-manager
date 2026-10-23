<?php

namespace App\Http\Middleware;

use App\Actions\Demo\DemoSandbox;
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
     * someone picks another one. The public demo is the exception: with no
     * choice made yet it follows the browser's language.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $available = array_keys(config('app.available_locales'));

        $locale = collect([$request->user()?->locale, $request->session()->get('locale')])
            ->first(fn (?string $candidate): bool => in_array($candidate, $available, true));

        if ($locale === null && DemoSandbox::enabled()) {
            $locale = $this->browserLocale($request, $available);
        }

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /**
     * @param  array<int, string>  $available
     */
    private function browserLocale(Request $request, array $available): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $code = strtolower(substr($language, 0, 2));

            if (in_array($code, $available, true)) {
                return $code;
            }
        }

        return null;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyWhileImpersonating
{
    public const string SESSION_KEY = 'impersonator_id';

    /**
     * Tables written by the framework on every request, which stay writable.
     */
    private const string ALLOWED_WRITES = '/^\s*(?:insert\s+(?:or\s+\w+\s+)?into|update|delete\s+from|replace\s+into)\s+[`"\[]?(?:sessions|cache|cache_locks)[`"\]]?[\s(]/i';

    private static bool $writesAllowed = false;

    /**
     * While an admin is viewing the app as another user, nothing may change:
     * database writes fail, form posts are refused and the pages that expose
     * the user's credentials stay closed.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasSession() || ! $request->session()->has(self::SESSION_KEY)) {
            return $next($request);
        }

        abort_if($request->routeIs('profile.edit', 'security.edit'), 403, __('Not available while viewing as another user.'));

        abort_unless(
            $request->isMethodSafe() || $request->hasHeader('X-Livewire') || $request->routeIs('impersonation.stop', 'logout'),
            403,
            __('Read-only while viewing as another user.'),
        );

        if (! $request->routeIs('logout')) {
            $this->guardDatabaseWrites();
        }

        return $next($request);
    }

    /**
     * Run a harmless preference change (such as picking the shelter to view)
     * even while impersonating.
     *
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public static function allowWrites(Closure $callback): mixed
    {
        self::$writesAllowed = true;

        try {
            return $callback();
        } finally {
            self::$writesAllowed = false;
        }
    }

    private function guardDatabaseWrites(): void
    {
        if (app()->bound('impersonation.guard')) {
            return;
        }

        app()->instance('impersonation.guard', true);

        DB::beforeExecuting(function (string $query): void {
            $isWrite = preg_match('/^\s*(?:insert|update|delete|replace|truncate|alter|drop|create)\b/i', $query) === 1;

            if ($isWrite && ! self::$writesAllowed && preg_match(self::ALLOWED_WRITES, $query) !== 1) {
                abort(403, __('Read-only while viewing as another user.'));
            }
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ReadOnlyWhileImpersonating;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationController extends Controller
{
    /**
     * Let an admin view the app, read-only, as a manager or staff user.
     */
    public function start(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        abort_unless($admin->is_admin && ! $request->session()->has(ReadOnlyWhileImpersonating::SESSION_KEY), 403);
        abort_if($user->is_admin || $user->is($admin), 403);

        $request->session()->put(ReadOnlyWhileImpersonating::SESSION_KEY, $admin->id);
        Auth::login($user);
        $request->session()->regenerate();

        Log::info('Impersonation started', ['admin_id' => $admin->id, 'user_id' => $user->id]);

        return redirect()->route('dashboard');
    }

    public function stop(Request $request): RedirectResponse
    {
        $adminId = $request->session()->pull(ReadOnlyWhileImpersonating::SESSION_KEY);

        abort_if($adminId === null, 403);

        Log::info('Impersonation stopped', ['admin_id' => $adminId, 'user_id' => $request->user()->id]);

        $admin = User::query()->where('is_admin', true)->findOrFail($adminId);
        ReadOnlyWhileImpersonating::allowWrites(fn () => Auth::login($admin));
        $request->session()->regenerate();

        return redirect()->route('admin.users.index');
    }
}

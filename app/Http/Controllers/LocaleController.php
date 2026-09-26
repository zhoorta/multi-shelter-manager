<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    /**
     * Switch the interface language from the public/auth language selector.
     * It is kept in the session and, for a logged-in user, saved to their
     * account so it follows them to other devices and their notifications.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(array_keys(config('app.available_locales')))],
        ]);

        $request->session()->put('locale', $validated['locale']);
        $request->user()?->update(['locale' => $validated['locale']]);

        return redirect()->back();
    }
}

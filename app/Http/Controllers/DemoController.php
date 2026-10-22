<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Demo\DemoSandbox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    /**
     * Give the visitor a throwaway shelter and log them in as its manager.
     */
    public function __invoke(Request $request, DemoSandbox $sandbox): RedirectResponse
    {
        abort_unless(DemoSandbox::enabled(), 404);

        if ($sandbox->isFull()) {
            return redirect()->route('login')->withErrors(['demo' => __('The demo is busy right now. Please try again in a few minutes.')]);
        }

        Auth::login($sandbox->create());
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}

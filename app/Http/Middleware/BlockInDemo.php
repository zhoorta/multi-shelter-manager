<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\Demo\DemoSandbox;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps demo visitors out of the pages that change the account itself or send e-mail
 * (profile, security, data export, user invitations).
 */
class BlockInDemo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (DemoSandbox::isDemoUser($request->user())) {
            return redirect()->route('dashboard')->with('status', __('Not available in the demo.'));
        }

        return $next($request);
    }
}

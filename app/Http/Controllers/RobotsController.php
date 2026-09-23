<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Backoffice paths that search engines must never crawl.
     *
     * @var list<string>
     */
    private const array PRIVATE_PATHS = [
        '/dashboard',
        '/documentation',
        '/pets',
        '/volunteers',
        '/facilities',
        '/admin',
        '/settings',
        '/login',
        '/setup',
    ];

    /**
     * Dynamic robots.txt: when the public portal is disabled the installation
     * is backoffice only, so the whole site is kept out of search engines;
     * otherwise only the backoffice is blocked and the sitemap is announced.
     */
    public function __invoke(): Response
    {
        $lines = ['User-agent: *'];

        if (! config('app.public_portal_enabled')) {
            $lines[] = 'Disallow: /';
        } else {
            foreach (self::PRIVATE_PATHS as $path) {
                $lines[] = 'Disallow: '.$path;
            }

            $lines[] = '';
            $lines[] = 'Sitemap: '.route('sitemap');
        }

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}

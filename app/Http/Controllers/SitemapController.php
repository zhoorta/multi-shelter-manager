<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Shelter;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * XML sitemap of the public portal pages, so search engines can discover
     * every partner shelter page.
     */
    public function __invoke(): Response
    {
        $shelters = Shelter::query()->orderBy('id')->get(['id', 'updated_at']);

        return response()
            ->view('sitemap', ['shelters' => $shelters])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}

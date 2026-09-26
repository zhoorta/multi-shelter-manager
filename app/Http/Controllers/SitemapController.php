<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Shelter;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * XML sitemap of the public portal pages, so search engines can discover
     * every partner shelter page and every published pet page.
     */
    public function __invoke(): Response
    {
        $shelters = Shelter::query()->orderBy('id')->get(['id', 'updated_at']);

        $pets = Pet::query()
            ->withoutGlobalScope('shelter')
            ->publishedToPortal()
            ->with(['species', 'shelter.region'])
            ->orderBy('id')
            ->get();

        return response()
            ->view('sitemap', ['shelters' => $shelters, 'pets' => $pets])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}

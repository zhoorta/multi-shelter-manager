<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Shelter;
use Illuminate\Http\Response;

class ShelterFeedController extends Controller
{
    /**
     * RSS feed of the pets a shelter has published for adoption, newest
     * first, so shelters can auto-post them to social media through tools
     * like Buffer or Zapier.
     */
    public function __invoke(Shelter $shelter): Response
    {
        $pets = $shelter->publishedPets()
            ->with(['species', 'breed', 'size', 'shelter.region', 'images'])
            ->latest('checkin_date')
            ->latest('id')
            ->limit(30)
            ->get();

        return response()
            ->view('feed', ['shelter' => $shelter, 'pets' => $pets])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}

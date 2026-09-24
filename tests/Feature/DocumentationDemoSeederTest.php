<?php

use App\Models\User;
use Database\Seeders\DocumentationDemoSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('creates a demo viewer account on the main shelter', function () {
    Http::fake();
    Storage::fake('public');

    $this->seed(DocumentationDemoSeeder::class);

    $viewer = User::query()->where('email', 'viewer@example.com')->firstOrFail();

    expect($viewer->currentShelter->name)->toBe('Happy Paws Animal Shelter')
        ->and($viewer->isViewerOfCurrentShelter())->toBeTrue();
});

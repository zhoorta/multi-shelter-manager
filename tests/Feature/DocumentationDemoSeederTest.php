<?php

use App\Models\AdoptionApplication;
use App\Models\Member;
use App\Models\Pet;
use App\Models\Shelter;
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

test('creates demo members of the main shelter with up-to-date and overdue fees', function () {
    Http::fake();
    Storage::fake('public');

    $this->seed(DocumentationDemoSeeder::class);

    $shelter = Shelter::query()->where('name', 'Happy Paws Animal Shelter')->firstOrFail();

    expect($shelter->membership_fee)->toBe('24.00')
        ->and($shelter->members()->count())->toBe(11)
        ->and(Member::query()->inArrears()->pluck('name')->sort()->values()->all())
        ->toBe(['Daniel Brooks', 'Laura Pinto', 'Thomas Green'])
        ->and(Member::query()->where('name', 'Emma Carter')->first()->volunteer?->name)->toBe('Emma Carter');
});

test('creates a foster families wing and adoption applications for the documentation', function () {
    Http::fake();
    Storage::fake('public');

    $this->seed(DocumentationDemoSeeder::class);

    $coco = Pet::query()->withoutGlobalScopes()->where('name', 'Coco')->firstOrFail();

    expect($coco->isInFosterFamily())->toBeTrue()
        ->and($coco->cage->code)->toBe('Carter family')
        ->and($coco->cage->volunteer->name)->toBe('Emma Carter')
        ->and(AdoptionApplication::query()->where('status', 'pending')->count())->toBe(4)
        ->and(AdoptionApplication::query()->where('status', 'approved')->value('adoption_id'))->not->toBeNull();
});

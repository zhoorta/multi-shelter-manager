<?php

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Http::fake();
    config(['demo.enabled' => true]);
});

test('preparing the demo seeds the template once and locks the example accounts', function () {
    $this->artisan('demo:prepare')->assertSuccessful();

    $manager = User::query()->where('email', 'manager@example.com')->sole();
    expect(Shelter::query()->count())->toBeGreaterThan(0)
        ->and(Hash::check('password', $manager->password))->toBeFalse();

    $shelters = Shelter::query()->count();
    $this->artisan('demo:prepare')->assertSuccessful();
    expect(Shelter::query()->count())->toBe($shelters);
});

test('preparing the demo needs demo mode', function () {
    config(['demo.enabled' => false]);

    $this->artisan('demo:prepare')->assertFailed();
});

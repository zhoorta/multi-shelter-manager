<?php

use App\Actions\Demo\DemoSandbox;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Member;
use App\Models\Pet;
use App\Models\PetClinicalPhoto;
use App\Models\PetImage;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
    config(['demo.enabled' => true, 'demo.ttl_minutes' => 60]);

    $this->template = Shelter::factory()->create(['name' => 'Template Shelter']);
    config(['demo.template_shelter_id' => $this->template->id]);

    $wing = Wing::factory()->for(Facility::factory()->for($this->template))->create();
    $cage = Cage::factory()->for($wing)->create();
    $this->pet = Pet::factory()->for($this->template)->create(['cage_id' => $cage->id, 'name' => 'Rex']);

    Storage::disk('public')->put('pets/rex.jpg', 'photo');
    PetImage::factory()->for($this->pet)->create(['image_path' => 'pets/rex.jpg', 'is_main' => true]);
    Storage::disk('local')->put('clinical-photos/1/rex.jpg', 'clinical');
    PetClinicalPhoto::factory()->for($this->pet)->create(['image_path' => 'clinical-photos/1/rex.jpg', 'thumb_path' => null]);
    Member::factory()->for($this->template)->create();
});

test('a sandbox is a copy of the template shelter with its own manager', function () {
    $user = app(DemoSandbox::class)->create();

    $sandbox = $user->currentShelter;
    expect(DemoSandbox::isDemoUser($user))->toBeTrue()
        ->and($sandbox->id)->not->toBe($this->template->id)
        ->and($sandbox->name)->toBe('Template Shelter')
        ->and($user->shelters()->pluck('shelters.id')->all())->toBe([$sandbox->id]);

    $copy = Pet::query()->where('shelter_id', $sandbox->id)->sole();
    expect($copy->name)->toBe('Rex')
        ->and($copy->cage_id)->not->toBe($this->pet->cage_id)
        ->and($copy->cage->wing->facility->shelter_id)->toBe($sandbox->id)
        ->and($copy->images)->toHaveCount(1)
        ->and($copy->images->first()->image_path)->not->toBe('pets/rex.jpg')
        ->and($copy->clinicalPhotos)->toHaveCount(1)
        ->and($sandbox->members()->count())->toBe(1)
        ->and(Pet::query()->where('shelter_id', $this->template->id)->count())->toBe(1);

    Storage::disk('public')->assertExists($copy->images->first()->image_path);
    Storage::disk('local')->assertExists($copy->clinicalPhotos->first()->image_path);
});

test('pruning deletes expired sandboxes with their files and leaves the template and fresh sandboxes alone', function () {
    $old = app(DemoSandbox::class)->create();
    $fresh = app(DemoSandbox::class)->create();
    $old->forceFill(['created_at' => now()->subMinutes(61)])->save();
    $oldPhoto = Pet::query()->where('shelter_id', $old->current_shelter_id)->sole()->images->first()->image_path;

    expect(app(DemoSandbox::class)->prune())->toBe(1);

    expect(User::query()->find($old->id))->toBeNull()
        ->and(Shelter::query()->withTrashed()->find($old->current_shelter_id))->toBeNull()
        ->and(Pet::query()->where('shelter_id', $old->current_shelter_id)->count())->toBe(0)
        ->and(User::query()->find($fresh->id))->not->toBeNull()
        ->and(Pet::query()->where('shelter_id', $fresh->current_shelter_id)->count())->toBe(1)
        ->and(Pet::query()->where('shelter_id', $this->template->id)->count())->toBe(1);

    Storage::disk('public')->assertMissing($oldPhoto);
    Storage::disk('public')->assertExists('pets/rex.jpg');
    Storage::disk('local')->assertExists('clinical-photos/1/rex.jpg');
});

test('the login page offers the demo only when demo mode is on', function () {
    User::factory()->create();

    $this->get(route('login'))->assertOk()->assertSee('demo-button', false);

    config(['demo.enabled' => false]);
    $this->get(route('login'))->assertOk()->assertDontSee('demo-button', false);
});

test('starting the demo logs the visitor in as the manager of a new shelter', function () {
    $this->post(route('demo.start'))->assertRedirect(route('dashboard'));

    $user = auth()->user();
    expect(DemoSandbox::isDemoUser($user))->toBeTrue()
        ->and($user->shelters()->first()->pivot->role)->toBe('manager');

    $this->get(route('dashboard'))->assertOk()->assertSee('demo-banner', false);
});

test('the demo is not available when demo mode is off', function () {
    config(['demo.enabled' => false]);

    $this->post(route('demo.start'))->assertNotFound();
});

test('the demo refuses new visitors when it is full', function () {
    config(['demo.max_sandboxes' => 1]);
    app(DemoSandbox::class)->create();

    $this->post(route('demo.start'))->assertRedirect(route('login'))->assertSessionHasErrors('demo');
    expect(User::query()->where('email', 'like', '%@'.DemoSandbox::DOMAIN)->count())->toBe(1);
});

test('demo visitors cannot open the pages that change the account or invite users', function () {
    $this->actingAs(app(DemoSandbox::class)->create());

    foreach (['profile.edit', 'data-export.edit', 'security.edit', 'admin.users.create'] as $route) {
        $this->get(route($route))->assertRedirect(route('dashboard'));
    }

    $this->get(route('appearance.edit'))->assertOk();
});

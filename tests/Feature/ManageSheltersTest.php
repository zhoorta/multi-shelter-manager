<?php

use App\Livewire\Admin\ManageShelters;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.shelters.index'));

    $response->assertRedirect(route('login'));
});

test('staff and managers are forbidden from viewing the page', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    $this->actingAs($staff);
    $this->get(route('admin.shelters.index'))->assertForbidden();

    $manager = User::factory()->create(['role' => 'manager']);
    $this->actingAs($manager);
    $this->get(route('admin.shelters.index'))->assertForbidden();
});

test('admins can view the page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.shelters.index'))->assertOk();
});

test('shows a placeholder message when there are no shelters', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->get(route('admin.shelters.index'))->assertSee(__('No shelters registered'));
});

test('lists shelters with their user and pet counts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'city' => 'Lisbon']);
    User::factory()->create(['role' => 'staff', 'shelter_id' => $shelter->id]);
    Pet::factory()->create(['shelter_id' => $shelter->id]);

    Livewire::test(ManageShelters::class)
        ->assertSee('Happy Paws')
        ->assertSee('Lisbon');
});

test('creates a new shelter and closes the modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageShelters::class)
        ->set('shelterName', 'Happy Paws')
        ->set('shelterCity', 'Lisbon')
        ->call('saveShelter')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'shelter-form');

    expect(Shelter::query()->where('name', 'Happy Paws')->where('city', 'Lisbon')->exists())->toBeTrue();
});

test('opening the create modal resets a stale edit state', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'city' => 'Lisbon']);

    Livewire::test(ManageShelters::class)
        ->call('editShelter', $shelter->id)
        ->assertSet('shelterName', 'Happy Paws')
        ->assertSet('shelterCity', 'Lisbon')
        ->call('createShelter')
        ->assertSet('editingShelterId', null)
        ->assertSet('shelterName', '')
        ->assertSet('shelterCity', '');
});

test('requires a name and city to create a shelter', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageShelters::class)
        ->set('shelterName', '')
        ->set('shelterCity', '')
        ->call('saveShelter')
        ->assertHasErrors(['shelterName' => 'required', 'shelterCity' => 'required']);
});

test('validates email and website format', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageShelters::class)
        ->set('shelterName', 'Happy Paws')
        ->set('shelterCity', 'Lisbon')
        ->set('shelterEmail', 'not-an-email')
        ->set('shelterWebsite', 'not-a-url')
        ->call('saveShelter')
        ->assertHasErrors(['shelterEmail' => 'email', 'shelterWebsite' => 'url']);
});

test('updates an existing shelter', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'city' => 'Lisbon']);

    Livewire::test(ManageShelters::class)
        ->call('editShelter', $shelter->id)
        ->set('shelterName', 'Happier Paws')
        ->set('shelterCity', 'Porto')
        ->call('saveShelter')
        ->assertHasNoErrors();

    expect($shelter->fresh()->name)->toBe('Happier Paws');
    expect($shelter->fresh()->city)->toBe('Porto');
});

test('uploads and replaces a shelter logo', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['logo_path' => null]);

    Livewire::test(ManageShelters::class)
        ->call('editShelter', $shelter->id)
        ->set('shelterLogo', UploadedFile::fake()->image('logo.png'))
        ->call('saveShelter')
        ->assertHasNoErrors();

    $firstLogoPath = $shelter->fresh()->logo_path;
    expect($firstLogoPath)->not->toBeNull();
    Storage::disk('public')->assertExists($firstLogoPath);

    Livewire::test(ManageShelters::class)
        ->call('editShelter', $shelter->id)
        ->set('shelterLogo', UploadedFile::fake()->image('logo-2.png'))
        ->call('saveShelter')
        ->assertHasNoErrors();

    $secondLogoPath = $shelter->fresh()->logo_path;
    expect($secondLogoPath)->not->toBe($firstLogoPath);
    Storage::disk('public')->assertExists($secondLogoPath);
    Storage::disk('public')->assertMissing($firstLogoPath);
});

test('soft-deletes a shelter instead of removing it permanently', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $shelter = Shelter::factory()->create(['name' => 'Happy Paws']);

    Livewire::test(ManageShelters::class)
        ->call('deleteShelter', $shelter->id)
        ->assertDontSee('Happy Paws');

    expect($shelter->fresh()->trashed())->toBeTrue();
    expect(Shelter::query()->find($shelter->id))->toBeNull();
});

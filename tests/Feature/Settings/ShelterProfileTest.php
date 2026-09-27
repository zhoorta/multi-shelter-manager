<?php

use App\Livewire\Settings\ShelterProfile;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->shelter = Shelter::factory()->create(['name' => 'Happy Paws', 'email' => 'old@happypaws.pt']);
    $this->manager = User::factory()->forShelter($this->shelter, 'manager')->create();
});

test('managers can open the shelter settings page and see it in the settings menu', function () {
    $this->actingAs($this->manager)
        ->get(route('shelter-profile.edit'))
        ->assertOk()
        ->assertSee('Happy Paws');

    $this->actingAs($this->manager)
        ->get(route('profile.edit'))
        ->assertSee(route('shelter-profile.edit'));
});

test('managers see the public page address read-only when the public portal is enabled', function (bool $portalEnabled) {
    config(['app.public_portal_enabled' => $portalEnabled]);

    $response = $this->actingAs($this->manager)->get(route('shelter-profile.edit'))->assertOk();

    $portalEnabled
        ? $response->assertSee(route('shelters.show', $this->shelter))
        : $response->assertDontSee(route('shelters.show', $this->shelter));
})->with([
    'portal enabled' => [true],
    'portal disabled' => [false],
]);

test('staff, viewers and admins cannot open the shelter settings page', function (string $role) {
    $user = $role === 'admin'
        ? User::factory()->admin()->create()
        : User::factory()->forShelter($this->shelter, $role)->create();

    $this->actingAs($user)->get(route('shelter-profile.edit'))->assertForbidden();
    $this->actingAs($user)->get(route('profile.edit'))->assertDontSee(route('shelter-profile.edit'));
})->with(['staff', 'viewer', 'admin']);

test('managers update their shelter profile but not its name', function () {
    Storage::fake('public');

    $region = Region::factory()->create();

    Livewire::actingAs($this->manager)
        ->test(ShelterProfile::class)
        ->assertSet('shelterEmail', 'old@happypaws.pt')
        ->set('shelterName', 'Renamed')
        ->set('shelterShortName', 'HP')
        ->set('shelterEmail', 'new@happypaws.pt')
        ->set('shelterPhone', '292000000')
        ->set('shelterCity', 'Horta')
        ->set('shelterRegionId', $region->id)
        ->set('shelterLogo', UploadedFile::fake()->image('logo.png'))
        ->call('saveShelter')
        ->assertHasNoErrors();

    $shelter = $this->shelter->fresh();

    expect($shelter->name)->toBe('Happy Paws')
        ->and($shelter->short_name)->toBe('HP')
        ->and($shelter->email)->toBe('new@happypaws.pt')
        ->and($shelter->phone)->toBe('292000000')
        ->and($shelter->city)->toBe('Horta')
        ->and($shelter->region_id)->toBe($region->id);

    Storage::disk('public')->assertExists($shelter->logo_path);
});

test('the shelter email is required and must be valid', function (string $email, string $rule) {
    Livewire::actingAs($this->manager)
        ->test(ShelterProfile::class)
        ->set('shelterEmail', $email)
        ->call('saveShelter')
        ->assertHasErrors(['shelterEmail' => $rule]);

    expect($this->shelter->fresh()->email)->toBe('old@happypaws.pt');
})->with([
    'empty' => ['', 'required'],
    'invalid' => ['not-an-email', 'email'],
]);

test('only the current shelter is updated', function () {
    $otherShelter = Shelter::factory()->create(['email' => 'other@shelter.pt']);

    Livewire::actingAs($this->manager)
        ->test(ShelterProfile::class)
        ->set('shelterEmail', 'new@happypaws.pt')
        ->call('saveShelter')
        ->assertHasNoErrors();

    expect($otherShelter->fresh()->email)->toBe('other@shelter.pt');
});

test('a user demoted from manager can no longer save', function () {
    $component = Livewire::actingAs($this->manager)->test(ShelterProfile::class);

    $this->manager->shelters()->updateExistingPivot($this->shelter->id, ['role' => 'staff']);

    $component->set('shelterEmail', 'new@happypaws.pt')
        ->call('saveShelter')
        ->assertForbidden();

    expect($this->shelter->fresh()->email)->toBe('old@happypaws.pt');
});

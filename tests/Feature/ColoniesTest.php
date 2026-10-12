<?php

use App\Livewire\Colonies\ColonyShow;
use App\Livewire\Colonies\ManageColonies;
use App\Livewire\Pets\ManagePets;
use App\Livewire\Pets\PetForm;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Colony;
use App\Models\Facility;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Species;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('colonies.index'))->assertRedirect(route('login'));
});

test('admins are forbidden from the colonies pages', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('colonies.index'))->assertForbidden();
});

test('the page answers 404 when the shelter turned the colonies module off', function () {
    $shelter = Shelter::factory()->create(['modules' => ['colonies' => false]]);
    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    $this->get(route('colonies.index'))->assertNotFound();
});

test('lists only the colonies of the acting user\'s shelter with their counts', function () {
    $shelter = Shelter::factory()->create();
    $colony = Colony::factory()->for($shelter)->create(['name' => 'Cova da Piedade']);
    Colony::factory()->create(['name' => 'Other Colony']);
    Pet::factory()->for($shelter)->create(['colony_id' => $colony->id, 'is_neutered' => true]);
    Pet::factory()->for($shelter)->create(['colony_id' => $colony->id, 'is_neutered' => false]);
    Pet::factory()->for($shelter)->create(['colony_id' => $colony->id, 'date_of_death' => now()->subDay()]);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageColonies::class)
        ->assertSee('Cova da Piedade')
        ->assertDontSee('Other Colony')
        ->assertSee('1 / 2');
});

test('staff create, edit and delete colonies in their own shelter', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $species = Species::factory()->create(['has_colonies' => true]);

    Livewire::test(ManageColonies::class)
        ->set('colonySpeciesId', $species->id)
        ->set('colonyName', 'Feitoria')
        ->set('colonyCity', 'Almada')
        ->call('saveColony')
        ->assertHasNoErrors();

    $colony = Colony::query()->where('name', 'Feitoria')->firstOrFail();
    expect($colony->shelter_id)->toBe($shelter->id)->and($colony->species_id)->toBe($species->id)->and($colony->is_active)->toBeTrue();

    Livewire::test(ManageColonies::class)
        ->call('editColony', $colony->id)
        ->set('colonyName', 'Feitoria Nova')
        ->call('saveColony');

    expect($colony->fresh()->name)->toBe('Feitoria Nova');

    Livewire::test(ManageColonies::class)->call('deleteColony', $colony->id);

    expect(Colony::query()->find($colony->id))->toBeNull();
});

test('the name is required', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'staff')->create());

    Livewire::test(ManageColonies::class)
        ->call('saveColony')
        ->assertHasErrors(['colonyName' => 'required']);
});

test('viewers and staff without the pets area cannot change colonies', function () {
    $shelter = Shelter::factory()->create();
    $colony = Colony::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());
    Livewire::test(ManageColonies::class)->call('deleteColony', $colony->id)->assertForbidden();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff', editAreas: ['health'])->create());
    Livewire::test(ManageColonies::class)->call('deleteColony', $colony->id)->assertForbidden();
});

test('cannot edit or delete a colony of another shelter', function () {
    $other = Colony::factory()->create();
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'manager')->create());

    expect(fn () => Livewire::test(ManageColonies::class)->call('editColony', $other->id))
        ->toThrow(ModelNotFoundException::class);
    expect(fn () => Livewire::test(ManageColonies::class)->call('deleteColony', $other->id))
        ->toThrow(ModelNotFoundException::class);
});

test('the colony page shows the totals and exports the living cats as CSV', function () {
    $shelter = Shelter::factory()->create();
    $colony = Colony::factory()->for($shelter)->create(['name' => 'Cacilhas']);
    Pet::factory()->for($shelter)->create(['name' => 'Tigre', 'colony_id' => $colony->id, 'is_neutered' => true, 'is_ear_tipped' => true, 'captured_at' => '2026-03-01']);
    Pet::factory()->for($shelter)->create(['name' => 'Mimi', 'colony_id' => $colony->id, 'is_neutered' => false, 'neutering_status' => 'pending']);
    Pet::factory()->for($shelter)->create(['name' => 'Gone', 'colony_id' => $colony->id, 'date_of_death' => now()->subDay()]);

    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ColonyShow::class, ['colony' => $colony])
        ->assertSet('totals', ['total' => 2, 'neutered' => 1, 'pending' => 1, 'unknown' => 0, 'earTipped' => 1])
        ->assertSee('Tigre')
        ->assertDontSee('Gone')
        ->call('exportCsv')
        ->assertFileDownloaded();
});

test('a colony of another shelter answers 404', function () {
    $other = Colony::factory()->create();
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'manager')->create());

    $this->get(route('colonies.show', $other))->assertNotFound();
});

function colonyPetForm(Shelter $shelter): array
{
    $species = Species::factory()->create(['has_colonies' => true]);

    return [$species, Breed::factory()->for($species)->create()];
}

test('a cat is registered in a colony with its capture details and no cage', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    [$species, $breed] = colonyPetForm($shelter);
    $colony = Colony::factory()->for($shelter)->create(['species_id' => $species->id]);
    $cage = Cage::factory()->create();

    Livewire::withQueryParams(['colony' => $colony->id])
        ->test(PetForm::class)
        ->assertSet('petColonyId', $colony->id)
        ->assertSet('petSpeciesId', $species->id)
        ->set('petName', 'Tigre')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petCageId', $cage->id)
        ->set('petCapturedAt', '2026-03-01')
        ->set('petReturnedAt', '2026-03-05')
        ->set('petIsEarTipped', true)
        ->call('savePet')
        ->assertHasNoErrors();

    $pet = Pet::query()->where('name', 'Tigre')->firstOrFail();
    expect($pet->colony_id)->toBe($colony->id)
        ->and($pet->cage_id)->toBeNull()
        ->and($pet->captured_at->toDateString())->toBe('2026-03-01')
        ->and($pet->returned_at->toDateString())->toBe('2026-03-05')
        ->and($pet->is_ear_tipped)->toBeTrue();
});

test('the return date cannot be before the capture date', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->create();

    Livewire::test(PetForm::class)
        ->set('petColonyId', $colony->id)
        ->set('petCapturedAt', '2026-03-05')
        ->set('petReturnedAt', '2026-03-01')
        ->call('savePet')
        ->assertHasErrors(['petReturnedAt']);
});

test('a cat cannot be registered in a colony of another shelter', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    [$species, $breed] = colonyPetForm($shelter);
    $other = Colony::factory()->create(['species_id' => $species->id]);

    Livewire::test(PetForm::class)
        ->set('petName', 'Tigre')
        ->set('petSpeciesId', $species->id)
        ->set('petBreedId', $breed->id)
        ->set('petGender', 'male')
        ->set('petColonyId', $other->id)
        ->call('savePet')
        ->assertHasErrors(['petColonyId']);

    expect(Pet::query()->where('name', 'Tigre')->exists())->toBeFalse();
});

test('only the colonies of the chosen species are offered, and a mismatched species is rejected', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    [$cat, $catBreed] = colonyPetForm($shelter);
    $rabbit = Species::factory()->create(['has_colonies' => true]);
    $rabbitBreed = Breed::factory()->for($rabbit)->create();
    Colony::factory()->for($shelter)->create(['species_id' => $cat->id, 'name' => 'Cacilhas']);
    $rabbitColony = Colony::factory()->for($shelter)->create(['species_id' => $rabbit->id, 'name' => 'Warren']);

    Livewire::test(PetForm::class)
        ->set('petSpeciesId', $cat->id)
        ->assertSee('Cacilhas')
        ->assertDontSee('Warren')
        ->set('petName', 'Tigre')
        ->set('petBreedId', $catBreed->id)
        ->set('petGender', 'male')
        ->set('petColonyId', $rabbitColony->id)
        ->call('savePet')
        ->assertHasErrors(['petColonyId']);

    expect(Pet::query()->where('name', 'Tigre')->exists())->toBeFalse();
});

test('changing the species clears a colony of another species', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    [$cat] = colonyPetForm($shelter);
    $colony = Colony::factory()->for($shelter)->create(['species_id' => $cat->id]);
    $dog = Species::factory()->create();

    Livewire::withQueryParams(['colony' => $colony->id])
        ->test(PetForm::class)
        ->assertSet('petColonyId', $colony->id)
        ->set('petSpeciesId', $dog->id)
        ->assertSet('petColonyId', null);
});

test('adding an animal from a colony opens the form with the colony species', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->create();

    $this->get(route('colonies.show', $colony))
        ->assertSee(e(route('pets.create', ['colony' => $colony->id, 'species' => $colony->species_id])), false);
});

test('a colony only accepts species flagged for colonies', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'staff')->create());
    $dog = Species::factory()->create(['has_colonies' => false]);

    Livewire::test(ManageColonies::class)
        ->set('colonySpeciesId', $dog->id)
        ->set('colonyName', 'Dogs')
        ->call('saveColony')
        ->assertHasErrors(['colonySpeciesId']);
});

test('the species of a colony cannot change once it has animals', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->create();
    Pet::factory()->for($shelter)->create(['colony_id' => $colony->id]);
    $other = Species::factory()->create(['has_colonies' => true]);

    Livewire::test(ManageColonies::class)
        ->call('editColony', $colony->id)
        ->set('colonySpeciesId', $other->id)
        ->call('saveColony')
        ->assertHasErrors(['colonySpeciesId']);

    expect($colony->fresh()->species_id)->not->toBe($other->id);
});

test('the add button names the colony species', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->for(Species::factory()->state(['name' => 'Gato', 'has_colonies' => true]))->create();

    $this->get(route('colonies.show', $colony))->assertSee('Add Gato');
});

test('the back buttons of the page and add form of a colony animal lead to its colony', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->create(['name' => 'Cacilhas']);
    $pet = Pet::factory()->for($shelter)->create(['species_id' => $colony->species_id, 'colony_id' => $colony->id]);
    $backLink = 'href="'.route('colonies.show', $colony).'"';

    $this->get(route('pets.show', $pet))->assertSee($backLink, false);
    $this->get(route('pets.edit', $pet))->assertDontSee($backLink, false)->assertSee('href="'.route('pets.show', $pet).'"', false);
    $this->get(route('pets.create', ['colony' => $colony->id]))->assertSee($backLink, false);
});

test('the colony page searches and filters its animals while the totals stay whole', function () {
    $shelter = Shelter::factory()->create();
    $colony = Colony::factory()->for($shelter)->create();
    Pet::factory()->for($shelter)->create(['name' => 'Tigre', 'chip' => '985000111', 'colony_id' => $colony->id, 'is_neutered' => true]);
    Pet::factory()->for($shelter)->create(['name' => 'Mimi', 'colony_id' => $colony->id, 'is_neutered' => false, 'neutering_status' => 'pending']);
    Pet::factory()->for($shelter)->create(['name' => 'Bolt', 'colony_id' => $colony->id, 'is_neutered' => false, 'neutering_status' => null]);
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ColonyShow::class, ['colony' => $colony])
        ->set('search', 'Tig')
        ->assertSee('Tigre')->assertDontSee('Mimi')
        ->set('search', '985000')
        ->assertSee('Tigre')->assertDontSee('Mimi')
        ->set('search', '')
        ->set('neuteringFilter', 'pending')
        ->assertSee('Mimi')->assertDontSee('Tigre')->assertDontSee('Bolt')
        ->set('neuteringFilter', 'unknown')
        ->assertSee('Bolt')->assertDontSee('Mimi')->assertDontSee('Tigre')
        ->assertSet('totals.total', 3)
        ->assertSet('totals.pending', 1)
        ->assertSet('totals.unknown', 1);
});

test('the pets list shows the colony as accommodation and filters by it', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->create(['name' => 'Cacilhas']);
    Pet::factory()->for($shelter)->create(['name' => 'Tigre', 'colony_id' => $colony->id, 'species_id' => $colony->species_id]);
    Pet::factory()->for($shelter)->create(['name' => 'Rex']);

    Livewire::test(ManagePets::class)
        ->assertSee('Cacilhas')
        ->set('locationFilter', 'colony:'.$colony->id)
        ->assertSee('Tigre')
        ->assertDontSee('Rex');
});

test('the pets list location filter drops a colony of another species', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->create();
    $other = Species::factory()->create();

    Livewire::test(ManagePets::class)
        ->set('locationFilter', 'colony:'.$colony->id)
        ->set('speciesFilter', (string) $other->id)
        ->assertSet('locationFilter', '');
});

test('the colonies list shows the caretaker and notes, hidden from viewers', function () {
    $shelter = Shelter::factory()->create();
    Colony::factory()->for($shelter)->create(['caretaker_name' => 'Dona Rosa', 'caretaker_phone' => '912345678', 'notes' => 'Feed at dusk']);

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    Livewire::test(ManageColonies::class)->assertSee('Dona Rosa')->assertSee('912345678')->assertSee('Feed at dusk');

    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());
    Livewire::test(ManageColonies::class)->assertDontSee('Dona Rosa')->assertDontSee('Feed at dusk');
});

test('deleting a colony releases its animals so they can be given a new location', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->create();
    $breed = Breed::factory()->for($colony->species)->create();
    $pet = Pet::factory()->for($shelter)->create(['species_id' => $colony->species_id, 'breed_id' => $breed->id, 'colony_id' => $colony->id]);
    $cage = Cage::factory()->for(Wing::factory()->for(Facility::factory()->for($shelter)))->create(['species_id' => null]);

    Livewire::test(ManageColonies::class)->call('deleteColony', $colony->id);

    expect($pet->fresh()->colony_id)->toBeNull();

    Livewire::test(PetForm::class, ['pet' => $pet])
        ->assertSet('petColonyId', null)
        ->set('petCageId', $cage->id)
        ->call('savePet')
        ->assertHasNoErrors();

    expect($pet->fresh()->cage_id)->toBe($cage->id);
});

test('a pet whose colony was soft-deleted elsewhere can still be edited and relocated', function () {
    $shelter = Shelter::factory()->create();
    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());
    $colony = Colony::factory()->for($shelter)->create();
    $breed = Breed::factory()->for($colony->species)->create();
    $pet = Pet::factory()->for($shelter)->create(['species_id' => $colony->species_id, 'breed_id' => $breed->id, 'colony_id' => $colony->id]);
    $colony->delete();

    Livewire::test(PetForm::class, ['pet' => $pet->fresh()])
        ->assertSet('petColonyId', null)
        ->call('savePet')
        ->assertHasNoErrors();
});

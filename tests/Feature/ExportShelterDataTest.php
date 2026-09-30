<?php

use App\Actions\ExportShelterData;
use App\Livewire\Settings\ExportData;
use App\Models\Adoption;
use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Sponsorship;
use App\Models\User;
use App\Models\Vaccine;
use Livewire\Livewire;

/**
 * @return array<string, array<int, string>> CSV name => lines (without the BOM and header)
 */
function exportedFiles(Shelter $shelter): array
{
    $path = app(ExportShelterData::class)->handle($shelter);

    $zip = new ZipArchive;
    $zip->open($path);

    $files = [];

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $content = str_replace("\xEF\xBB\xBF", '', $zip->getFromIndex($i));
        $files[basename($zip->getNameIndex($i), '.csv')] = array_values(array_filter(explode("\n", $content)));
    }

    $zip->close();
    unlink($path);

    return $files;
}

beforeEach(function () {
    $this->shelter = Shelter::factory()->create(['name' => 'Happy Paws']);
    $this->manager = User::factory()->forShelter($this->shelter, 'manager')->create();
});

test('managers can open the export page and see it in the settings menu', function () {
    $this->actingAs($this->manager)
        ->get(route('data-export.edit'))
        ->assertOk()
        ->assertSee('Download export');

    $this->actingAs($this->manager)
        ->get(route('profile.edit'))
        ->assertSee(route('data-export.edit'));
});

test('staff, viewers and admins cannot open the export page', function (string $role) {
    $user = $role === 'admin'
        ? User::factory()->admin()->create()
        : User::factory()->forShelter($this->shelter, $role)->create();

    $this->actingAs($user)->get(route('data-export.edit'))->assertForbidden();
    $this->actingAs($user)->get(route('profile.edit'))->assertDontSee(route('data-export.edit'));
})->with(['staff', 'viewer', 'admin']);

test('managers download a ZIP named after their shelter', function () {
    Livewire::actingAs($this->manager)
        ->test(ExportData::class)
        ->call('export')
        ->assertFileDownloaded('focinhos-happy-paws-'.now()->format('Y-m-d').'.zip');
});

test('the export holds one CSV per area with only the shelter\'s own rows', function () {
    $pet = Pet::factory()->for($this->shelter)->create(['name' => 'Rex']);
    $pet->vaccines()->attach(Vaccine::factory()->create(['name' => 'Rabies']), ['administered_date' => '2026-01-10']);
    Sponsorship::factory()->for($pet)->create(['name' => 'Ana Sponsor']);
    Adoption::factory()->for($pet)->create(['name' => 'Rui Adopter']);
    $member = Member::factory()->for($this->shelter)->create(['name' => 'Maria Member']);
    MemberPayment::factory()->for($member)->create();

    $other = Shelter::factory()->create();
    $otherPet = Pet::factory()->for($other)->create(['name' => 'Stranger']);
    Sponsorship::factory()->for($otherPet)->create(['name' => 'Other Sponsor']);
    Member::factory()->for($other)->create(['name' => 'Other Member']);

    // A manager of another shelter is logged in: the export must still follow the requested shelter.
    $this->actingAs(User::factory()->forShelter($other, 'manager')->create());

    $files = exportedFiles($this->shelter);

    expect(array_keys($files))->toEqualCanonicalizing([
        'pets', 'vaccinations', 'treatments', 'diagnoses', 'adoptions', 'adoption_applications',
        'sponsorships', 'sponsorship_payments', 'members', 'member_payments', 'volunteers',
        'volunteer_availabilities', 'facilities', 'wings', 'cages',
    ])
        ->and(implode("\n", $files['pets']))->toContain('Rex')->not->toContain('Stranger')
        ->and(implode("\n", $files['vaccinations']))->toContain('Rabies')->toContain('Rex')
        ->and(implode("\n", $files['sponsorships']))->toContain('Ana Sponsor')->not->toContain('Other Sponsor')
        ->and(implode("\n", $files['adoptions']))->toContain('Rui Adopter')
        ->and(implode("\n", $files['members']))->toContain('Maria Member')->not->toContain('Other Member')
        ->and($files['member_payments'])->toHaveCount(2);
});

test('text that a spreadsheet would read as a formula is neutralised', function () {
    Pet::factory()->for($this->shelter)->create(['name' => '=HYPERLINK("http://evil.test")']);

    expect(implode("\n", exportedFiles($this->shelter)['pets']))->toContain("'=HYPERLINK");
});

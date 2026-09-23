<?php

use App\Models\Adoption;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Color;
use App\Models\Facility;
use App\Models\FurType;
use App\Models\Pet;
use App\Models\Shelter;
use App\Models\Size;
use App\Models\Species;
use App\Models\Sponsorship;
use App\Models\Wing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Create the shelter, cages and lookup rows the fixture CSVs refer to.
 *
 * @return array{shelter: Shelter, cage: Cage}
 */
function createPortugalZoofiloLookups(): array
{
    $species = Species::factory()->create(['name' => 'Cão']);
    Breed::factory()->for($species)->create(['name' => 'Cão Rafeiro', 'is_default' => true]);
    Breed::factory()->for($species)->create(['name' => 'Labrador Retriever']);

    foreach (['Tigrado', 'Castanho', 'Preto'] as $color) {
        Color::factory()->create(['name' => $color]);
    }

    foreach (['Médio e Ondulado', 'Curto'] as $furType) {
        FurType::factory()->create(['name' => $furType]);
    }

    foreach (['Médio', 'Grande'] as $size) {
        Size::factory()->for($species)->create(['name' => $size]);
    }

    $shelter = Shelter::factory()->create();
    $facility = Facility::factory()->for($shelter)->create(['name' => 'Canil AFAMA']);
    $cage = Cage::factory()->for(Wing::factory()->for($facility)->create(['name' => 'E']))->create(['code' => '04', 'capacity' => 3]);
    Cage::factory()->for(Wing::factory()->for($facility)->create(['name' => 'A']))->create(['code' => '01']);

    return ['shelter' => $shelter, 'cage' => $cage];
}

/**
 * @return array<string, mixed>
 */
function portugalZoofiloOptions(Shelter $shelter, array $options = []): array
{
    $fixtures = base_path('tests/Fixtures/PortugalZoofilo');

    return [
        '--animal' => 'cao',
        '--shelter' => $shelter->id,
        '--animals' => "{$fixtures}/dogs.csv",
        '--adoptions' => "{$fixtures}/dogs_adoptions.csv",
        '--sponsorships' => "{$fixtures}/dogs_sponsorships.csv",
        ...$options,
    ];
}

function petByRef(string $ref): Pet
{
    return Pet::query()->where('ref', $ref)->sole();
}

test('imports a dog still in the shelter with its lookups, cage and PZ ref', function () {
    ['shelter' => $shelter, 'cage' => $cage] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))->assertSuccessful();

    $lana = petByRef('PZ101');
    expect($lana)
        ->shelter_id->toBe($shelter->id)
        ->name->toBe('Lana')
        ->gender->toBe('female')
        ->chip->toBe('900085000279337')
        ->is_pure_breed->toBeFalse()
        ->is_neutered->toBeTrue()
        ->cage_id->toBe($cage->id)
        ->primary_color_id->toBe(Color::query()->where('name', 'Tigrado')->value('id'))
        ->fur_type_id->toBe(FurType::query()->where('name', 'Médio e Ondulado')->value('id'))
        ->size_id->toBe(Size::query()->where('name', 'Médio')->value('id'))
        ->publish_to_portal->toBeTrue()
        ->is_featured->toBeTrue()
        ->internal_notes->toBe("Resgatada do Canil Municipal.\nMuito assustada.")
        ->view_count->toBe(3710)
        ->status->toBe('available');
});

test('takes an adopted dog off the portal and out of its cage, marking it adopted', function () {
    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))->assertSuccessful();

    expect(petByRef('PZ102'))
        ->status->toBe('adopted')
        ->is_pure_breed->toBeTrue()
        ->chip->toBeNull()
        ->cage_id->toBeNull()
        ->is_adoptable->toBeFalse()
        ->is_sponsorable->toBeFalse()
        ->publish_to_portal->toBeFalse()
        ->is_featured->toBeFalse();
});

test('marks a dog with a death date as deceased even with an adoption on record', function () {
    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))->assertSuccessful();

    expect(petByRef('PZ103'))->status->toBe('deceased')->publish_to_portal->toBeFalse();
});

test('imports adoptions with normalised contact details and the last visit in the notes', function () {
    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))->assertSuccessful();

    $adoption = Adoption::query()->where('pet_id', petByRef('PZ102')->id)->sole();
    expect($adoption)
        ->name->toBe('Desconhecido')
        ->phone->toBe('912345678')
        ->postal_code->toBe('9900-089')
        ->city->toBe('Horta')
        ->application_status->toBe('Approved')
        ->notes->toBe("Última visita: 2020-02-01 por Paulo Melo (Bem entregue). Tudo bem.\nOutros contactos: 292391555\n[PZ adopc 5001]");

    expect(Adoption::query()->where('pet_id', petByRef('PZ103')->id)->sole())
        ->name->toBe('Desconhecido')
        ->city->toBe('Alemanha');
});

test('skips adoptions of dogs missing from the dogs file and reports them', function () {
    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))
        ->expectsOutputToContain('Adoption 5003 skipped: animal is not in the animals file')
        ->assertSuccessful();

    expect(Adoption::query()->count())->toBe(2);
});

test('imports sponsorships with a placeholder payment ending on the paid-until date', function () {
    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))->assertSuccessful();

    $sponsorship = Sponsorship::query()->where('pet_id', petByRef('PZ101')->id)->sole();
    expect($sponsorship)
        ->name->toBe('Luísa Narigão')
        ->phone->toBe('967375999')
        ->send_feedback->toBeFalse()
        ->notes->toBe("Válido até 2026-12-02\n[PZ apad 7001]");

    $payment = $sponsorship->payments()->sole();
    expect($payment->start_date->toDateString())->toBe('2025-12-03')
        ->and($payment->end_date->toDateString())->toBe('2026-12-02')
        ->and($payment->payment_date->toDateString())->toBe('2025-12-03')
        ->and($payment->payment_value)->toBe('0.00');
});

test('makes a dog in the shelter sponsorable when it has a sponsor, but not a deceased one', function () {
    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))->assertSuccessful();

    expect(petByRef('PZ101')->is_sponsorable)->toBeTrue()
        ->and(petByRef('PZ103')->is_sponsorable)->toBeFalse();
});

test('updates existing records instead of duplicating them when run again', function () {
    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))->assertSuccessful();
    petByRef('PZ101')->update(['name' => 'Renamed']);
    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter))->assertSuccessful();

    expect(Pet::query()->count())->toBe(3)
        ->and(petByRef('PZ101')->name)->toBe('Lana')
        ->and(Adoption::query()->count())->toBe(2)
        ->and(Sponsorship::query()->count())->toBe(2)
        ->and(Sponsorship::query()->withCount('payments')->get()->sum('payments_count'))->toBe(2);
});

test('saves nothing on a dry run', function () {
    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter, ['--dry-run' => true]))
        ->expectsOutputToContain('[Dry run, nothing saved] 3 animals')
        ->assertSuccessful();

    expect(Pet::query()->count())->toBe(0)
        ->and(Adoption::query()->count())->toBe(0)
        ->and(Sponsorship::query()->count())->toBe(0);
});

test('fails without importing when the shelter does not exist', function () {
    createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', ['--animal' => 'cao', '--shelter' => 999])
        ->expectsOutputToContain('Shelter not found')
        ->assertFailed();

    expect(Pet::query()->count())->toBe(0);
});

test('downloads the biography and photos of each dog from the portal, skipping unpublished dogs', function () {
    Storage::fake();
    Http::preventStrayRequests();

    $portal = 'http://www.portugalzoofilo.net';
    $page = '<div class="biografia"><h2>Biografia e Apresentação: </h2>'
        .'<p style="font-size: 12px;"><span onclick="x()">Resgatada do Ch&atilde;o Frio.&nbsp;Gosta de &lt;script&gt;.</span></p>'
        .'<p><strong> </strong>&nbsp;</p><table><tr><td>Data de Nascimento:</td><td>30 de Maio de 2016</td></tr></table></div>';

    Http::fake([
        "{$portal}/caes/cao.jsp?animal_id=101" => Http::response(mb_convert_encoding($page, 'ISO-8859-1', 'UTF-8')),
        "{$portal}/caes/cao.jsp?animal_id=*" => Http::response('', 302, ['Location' => "{$portal}/"]),
        "{$portal}/" => Http::response('<div class="biografia"><h2>Biografia e Apresentação: </h2><p>Outro cão.</p></div>'),
        "{$portal}/images/instituicoes/68/animais/101.jpg" => Http::response('main', 200, ['Content-Type' => 'image/jpeg']),
        "{$portal}/images/instituicoes/68/animais/101_2.jpg" => Http::response('second', 200, ['Content-Type' => 'image/jpeg']),
        "{$portal}/images/instituicoes/68/animais/*" => Http::response('Not found', 404, ['Content-Type' => 'text/html']),
    ]);

    ['shelter' => $shelter] = createPortugalZoofiloLookups();

    $this->artisan('app:import-portugal-zoofilo', portugalZoofiloOptions($shelter, ['--with-portal' => true, '--institution' => 68]))
        ->assertSuccessful();

    $lana = petByRef('PZ101');
    expect($lana->description)->toBe('<p>Resgatada do Chão Frio. Gosta de &lt;script&gt;.</p>')
        ->and(petByRef('PZ102')->description)->toBeNull();

    $images = $lana->images()->orderBy('id')->get();
    expect($images)->toHaveCount(2)
        ->and($images->pluck('is_main')->all())->toBe([true, false])
        ->and(Storage::get($images[0]->image_path))->toBe('main')
        ->and(petByRef('PZ102')->images()->count())->toBe(0);
});

test('imports cats as their species without a size, reading their portal pages', function () {
    Storage::fake();
    Http::preventStrayRequests();

    $portal = 'http://www.portugalzoofilo.net';
    Http::fake([
        "{$portal}/gatos/gato.jsp?animal_id=201" => Http::response('<div class="biografia"><h2>Biografia e Apresentação: </h2><p>Gatinha meiga.</p></div>'),
        "{$portal}/images/instituicoes/68/animais/*" => Http::response('Not found', 404, ['Content-Type' => 'text/html']),
    ]);

    $species = Species::factory()->create(['name' => 'Gato']);
    Breed::factory()->for($species)->create(['name' => 'Europeu Comum', 'is_default' => true]);
    Color::factory()->create(['name' => 'Amarelo']);
    FurType::factory()->create(['name' => 'Curto']);
    $shelter = Shelter::factory()->create();

    $this->artisan('app:import-portugal-zoofilo', [
        '--animal' => 'gato',
        '--shelter' => $shelter->id,
        '--animals' => base_path('tests/Fixtures/PortugalZoofilo/cats.csv'),
        '--with-portal' => true,
        '--institution' => 68,
    ])
        ->doesntExpectOutputToContain('porte_nome')
        ->assertSuccessful();

    expect(petByRef('PZ201'))
        ->species_id->toBe($species->id)
        ->size_id->toBeNull()
        ->is_pure_breed->toBeFalse()
        ->description->toBe('<p>Gatinha meiga.</p>');
});

test('fails when the kind of animal is not given', function () {
    $shelter = Shelter::factory()->create();

    $this->artisan('app:import-portugal-zoofilo', ['--shelter' => $shelter->id])
        ->expectsOutputToContain('--animal=cao or --animal=gato')
        ->assertFailed();
});

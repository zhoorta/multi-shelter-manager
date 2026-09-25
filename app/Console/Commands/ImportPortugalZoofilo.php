<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Adoption;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Color;
use App\Models\FurType;
use App\Models\Member;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Shelter;
use App\Models\Size;
use App\Models\Species;
use App\Models\Sponsorship;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

#[Signature('app:import-portugal-zoofilo
    {--animal= : Kind of animal in the files: cao or gato}
    {--shelter= : Id of the shelter the animals belong to}
    {--animals= : Animals CSV exported from Portugal Zoófilo}
    {--adoptions= : Adoptions CSV (optional)}
    {--sponsorships= : Sponsorships CSV (optional)}
    {--members= : Members (sócios) CSV, which can be imported on its own without --animal and --animals}
    {--with-portal : Also download each animal\'s biography and photos from portugalzoofilo.net}
    {--institution= : The shelter\'s Portugal Zoófilo institution id, required by --with-portal}
    {--dry-run : Import inside a transaction that is rolled back, only reporting the result}')]
#[Description('Import dogs or cats, their adoptions and sponsorships, and the members exported from Portugal Zoófilo into a shelter')]
class ImportPortugalZoofilo extends Command
{
    private const string PORTAL_URL = 'http://www.portugalzoofilo.net';

    /**
     * Each kind of animal the portal handles: the species it is imported
     * as, and the path of its public page on the portal.
     *
     * @var array<string, array{species: string, page: string}>
     */
    private const array ANIMALS = [
        'cao' => ['species' => 'Cão', 'page' => 'caes/cao.jsp'],
        'gato' => ['species' => 'Gato', 'page' => 'gatos/gato.jsp'],
    ];

    private const string DEFAULT_PAYMENT_NOTE = 'Importado do Portugal Zoófilo — valor desconhecido';

    /**
     * Adopter "names" in the export that are really the country the animal
     * was adopted into, so they are stored as the city instead.
     */
    private const array COUNTRY_NAMES = ['Alemanha'];

    /**
     * The export's file header columns: the first column of the header
     * line of each kind of file.
     */
    private const array HEADER_COLUMNS = ['animal_id', 'pessoa_id'];

    /** @var array<int, array{0: string, 1: string}> */
    private array $warnings = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $shelter = Shelter::query()->find($this->option('shelter'));

        if ($shelter === null) {
            $this->error('Shelter not found. Pass its id with --shelter.');

            return self::FAILURE;
        }

        $memberRows = $this->readCsv($this->option('members'));

        if ($this->option('members') !== null && $memberRows === null) {
            $this->error("Members file not found: {$this->option('members')}");

            return self::FAILURE;
        }

        $importsAnimals = $memberRows === null || $this->option('animals') !== null;
        $animal = self::ANIMALS[$this->option('animal')] ?? null;

        if ($importsAnimals && $animal === null) {
            $this->error('Pass the kind of animal with --animal='.implode(' or --animal=', array_keys(self::ANIMALS)).'.');

            return self::FAILURE;
        }

        $species = $importsAnimals ? Species::query()->where('name', $animal['species'])->first() : null;

        if ($importsAnimals && $species === null) {
            $this->error("Species \"{$animal['species']}\" not found.");

            return self::FAILURE;
        }

        if ($importsAnimals && $this->option('with-portal') && ! $this->option('institution')) {
            $this->error('--with-portal needs the shelter\'s Portugal Zoófilo --institution id.');

            return self::FAILURE;
        }

        $animalRows = $importsAnimals ? $this->readCsv($this->option('animals')) : [];

        if ($animalRows === null) {
            $this->error("Animals file not found: {$this->option('animals')}");

            return self::FAILURE;
        }

        $adoptionRows = $this->readCsv($this->option('adoptions')) ?? [];
        $sponsorshipRows = $this->readCsv($this->option('sponsorships')) ?? [];

        DB::beginTransaction();

        try {
            $pets = $importsAnimals ? $this->importAnimals($shelter, $species, $animalRows) : [];
            $adoptionCount = $this->importAdoptions($pets, $adoptionRows);
            $sponsorshipCount = $this->importSponsorships($pets, $sponsorshipRows);
            $this->refreshStatuses($pets);
            $memberCount = $this->importMembers($shelter, $memberRows ?? []);

            $this->option('dry-run') ? DB::rollBack() : DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        if ($importsAnimals && $this->option('with-portal') && ! $this->option('dry-run')) {
            $this->importPortalContent($pets, $animal['page'], (string) $this->option('institution'));
        }

        $this->reportResult($pets, $adoptionCount, $sponsorshipCount, $memberCount);

        return self::SUCCESS;
    }

    /**
     * Read a semicolon-separated export into rows keyed by column name.
     * Lines before the "animal_id" or "pessoa_id" header (e.g. a "Table 1" title added by
     * spreadsheet exports) are skipped. Returns null when no file is given
     * or it is missing.
     *
     * @return array<int, array<string, string>>|null
     */
    private function readCsv(?string $path): ?array
    {
        if ($path === null) {
            return null;
        }

        $path = str_starts_with($path, '/') ? $path : base_path($path);

        if (! is_file($path)) {
            return null;
        }

        $handle = fopen($path, 'r');
        $header = null;
        $rows = [];

        while (($cells = fgetcsv($handle, null, ';', '"', '')) !== false) {
            $cells = array_map(fn (?string $cell): string => trim((string) $cell), $cells);

            if ($header === null) {
                $cells[0] = preg_replace('/^\xEF\xBB\xBF/', '', $cells[0]);

                if (in_array($cells[0], self::HEADER_COLUMNS, true)) {
                    $header = $cells;
                }

                continue;
            }

            if ($cells === ['']) {
                continue;
            }

            $cells = array_pad(array_slice($cells, 0, count($header)), count($header), '');
            $rows[] = array_combine($header, $cells);
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Create or update one pet per animal row, matched on shelter +
     * "PZ{animal_id}" ref so the import can be re-run (the portal's ids are
     * unique across species). Animals that have left or died are taken off
     * adoption, sponsorship and the portal, and only animals still in the
     * shelter keep their cage. Species without sizes (cats) get none.
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array<string, Pet>
     */
    private function importAnimals(Shelter $shelter, Species $species, array $rows): array
    {
        $breeds = Breed::query()->where('species_id', $species->id)->get()->keyBy(fn (Breed $breed): string => mb_strtolower($breed->name));
        $colors = Color::query()->get()->keyBy(fn (Color $color): string => mb_strtolower($color->name));
        $furTypes = FurType::query()->get()->keyBy(fn (FurType $furType): string => mb_strtolower($furType->name));
        $sizes = Size::query()->where('species_id', $species->id)->get()->keyBy(fn (Size $size): string => mb_strtolower($size->name));
        $cages = Cage::query()
            ->with('wing.facility')
            ->whereHas('wing.facility', fn ($query) => $query->where('shelter_id', $shelter->id))
            ->get()
            ->keyBy(fn (Cage $cage): string => $this->cageKey($cage->wing->facility->name, $cage->wing->name, $cage->code));
        $chipCounts = collect($rows)->pluck('animal_chip_id')->filter()->countBy();

        $pets = [];

        foreach ($rows as $row) {
            $ref = 'PZ'.$row['animal_id'];
            $breed = $breeds->get(mb_strtolower($row['raca_nome']));
            $gender = match ($row['animal_sexo']) {
                'Macho' => 'male',
                'Fêmea' => 'female',
                default => null,
            };

            if ($breed === null || $gender === null) {
                $this->addWarning($ref, $breed === null
                    ? "Skipped: unknown breed \"{$row['raca_nome']}\""
                    : "Skipped: unknown gender \"{$row['animal_sexo']}\"");

                continue;
            }

            $checkinDate = $this->value($row, 'animal_data_entrada_inst');
            $checkoutDate = $this->value($row, 'animal_data_saida_inst');
            $deathDate = $this->value($row, 'animal_data_obito');
            $birthDate = $this->value($row, 'animal_data_nascimento');
            $isInShelter = $checkoutDate === null && $deathDate === null;

            $pet = Pet::query()->withoutGlobalScope('shelter')->firstOrNew(['shelter_id' => $shelter->id, 'ref' => $ref]);
            $pet->fill([
                'species_id' => $species->id,
                'breed_id' => $breed->id,
                'is_pure_breed' => ! $breed->is_default,
                'primary_color_id' => $this->lookup($colors, $row, 'cor_primaria', $ref),
                'secondary_color_id' => $this->lookup($colors, $row, 'cor_secundaria', $ref),
                'fur_type_id' => $this->lookup($furTypes, $row, 'tippelo_nome', $ref),
                'size_id' => $sizes->isNotEmpty() ? $this->lookup($sizes, $row, 'porte_nome', $ref) : null,
                'cage_id' => $isInShelter ? $this->findCageId($cages, $row, $ref) : null,
                'name' => $row['animal_nome'],
                'chip' => $this->chip($row['animal_chip_id'], $chipCounts, $ref),
                'is_neutered' => $row['animal_esterilizado'] !== '',
                'gender' => $gender,
                'birth_date' => $birthDate,
                'date_of_death' => $deathDate,
                'checkin_date' => $checkinDate,
                'checkout_date' => $checkoutDate,
                'is_adoptable' => $isInShelter && $row['animal_para_adopcao'] === 'Adoptável',
                'is_sponsorable' => $isInShelter && $row['animal_para_apadrinhar'] === 'Apadrinhável',
                'publish_to_portal' => $isInShelter && $row['animal_divulgar_portal'] === 'Divulgado',
                'is_featured' => $isInShelter && $row['animal_em_destaque'] === 'Destacado',
                'internal_notes' => $this->multilineValue($row, 'notas_internas'),
            ]);
            $pet->view_count = (int) $row['nr_visualizacoes'];
            $pet->save();

            if ($birthDate !== null && $checkinDate !== null && $birthDate > $checkinDate) {
                $this->addWarning($ref, "Birth date {$birthDate} is after check-in date {$checkinDate}");
            }

            if ($checkoutDate !== null && $checkinDate !== null && $checkoutDate < $checkinDate) {
                $this->addWarning($ref, "Checkout date {$checkoutDate} is before check-in date {$checkinDate}");
            }

            $pets[$ref] = $pet;
        }

        return $pets;
    }

    /**
     * Create or update each adoption, matched on the export's adoption id
     * kept as a marker in the notes.
     *
     * @param  array<string, Pet>  $pets
     * @param  array<int, array<string, string>>  $rows
     */
    private function importAdoptions(array $pets, array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $ref = 'PZ'.$row['animal_id'];
            $pet = $pets[$ref] ?? null;

            if ($pet === null) {
                $this->addWarning($ref, "Adoption {$row['adopc_id']} skipped: animal is not in the animals file");

                continue;
            }

            $marker = "[PZ adopc {$row['adopc_id']}]";
            $name = $this->value($row, 'adopc_nome_dono');
            $isCountry = in_array($name, self::COUNTRY_NAMES, true);
            $phones = $this->phones($row, ['adopc_telemovel', 'adopc_telefone_casa', 'adopc_telefone_emprego']);
            $adoptionDate = $row['adopc_data_adopcao'];
            $returnDate = $this->value($row, 'adopc_data_devolucao');

            $notes = array_filter([
                $this->visitNote($row),
                count($phones) > 1 ? 'Outros contactos: '.implode(', ', array_slice($phones, 1)) : null,
                $marker,
            ]);

            $adoption = Adoption::query()->where('pet_id', $pet->id)->where('notes', 'like', "%{$marker}%")->first()
                ?? new Adoption(['pet_id' => $pet->id]);
            $adoption->fill([
                'name' => $name === null || str_starts_with($name, '?') || $isCountry ? 'Desconhecido' : $name,
                'email' => $this->value($row, 'adopc_email'),
                'phone' => $phones[0] ?? null,
                'address' => $this->value($row, 'adopc_morada'),
                'postal_code' => $this->postalCode($row, 'adopc_codpostal_4', 'adopc_codpostal_3'),
                'city' => $isCountry ? $name : $this->city($this->value($row, 'adopc_locpostal')),
                'adoption_date' => $adoptionDate,
                'return_date' => $returnDate,
                'notes' => implode("\n", $notes),
                'application_status' => 'Approved',
            ])->save();

            if ($returnDate !== null && $returnDate < $adoptionDate) {
                $this->addWarning($ref, "Adoption {$row['adopc_id']}: return date {$returnDate} is before adoption date {$adoptionDate}");
            }

            $count++;
        }

        return $count;
    }

    /**
     * Create or update each sponsorship, matched on the export's sponsorship
     * id kept as a marker in the notes. The "paid until" date becomes a single
     * placeholder payment covering the year before it, and an animal still in the
     * shelter that has a sponsor is made sponsorable so the team can edit it.
     *
     * @param  array<string, Pet>  $pets
     * @param  array<int, array<string, string>>  $rows
     */
    private function importSponsorships(array $pets, array $rows): int
    {
        $namesByEmail = $this->preferredNamesByEmail($rows);
        $count = 0;

        foreach ($rows as $row) {
            $ref = 'PZ'.$row['animal_id'];
            $pet = $pets[$ref] ?? null;

            if ($pet === null) {
                $this->addWarning($ref, "Sponsorship {$row['apad_id']} skipped: animal is not in the animals file");

                continue;
            }

            $marker = "[PZ apad {$row['apad_id']}]";
            $email = $this->value($row, 'apad_email');
            $validUntil = $this->value($row, 'valido_ate');
            $phones = $this->phones($row, ['apad_telemovel', 'apad_telefone_casa']);

            $sponsorship = Sponsorship::query()->where('pet_id', $pet->id)->where('notes', 'like', "%{$marker}%")->first()
                ?? new Sponsorship(['pet_id' => $pet->id]);
            $sponsorship->fill([
                'name' => $namesByEmail[mb_strtolower((string) $email)] ?? $row['apad_nome_padrinho'],
                'email' => $email,
                'phone' => $phones[0] ?? null,
                'address' => $this->value($row, 'apad_morada'),
                'postal_code' => $this->postalCode($row, 'apad_codpostal_4', 'apad_codpostal_3'),
                'city' => $this->city($this->value($row, 'apad_locpostal')),
                'send_feedback' => $row['apad_envio_feedback'] === 'Enviar',
                'send_newsletter' => false,
                'notes' => implode("\n", array_filter([$validUntil !== null ? "Válido até {$validUntil}" : null, $marker])),
            ])->save();

            if ($validUntil !== null && ! $sponsorship->payments()->exists()) {
                $startDate = Carbon::parse($validUntil)->subYear()->addDay();

                $sponsorship->payments()->create([
                    'start_date' => $startDate,
                    'end_date' => $validUntil,
                    'payment_date' => $startDate,
                    'payment_value' => 0,
                    'notes' => self::DEFAULT_PAYMENT_NOTE,
                ]);
            }

            if ($pet->checkout_date === null && $pet->date_of_death === null && ! $pet->is_sponsorable) {
                $pet->update(['is_sponsorable' => true]);
                $this->addWarning($ref, 'Marked as sponsorable because it has a sponsor');
            }

            $count++;
        }

        return $count;
    }

    /**
     * Create or update each member, matched on the export's person id kept as
     * a marker in the notes. The PZ reference (usually free text) becomes the
     * member number when it is a number still free in the shelter; otherwise
     * the next number is given and the reference is kept in the notes. A cancellation date makes the member "left". The joia is not in
     * the export, so it is 0 (never owed). A "quota paga" that is a year gets
     * a placeholder yearly payment for that year; any other value is kept in
     * the notes, as its meaning is unknown.
     *
     * @param  array<int, array<string, string>>  $rows
     */
    private function importMembers(Shelter $shelter, array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $ref = 'Sócio '.$row['pessoa_id'];
            $marker = "[PZ socio {$row['pessoa_id']}]";
            $name = $this->value($row, 'pessoa_nome');

            if ($name === null) {
                $this->addWarning($ref, 'Skipped: no name');

                continue;
            }

            $member = Member::query()->withoutGlobalScope('shelter')
                ->where('shelter_id', $shelter->id)
                ->where('notes', 'like', "%{$marker}%")
                ->first() ?? new Member(['shelter_id' => $shelter->id]);

            $joinDate = $this->joinDate($row, $ref);
            $cancellationDate = $this->value($row, 'socio_data_cancelamento');
            $paidYear = $this->value($row, 'socio_quota_paga');
            $isPaidYear = $paidYear !== null && preg_match('/^(19|20)\d{2}$/', $paidYear) === 1;
            $phones = $this->phones($row, ['pessoa_telemovel', 'pessoa_telefone_casa']);
            $reference = $this->value($row, 'socio_referencia');

            if (! $member->exists) {
                $member->member_number = $this->memberNumber($shelter, $reference, $ref);
            }

            if ($paidYear !== null && ! $isPaidYear) {
                $this->addWarning($ref, "Unrecognised quota paga \"{$paidYear}\" kept in the notes");
            }

            $notes = array_filter([
                $this->multilineValue($row, 'socio_notas'),
                $reference !== null && (string) $member->member_number !== $reference ? "Referência Portugal Zoófilo: {$reference}" : null,
                $cancellationDate !== null ? "Saída: {$cancellationDate}" : null,
                $paidYear !== null && ! $isPaidYear ? "Quota paga (Portugal Zoófilo): {$paidYear}" : null,
                count($phones) > 1 ? 'Outros contactos: '.implode(', ', array_slice($phones, 1)) : null,
                $marker,
            ]);

            $member->fill([
                'name' => $name,
                'tin' => $this->value($row, 'pessoa_nif'),
                'email' => $this->value($row, 'pessoa_email'),
                'phone' => $phones[0] ?? null,
                'address' => $this->value($row, 'pessoa_morada'),
                'postal_code' => $this->postalCode($row, 'pessoa_codpostal_4', 'pessoa_codpostal_3'),
                'city' => $this->city($this->value($row, 'pessoa_locpostal')),
                'join_date' => $joinDate,
                'status' => $cancellationDate !== null ? 'left' : 'active',
                'joining_fee' => $member->exists ? $member->joining_fee : 0,
                'membership_fee' => $this->amount($row, 'socio_quota_definida', $ref) ?? 0,
                'membership_fee_frequency' => 'yearly',
                'notes' => implode("\n", $notes),
            ]);

            $member->save();

            if ($isPaidYear && ! $member->payments()->exists()) {
                $member->payments()->create([
                    'type' => 'membership_fee',
                    'start_date' => "{$paidYear}-01-01",
                    'end_date' => "{$paidYear}-12-31",
                    'payment_date' => "{$paidYear}-01-01",
                    'payment_value' => 0,
                    'notes' => self::DEFAULT_PAYMENT_NOTE,
                ]);
            }

            $count++;
        }

        return $count;
    }

    /**
     * The join date, or 1 January of the join year when only the year is
     * given, or today when neither is.
     *
     * @param  array<string, string>  $row
     */
    private function joinDate(array $row, string $ref): string
    {
        $date = $this->value($row, 'socio_data_adesao');

        if ($date !== null) {
            return Str::before($date, ' ');
        }

        $year = $this->value($row, 'socio_ano_adesao');

        if ($year !== null && preg_match('/^(19|20)\d{2}$/', $year) === 1) {
            return "{$year}-01-01";
        }

        $this->addWarning($ref, 'No join date, set to today');

        return today()->toDateString();
    }

    /**
     * The PZ member reference when it is a number not yet used in the shelter
     * (trashed members included), or null to number the member automatically.
     */
    private function memberNumber(Shelter $shelter, ?string $reference, string $ref): ?int
    {
        if ($reference === null) {
            return null;
        }

        $isTaken = ctype_digit($reference) && Member::query()->withoutGlobalScope('shelter')->withTrashed()
            ->where('shelter_id', $shelter->id)
            ->where('member_number', (int) $reference)
            ->exists();

        if (! ctype_digit($reference) || $isTaken || (int) $reference === 0) {
            $this->addWarning($ref, "Member reference \"{$reference}\" is not a free number, numbered automatically");

            return null;
        }

        return (int) $reference;
    }

    /**
     * A money amount written as "12,50", "12.50" or "12 €", or null when empty.
     *
     * @param  array<string, string>  $row
     */
    private function amount(array $row, string $column, string $ref): ?string
    {
        $value = $this->value($row, $column);

        if ($value === null) {
            return null;
        }

        $amount = str_replace(',', '.', preg_replace('/[^\d,.]/u', '', $value) ?? '');

        if (! is_numeric($amount)) {
            $this->addWarning($ref, "Invalid {$column} \"{$value}\" ignored");

            return null;
        }

        return $amount;
    }

    /**
     * Recalculate every imported pet's status from its dates and adoptions.
     *
     * @param  array<string, Pet>  $pets
     */
    private function refreshStatuses(array $pets): void
    {
        foreach ($pets as $pet) {
            $pet->status = $pet->determineStatus();
            $pet->save();
        }
    }

    /**
     * Download each animal's biography and photos from its portal page. Animals
     * that already have a description or photos keep them, so this step can
     * be re-run to fill only what is missing.
     *
     * @param  array<string, Pet>  $pets
     */
    private function importPortalContent(array $pets, string $page, string $institution): void
    {
        $this->info('Downloading biographies and photos from portugalzoofilo.net...');
        $progressBar = $this->output->createProgressBar(count($pets));

        foreach ($pets as $ref => $pet) {
            $animalId = Str::after($ref, 'PZ');

            try {
                if ($pet->description === null) {
                    $description = $this->fetchBiography($page, $animalId);

                    if ($description !== null) {
                        $pet->update(['description' => $description]);
                    }
                }

                if (! $pet->images()->exists() && $this->downloadPhotos($pet, $animalId, $institution) === 0) {
                    $this->addWarning($ref, 'No photos found on the portal');
                }
            } catch (ConnectionException $exception) {
                $this->addWarning($ref, 'Portal unreachable: '.$exception->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    /**
     * Extract the "Biografia e Apresentação" section of an animal's portal page
     * as sanitized description HTML. Pasted tables (usually a repeated birth
     * date) are dropped. Returns null for unpublished animals: their page
     * redirects to an unrelated one, so redirects are never followed.
     */
    private function fetchBiography(string $page, string $animalId): ?string
    {
        $response = Http::timeout(30)->withoutRedirecting()->get(self::PORTAL_URL.'/'.$page, ['animal_id' => $animalId]);

        if (! $response->successful()) {
            return null;
        }

        $html = mb_convert_encoding($response->body(), 'UTF-8', 'ISO-8859-1');

        if (! preg_match('#<h2>\s*Biografia e Apresenta[^<:]*:\s*</h2>(.*?)</div>#su', $html, $matches)) {
            return null;
        }

        $description = preg_replace('#<table\b.*?</table>#si', '', $matches[1]) ?? $matches[1];
        $description = $this->decodeEntities((string) Pet::sanitizeDescription($description));
        $description = preg_replace('#<(b|strong|i|em|u)>\s*</\1>#u', '', $description) ?? $description;
        $description = preg_replace('#<p>\s*</p>#u', '', $description) ?? $description;
        $description = trim(preg_replace('/[ \t]+/u', ' ', $description) ?? $description);

        return trim(strip_tags($description)) !== '' ? $description : null;
    }

    /**
     * Decode HTML entities to plain characters, except the ones that would
     * turn escaped text into markup. Non-breaking spaces become plain spaces.
     */
    private function decodeEntities(string $html): string
    {
        $decoded = preg_replace_callback('/&(#\d+|#x[0-9a-f]+|[a-z]+\d*);/i', function (array $match): string {
            $character = html_entity_decode($match[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return in_array($character, ['<', '>', '&'], true) ? $match[0] : $character;
        }, $html) ?? $html;

        return str_replace("\u{00A0}", ' ', $decoded);
    }

    /**
     * Download the animal's main photo and up to two extra photos, stored the
     * same way as photos uploaded through the pet form. Returns how many
     * photos were saved.
     */
    private function downloadPhotos(Pet $pet, string $animalId, string $institution): int
    {
        $saved = 0;

        foreach (['', '_2', '_3'] as $suffix) {
            $response = Http::timeout(30)->withoutRedirecting()->get(self::PORTAL_URL."/images/instituicoes/{$institution}/animais/{$animalId}{$suffix}.jpg");

            if (! $response->successful() || ! str_starts_with($response->header('Content-Type'), 'image/')) {
                continue;
            }

            $path = 'pets/'.Str::random(40).'.jpg';
            Storage::put($path, $response->body());

            PetImage::query()->create([
                'pet_id' => $pet->id,
                'image_path' => $path,
                'is_main' => $saved === 0,
            ]);

            $saved++;
        }

        return $saved;
    }

    /**
     * @param  array<string, Pet>  $pets
     */
    private function reportResult(array $pets, int $adoptionCount, int $sponsorshipCount, int $memberCount): void
    {
        if ($this->warnings !== []) {
            $this->table(['Ref', 'Warning'], $this->warnings);
        }

        $statuses = collect($pets)->countBy('status')->map(fn (int $count, string $status): string => "{$status}: {$count}")->implode(', ');

        $this->info(sprintf(
            '%s%d animals (%s), %d adoptions, %d sponsorships, %d members, %d warnings.',
            $this->option('dry-run') ? '[Dry run, nothing saved] ' : 'Imported ',
            count($pets),
            $statuses,
            $adoptionCount,
            $sponsorshipCount,
            $memberCount,
            count($this->warnings),
        ));
    }

    /**
     * @param  Collection<string, Cage>  $cages
     * @param  array<string, string>  $row
     */
    private function findCageId(Collection $cages, array $row, string $ref): ?int
    {
        $location = [$row['instal_nome'], $row['ala_nome'], $row['jaula_referencia']];

        if (collect($location)->contains(fn (string $part): bool => $part === '' || str_starts_with($part, 'Sem '))) {
            return null;
        }

        $cage = $cages->get($this->cageKey(...$location));

        if ($cage === null) {
            $this->addWarning($ref, 'Unknown cage '.implode(' / ', $location));
        }

        return $cage?->id;
    }

    private function cageKey(string $facility, string $wing, string $cage): string
    {
        return mb_strtolower("{$facility}|{$wing}|{$cage}");
    }

    /**
     * @param  Collection<string, Color|FurType|Size>  $lookup
     * @param  array<string, string>  $row
     */
    private function lookup(Collection $lookup, array $row, string $column, string $ref): ?int
    {
        $name = $this->value($row, $column);

        if ($name === null) {
            return null;
        }

        $model = $lookup->get(mb_strtolower($name));

        if ($model === null) {
            $this->addWarning($ref, "Unknown {$column} \"{$name}\"");
        }

        return $model?->id;
    }

    /**
     * @param  Collection<string, int>  $chipCounts
     */
    private function chip(string $chip, Collection $chipCounts, string $ref): ?string
    {
        if ($chip === '') {
            return null;
        }

        if (! ctype_digit($chip)) {
            $this->addWarning($ref, "Invalid chip \"{$chip}\" ignored");

            return null;
        }

        if (strlen($chip) !== 15) {
            $this->addWarning($ref, "Chip {$chip} has ".strlen($chip).' digits instead of 15');
        }

        if ($chipCounts->get($chip) > 1) {
            $this->addWarning($ref, "Chip {$chip} is shared with another animal");
        }

        return $chip;
    }

    /**
     * Real phone numbers in the given columns, in order; the export uses
     * "-1" for empty and some short internal codes that are not phones.
     *
     * @param  array<string, string>  $row
     * @param  array<int, string>  $columns
     * @return array<int, string>
     */
    private function phones(array $row, array $columns): array
    {
        return collect($columns)
            ->map(fn (string $column): string => preg_replace('/\s+/', '', $row[$column]) ?? '')
            ->filter(fn (string $phone): bool => preg_match('/^\+?\d{9,}$/', $phone) === 1)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Build a Portuguese "1234-567" postal code. The export dropped the
     * leading zeros of the second part, so it is padded back to 3 digits.
     *
     * @param  array<string, string>  $row
     */
    private function postalCode(array $row, string $firstColumn, string $secondColumn): ?string
    {
        $first = $row[$firstColumn];
        $second = $row[$secondColumn];

        if (preg_match('/^\d{4}$/', $first) !== 1) {
            return null;
        }

        return preg_match('/^\d{1,3}$/', $second) === 1
            ? $first.'-'.str_pad($second, 3, '0', STR_PAD_LEFT)
            : $first;
    }

    /**
     * Title-case a city typed in all capitals or starting in lowercase
     * ("HORTA", "cONCEIÇÃO"); anything else is kept as typed.
     */
    private function city(?string $city): ?string
    {
        if ($city === null) {
            return null;
        }

        $isMiscased = $city === mb_strtoupper($city) || mb_substr($city, 0, 1) === mb_strtolower(mb_substr($city, 0, 1));

        return $isMiscased ? mb_convert_case($city, MB_CASE_TITLE) : $city;
    }

    /**
     * Summarise the last follow-up visit recorded for an adoption.
     *
     * @param  array<string, string>  $row
     */
    private function visitNote(array $row): ?string
    {
        $visitDate = $this->value($row, 'visita_data_ultima');

        if ($visitDate === null) {
            return null;
        }

        $opinion = match ($row['visita_opiniao']) {
            'BEM_ENTREGUE' => 'Bem entregue',
            'CONTINUAR_CONTROLO' => 'Continuar controlo',
            default => $this->value($row, 'visita_opiniao'),
        };

        $note = "Última visita: {$visitDate}";
        $note .= $this->value($row, 'visita_nome_visitante') !== null ? " por {$row['visita_nome_visitante']}" : '';
        $note .= $opinion !== null ? " ({$opinion})" : '';
        $note .= $this->value($row, 'visita_notas') !== null ? '. '.$this->multilineValue($row, 'visita_notas') : '';

        return $note;
    }

    /**
     * The best-spelled sponsor name for each email: the one with the most
     * accented characters ("Narigão" over "Narigao").
     *
     * @param  array<int, array<string, string>>  $rows
     * @return array<string, string>
     */
    private function preferredNamesByEmail(array $rows): array
    {
        return collect($rows)
            ->filter(fn (array $row): bool => $row['apad_email'] !== '')
            ->groupBy(fn (array $row): string => mb_strtolower($row['apad_email']))
            ->map(fn (Collection $group): string => $group
                ->pluck('apad_nome_padrinho')
                ->sortByDesc(fn (string $name): int => strlen($name) - mb_strlen($name))
                ->first())
            ->all();
    }

    /**
     * The trimmed cell, or null when empty or the export's "-1" placeholder.
     *
     * @param  array<string, string>  $row
     */
    private function value(array $row, string $column): ?string
    {
        $value = $row[$column] ?? '';

        return $value === '' || $value === '-1' ? null : $value;
    }

    /**
     * @param  array<string, string>  $row
     */
    private function multilineValue(array $row, string $column): ?string
    {
        $value = $this->value($row, $column);

        return $value !== null ? str_replace(["\r\n", "\r"], "\n", $value) : null;
    }

    private function addWarning(string $ref, string $message): void
    {
        $this->warnings[] = [$ref, $message];
    }
}

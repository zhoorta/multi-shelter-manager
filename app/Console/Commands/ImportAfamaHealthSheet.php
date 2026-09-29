<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Pet;
use App\Models\PetTreatment;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Treatment;
use App\Models\Vaccine;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('app:import-afama-health-sheet
    {--shelter= : Id of the shelter the animals belong to}
    {--dogs= : The "cães" sheet saved as a semicolon-separated CSV}
    {--cats= : The "Gatos" sheet saved as a semicolon-separated CSV}
    {--clinical= : The "Informação Clinica" sheet saved as a semicolon-separated CSV}
    {--dry-run : Import inside a transaction that is rolled back, only reporting the result}')]
#[Description('Import the last and next vaccination and deworming dates, weights and clinical notes from AFAMA\'s health spreadsheet')]
class ImportAfamaHealthSheet extends Command
{
    /**
     * The first column of each sheet's header line.
     */
    private const string HEADER_COLUMN = 'animal_nº PZ';

    /**
     * The product AFAMA deworms with: one dose covers internal and external
     * parasites for 3 months.
     */
    private const string DEWORMING_PRODUCT = 'Nexgard';

    private const string DEWORMING_TREATMENT = 'Desparasitação interna + externa';

    private const string DEWORMING_COLUMN = 'Último nexgard (Desparasitar)';

    /**
     * Each animal sheet: the species it holds, its weight column, and its
     * vaccine columns (last dose, next date) with the catalogue vaccine
     * they are imported as. Columns listed as unmapped have no catalogue
     * vaccine and only raise a warning when filled in.
     *
     * @var array<string, array{species: string, weight: string, vaccines: array<int, array{last: string, next: string, vaccine: string}>, unmapped: array<int, string>}>
     */
    private const array SHEETS = [
        'dogs' => [
            'species' => 'Cão',
            'weight' => 'peso cão',
            'vaccines' => [
                ['last' => 'Último multivalente (Vacina)', 'next' => 'Próx multivalente (Vacina)', 'vaccine' => 'Polivalente Canina (DHPPi)'],
                ['last' => 'Última rábica (Vacina)', 'next' => 'Prox rábica (Vacina)', 'vaccine' => 'Antirrábica (Raiva)'],
            ],
            'unmapped' => [],
        ],
        'cats' => [
            'species' => 'Gato',
            'weight' => 'peso gato',
            'vaccines' => [
                ['last' => 'Última CRP (Vacina', 'next' => 'Próx multivalente (Vacina)', 'vaccine' => 'Tríplice Felina (FVRCP)'],
                ['last' => 'Última FELv (Vacina)', 'next' => 'Prox FELv (Vacina)', 'vaccine' => 'Leucemia Felina (FeLV)'],
            ],
            'unmapped' => ['Último RCPCh (Vacina)', 'Próx RCPCh (Vacina)'],
        ],
    ];

    private const string CLINICAL_WEIGHT_COLUMN = 'peso cão';

    private const string CLINICAL_TEXT_COLUMN = 'Inf Clinica *Separador proprio';

    private const string NOTES_COLUMN = 'Notas';

    /** @var array<int, array{0: string, 1: string}> */
    private array $warnings = [];

    /** @var array{vaccinations: int, treatments: int, clinical_notes: int, skipped: int} */
    private array $counts = ['vaccinations' => 0, 'treatments' => 0, 'clinical_notes' => 0, 'skipped' => 0];

    /**
     * Clinical note lines gathered for each pet id, written once at the end
     * so a pet listed in several sheets gets a single update.
     *
     * @var array<int, array<int, string>>
     */
    private array $clinicalLines = [];

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

        $sheetRows = [];

        foreach (['dogs', 'cats', 'clinical'] as $sheet) {
            if ($this->option($sheet) === null) {
                continue;
            }

            $rows = $this->readCsv($this->option($sheet));

            if ($rows === null) {
                $this->error("File not found: {$this->option($sheet)}");

                return self::FAILURE;
            }

            $sheetRows[$sheet] = $rows;
        }

        if ($sheetRows === []) {
            $this->error('Pass at least one sheet with --dogs, --cats or --clinical.');

            return self::FAILURE;
        }

        $animalSheets = array_intersect_key(self::SHEETS, $sheetRows);
        $vaccines = $this->catalogueVaccines($animalSheets);
        $treatment = Treatment::query()->where('name', self::DEWORMING_TREATMENT)->first();
        $missing = array_keys(array_filter($vaccines, fn (?Vaccine $vaccine): bool => $vaccine === null));

        if ($treatment === null && $animalSheets !== []) {
            $missing[] = 'treatment "'.self::DEWORMING_TREATMENT.'"';
        }

        if ($missing !== []) {
            $this->error('Missing from the catalogue: '.implode(', ', $missing).'. Create them in the admin area first.');

            return self::FAILURE;
        }

        $pets = Pet::query()->where('shelter_id', $shelter->id)->get();

        DB::beginTransaction();

        try {
            foreach ($animalSheets as $sheet => $definition) {
                $this->importAnimalSheet($pets, $definition, $vaccines, $treatment, $sheetRows[$sheet] ?? []);
            }

            $this->collectClinicalSheet($pets, $sheetRows['clinical'] ?? []);
            $this->saveClinicalNotes($pets);

            $this->option('dry-run') ? DB::rollBack() : DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        $this->reportResult();

        return self::SUCCESS;
    }

    /**
     * The catalogue vaccine for each vaccine the given sheets import, keyed
     * by "vaccine name (species)"; null when it is missing. Looked up per
     * species since dogs and cats may each have a vaccine of the same name.
     *
     * @param  array<string, array{species: string, vaccines: array<int, array{vaccine: string}>}>  $sheets
     * @return array<string, Vaccine|null>
     */
    private function catalogueVaccines(array $sheets): array
    {
        $vaccines = [];

        foreach ($sheets as $definition) {
            foreach ($definition['vaccines'] as $column) {
                $vaccines["{$column['vaccine']} ({$definition['species']})"] = Vaccine::query()
                    ->where('name', $column['vaccine'])
                    ->whereHas('species', fn ($query) => $query->where('name', $definition['species']))
                    ->first();
            }
        }

        return $vaccines;
    }

    /**
     * Import one animal sheet: a vaccination per filled-in vaccine column,
     * a deworming round per filled-in Nexgard date, and the weight and
     * notes as clinical note lines.
     *
     * @param  Collection<int, Pet>  $pets
     * @param  array{species: string, weight: string, vaccines: array<int, array{last: string, next: string, vaccine: string}>, unmapped: array<int, string>}  $definition
     * @param  array<string, Vaccine|null>  $vaccines
     * @param  array<int, array<string, string>>  $rows
     */
    private function importAnimalSheet(Collection $pets, array $definition, array $vaccines, Treatment $treatment, array $rows): void
    {
        foreach ($rows as $row) {
            $pet = $this->findPet($pets, $row);

            if ($pet === null) {
                continue;
            }

            $ref = $pet->ref;

            foreach ($definition['vaccines'] as $column) {
                $vaccine = $vaccines["{$column['vaccine']} ({$definition['species']})"];
                $lastDose = $this->pastDate($row, $column['last'], $ref);
                $nextDate = $this->date($row, $column['next'], $ref);

                if ($lastDose !== null && $nextDate !== null && $nextDate <= $lastDose) {
                    $this->addWarning($ref, "{$column['vaccine']}: next date {$nextDate} is not after the last dose {$lastDose}.");
                }

                if ($lastDose !== null || $nextDate !== null) {
                    $this->saveVaccination($pet, $vaccine, $lastDose, $nextDate);
                }
            }

            foreach ($definition['unmapped'] as $column) {
                if ($this->value($row, $column) !== null) {
                    $this->addWarning($ref, "\"{$column}\" has no catalogue vaccine and was not imported.");
                }
            }

            $dewormingDate = $this->pastDate($row, self::DEWORMING_COLUMN, $ref);

            if ($dewormingDate !== null) {
                $this->saveDeworming($pet, $treatment, $dewormingDate);
            }

            $this->addClinicalLine($pet, $this->value($row, $definition['weight']), 'Peso: ');
            $this->addClinicalLine($pet, $this->value($row, self::NOTES_COLUMN), 'Notas: ');
        }
    }

    /**
     * @param  Collection<int, Pet>  $pets
     * @param  array<int, array<string, string>>  $rows
     */
    private function collectClinicalSheet(Collection $pets, array $rows): void
    {
        foreach ($rows as $row) {
            $pet = $this->findPet($pets, $row);

            if ($pet !== null) {
                $this->addClinicalLine($pet, $this->value($row, self::CLINICAL_WEIGHT_COLUMN), 'Peso: ');
                $this->addClinicalLine($pet, $this->value($row, self::CLINICAL_TEXT_COLUMN));
            }
        }
    }

    /**
     * Log a dose (with its next date) or, when only the next date is known,
     * schedule it. A dose already imported for the same date is updated
     * instead, so the import can be re-run, and an open scheduled row is
     * fulfilled by the dose, as VaccinationForm does.
     */
    private function saveVaccination(Pet $pet, Vaccine $vaccine, ?string $lastDose, ?string $nextDate): void
    {
        $records = PetVaccine::query()->where('pet_id', $pet->id)->where('vaccine_id', $vaccine->id);

        $petVaccine = $lastDose !== null
            ? (clone $records)->whereDate('administered_date', $lastDose)->first()
                ?? PetVaccine::scheduledRecordFulfilledBy($pet->id, $vaccine->id, $lastDose)
            : (clone $records)->where('status', 'scheduled')->whereDate('due_date', $nextDate)->first();

        $petVaccine ??= new PetVaccine(['pet_id' => $pet->id, 'vaccine_id' => $vaccine->id]);

        $petVaccine->fill([
            'administered_date' => $lastDose,
            'due_date' => $nextDate,
            'status' => $lastDose !== null ? 'administered' : 'scheduled',
        ])->save();

        $this->counts['vaccinations']++;
    }

    /**
     * Log a Nexgard deworming round, with the next one due after the
     * treatment's frequency, re-using a round already imported for the same
     * date or fulfilling an open scheduled one.
     */
    private function saveDeworming(Pet $pet, Treatment $treatment, string $date): void
    {
        $petTreatment = PetTreatment::query()
            ->where('pet_id', $pet->id)
            ->where('treatment_id', $treatment->id)
            ->whereDate('administered_date', $date)
            ->first()
            ?? PetTreatment::scheduledRecordFulfilledBy($pet->id, $treatment->id, $date)
            ?? new PetTreatment(['pet_id' => $pet->id, 'treatment_id' => $treatment->id]);

        $petTreatment->fill([
            'administered_date' => $date,
            'due_date' => $treatment->frequency_months !== null
                ? Carbon::parse($date)->addMonthsNoOverflow($treatment->frequency_months)->toDateString()
                : null,
            'status' => 'administered',
            'product' => self::DEWORMING_PRODUCT,
        ])->save();

        $this->counts['treatments']++;
    }

    /**
     * Find the row's pet by its "PZ{nº}" ref, then by chip, then (for rows
     * with no number) by a name only one resident pet has. When the chip
     * differs from the numbered pet's, the names must agree, otherwise the
     * number is a typo for another animal and the row is skipped. A pet that
     * has left the shelter is reported.
     *
     * @param  Collection<int, Pet>  $pets
     * @param  array<string, string>  $row
     */
    private function findPet(Collection $pets, array $row): ?Pet
    {
        $number = $this->value($row, self::HEADER_COLUMN);
        $chip = $this->chip($row);
        $name = $this->value($row, 'animal_nome');
        $label = $number !== null ? "PZ{$number}" : (string) $name;

        $pet = $number !== null ? $pets->firstWhere('ref', $label) : null;

        if ($pet !== null && $chip !== null && $pet->chip !== null && $pet->chip !== $chip) {
            if (mb_strtolower(trim($pet->name)) !== mb_strtolower((string) $name)) {
                $this->addWarning($label, "\"{$name}\" (chip {$chip}) doesn't match {$label} {$pet->name} (chip {$pet->chip}); row skipped.");
                $this->counts['skipped']++;

                return null;
            }

            $this->addWarning($label, "Chip {$chip} differs from the pet's chip {$pet->chip}; matched by number and name.");
        }

        $pet ??= $chip !== null ? $pets->firstWhere('chip', $chip) : null;

        if ($pet === null && $number === null && $name !== null) {
            $namesakes = $pets->filter(fn (Pet $pet): bool => mb_strtolower(trim($pet->name)) === mb_strtolower($name)
                && ! in_array($pet->status, ['adopted', 'deceased'], true));

            $pet = $namesakes->count() === 1 ? $namesakes->first() : null;
        }

        if ($pet === null) {
            $this->addWarning($label, "No pet found for \"{$name}\" (chip {$chip}); row skipped.");
            $this->counts['skipped']++;

            return null;
        }

        if (in_array($pet->status, ['adopted', 'deceased'], true)) {
            $this->addWarning($pet->ref, "{$pet->name} is {$pet->status} in Focinhos; imported anyway.");
        }

        return $pet;
    }

    private function addClinicalLine(Pet $pet, ?string $value, string $prefix = ''): void
    {
        if ($value === null) {
            return;
        }

        $line = $prefix.str_replace(["\r\n", "\r"], "\n", $value);

        if (! in_array($line, $this->clinicalLines[$pet->id] ?? [], true)) {
            $this->clinicalLines[$pet->id][] = $line;
        }
    }

    /**
     * Append the gathered lines to each pet's clinical notes, leaving out
     * lines the notes already hold (from an earlier run or typed by staff).
     *
     * @param  Collection<int, Pet>  $pets
     */
    private function saveClinicalNotes(Collection $pets): void
    {
        foreach ($this->clinicalLines as $petId => $lines) {
            $pet = $pets->firstWhere('id', $petId);
            $notes = (string) $pet->clinical_notes;
            $newLines = array_filter($lines, fn (string $line): bool => ! str_contains($notes, $line));

            if ($newLines === []) {
                continue;
            }

            $pet->update(['clinical_notes' => trim($notes."\n\n".implode("\n", $newLines))]);
            $this->counts['clinical_notes']++;
        }
    }

    /**
     * Read a semicolon-separated sheet into rows keyed by column name, with
     * runs of spaces in the header collapsed. Lines before the header and
     * rows with no animal name are skipped. Returns null when the file is
     * missing.
     *
     * @return array<int, array<string, string>>|null
     */
    private function readCsv(string $path): ?array
    {
        $path = str_starts_with($path, '/') ? $path : base_path($path);

        if (! is_file($path)) {
            return null;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            return null;
        }

        $header = null;
        $rows = [];

        while (($cells = fgetcsv($handle, null, ';', '"', '')) !== false) {
            $cells = array_map(fn (?string $cell): string => trim((string) $cell), $cells);

            if ($header === null) {
                $cells[0] = preg_replace('/^\xEF\xBB\xBF/', '', $cells[0]) ?? $cells[0];

                if ($cells[0] === self::HEADER_COLUMN) {
                    $header = array_map(fn (string $cell): string => preg_replace('/\s+/', ' ', $cell) ?? $cell, $cells);
                }

                continue;
            }

            $cells = array_pad(array_slice($cells, 0, count($header)), count($header), '');
            $row = array_combine($header, $cells);

            if (($row['animal_nome'] ?? '') !== '') {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return $rows;
    }

    /**
     * A date cell as Y-m-d, accepting ISO dates and the d/m/Y dates of a
     * Portuguese spreadsheet export; anything else is reported and ignored.
     *
     * @param  array<string, string>  $row
     */
    private function date(array $row, string $column, string $ref): ?string
    {
        $value = $this->value($row, $column);

        if ($value === null) {
            return null;
        }

        $datePart = substr($value, 0, 10);

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            if (Carbon::canBeCreatedFromFormat($datePart, $format)) {
                return Carbon::createFromFormat($format, $datePart)->toDateString();
            }
        }

        $this->addWarning($ref, "\"{$column}\": \"{$value}\" is not a date; ignored.");

        return null;
    }

    /**
     * A date a dose was given: one in the future is a typo, so it is
     * reported and ignored.
     *
     * @param  array<string, string>  $row
     */
    private function pastDate(array $row, string $column, string $ref): ?string
    {
        $date = $this->date($row, $column, $ref);

        if ($date !== null && $date > now()->toDateString()) {
            $this->addWarning($ref, "\"{$column}\": {$date} is in the future; ignored.");

            return null;
        }

        return $date;
    }

    /**
     * The chip number, which a spreadsheet may have turned into a float
     * ("620097800119750.0" or "6.20097800119750E+14").
     *
     * @param  array<string, string>  $row
     */
    private function chip(array $row): ?string
    {
        $chip = $this->value($row, 'animal_chip_id');

        if ($chip === null) {
            return null;
        }

        return is_numeric($chip) && ! ctype_digit($chip) ? number_format((float) $chip, 0, '', '') : $chip;
    }

    /**
     * @param  array<string, string>  $row
     */
    private function value(array $row, string $column): ?string
    {
        $value = $row[$column] ?? '';

        return $value === '' || str_starts_with($value, '=') ? null : $value;
    }

    private function reportResult(): void
    {
        if ($this->warnings !== []) {
            $this->table(['Ref', 'Warning'], $this->warnings);
        }

        $this->info(sprintf(
            '%s%d vaccinations, %d deworming rounds, %d pets with new clinical notes, %d rows skipped, %d warnings.',
            $this->option('dry-run') ? '[Dry run, nothing saved] ' : 'Imported ',
            $this->counts['vaccinations'],
            $this->counts['treatments'],
            $this->counts['clinical_notes'],
            $this->counts['skipped'],
            count($this->warnings),
        ));
    }

    private function addWarning(string $ref, string $message): void
    {
        $this->warnings[] = [$ref, $message];
    }
}

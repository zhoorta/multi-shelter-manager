<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Adoption;
use App\Models\AdoptionApplication;
use App\Models\Cage;
use App\Models\Facility;
use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\Pet;
use App\Models\PetSickness;
use App\Models\PetTreatment;
use App\Models\PetVaccine;
use App\Models\Shelter;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use App\Models\Volunteer;
use App\Models\VolunteerAvailability;
use App\Models\Wing;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use ZipArchive;

/**
 * Builds a ZIP with one CSV per area of a shelter's data (pets, health
 * records, adoptions, sponsorships, members, volunteers, spaces).
 *
 * Every table column is exported (minus audit and technical columns), so new
 * columns show up without touching this class; readable labels for the main
 * relations are added after them. Rows are scoped explicitly by shelter_id
 * so the export never depends on the logged-in user's current shelter.
 */
class ExportShelterData
{
    /**
     * Columns that only matter to the application itself.
     *
     * @var array<int, string>
     */
    private const EXCLUDED_COLUMNS = ['created_by', 'updated_by', 'deleted_by', 'deleted_at', 'ip_address'];

    /**
     * Build the ZIP in a temporary file and return its path; the caller
     * deletes it once it has been sent.
     */
    public function handle(Shelter $shelter): string
    {
        $path = tempnam(sys_get_temp_dir(), 'focinhos-export-');

        $zip = new ZipArchive;

        if ($path === false || $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create the export file.');
        }

        foreach ($this->datasets($shelter->id) as $name => [$query, $labels]) {
            $zip->addFromString($name.'.csv', $this->toCsv($query, $labels));
        }

        $zip->close();

        return $path;
    }

    /**
     * Every file of the export: its name, the shelter's rows and the label
     * columns (header => resolver) appended after the table columns.
     *
     * @return array<string, array{0: Builder<Model>, 1: array<string, Closure(Model): mixed>}>
     */
    private function datasets(int $shelterId): array
    {
        $ownRow = fn (Builder|Relation $query): Builder|Relation => $query->withoutGlobalScope('shelter')->where('shelter_id', $shelterId);
        $eagerPet = fn (Builder|Relation $query): Builder|Relation => $query->withoutGlobalScope('shelter')->withTrashed();

        $petLabels = fn (): array => [
            'pet_ref' => fn (Model $row): mixed => $row->pet?->ref,
            'pet_name' => fn (Model $row): mixed => $row->pet?->name,
        ];

        return [
            'pets' => [
                Pet::query()->withoutGlobalScope('shelter')->where('shelter_id', $shelterId)
                    ->with(['species', 'breed', 'primaryColor', 'secondaryColor', 'furType', 'size', 'cage.wing']),
                [
                    'species' => fn (Pet $pet): mixed => $pet->species?->name,
                    'breed' => fn (Pet $pet): mixed => $pet->breed?->name,
                    'primary_color' => fn (Pet $pet): mixed => $pet->primaryColor?->name,
                    'secondary_color' => fn (Pet $pet): mixed => $pet->secondaryColor?->name,
                    'fur_type' => fn (Pet $pet): mixed => $pet->furType?->name,
                    'size' => fn (Pet $pet): mixed => $pet->size?->name,
                    'wing' => fn (Pet $pet): mixed => $pet->cage?->wing?->name,
                    'cage' => fn (Pet $pet): mixed => $pet->cage?->code,
                ],
            ],
            'vaccinations' => [
                PetVaccine::query()->whereHas('pet', $ownRow)->with(['pet' => $eagerPet, 'vaccine']),
                $petLabels() + ['vaccine' => fn (PetVaccine $row): mixed => $row->vaccine?->name],
            ],
            'treatments' => [
                PetTreatment::query()->whereHas('pet', $ownRow)->with(['pet' => $eagerPet, 'treatment']),
                $petLabels() + ['treatment' => fn (PetTreatment $row): mixed => $row->treatment?->name],
            ],
            'diagnoses' => [
                PetSickness::query()->whereHas('pet', $ownRow)->with(['pet' => $eagerPet, 'sickness']),
                $petLabels() + ['sickness' => fn (PetSickness $row): mixed => $row->sickness?->name],
            ],
            'adoptions' => [
                Adoption::query()->whereHas('pet', $ownRow)->with(['pet' => $eagerPet]),
                $petLabels(),
            ],
            'adoption_applications' => [
                AdoptionApplication::query()->whereHas('pet', $ownRow)->with(['pet' => $eagerPet]),
                $petLabels(),
            ],
            'sponsorships' => [
                Sponsorship::query()->whereHas('pet', $ownRow)->with(['pet' => $eagerPet]),
                $petLabels(),
            ],
            'sponsorship_payments' => [
                SponsorshipPayment::query()->whereHas('sponsorship.pet', $ownRow)->with(['sponsorship.pet' => $eagerPet]),
                [
                    'sponsor_name' => fn (SponsorshipPayment $row): mixed => $row->sponsorship?->name,
                    'pet_ref' => fn (SponsorshipPayment $row): mixed => $row->sponsorship?->pet?->ref,
                    'pet_name' => fn (SponsorshipPayment $row): mixed => $row->sponsorship?->pet?->name,
                ],
            ],
            'members' => [
                Member::query()->withoutGlobalScope('shelter')->where('shelter_id', $shelterId),
                [],
            ],
            'member_payments' => [
                MemberPayment::query()->whereHas('member', $ownRow)->with(['member' => fn (Builder|Relation $query): Builder|Relation => $query->withoutGlobalScope('shelter')]),
                [
                    'member_number' => fn (MemberPayment $row): mixed => $row->member?->member_number,
                    'member_name' => fn (MemberPayment $row): mixed => $row->member?->name,
                ],
            ],
            'volunteers' => [
                Volunteer::query()->withoutGlobalScope('shelter')->where('shelter_id', $shelterId)->with(['activities', 'species']),
                [
                    'activities' => fn (Volunteer $volunteer): mixed => $volunteer->activities->pluck('name')->implode(' | '),
                    'species' => fn (Volunteer $volunteer): mixed => $volunteer->species->pluck('name')->implode(' | '),
                ],
            ],
            'volunteer_availabilities' => [
                VolunteerAvailability::query()->whereHas('volunteer', $ownRow)->with(['volunteer' => fn (Builder|Relation $query): Builder|Relation => $query->withoutGlobalScope('shelter')]),
                ['volunteer_name' => fn (VolunteerAvailability $row): mixed => $row->volunteer?->name],
            ],
            'facilities' => [
                Facility::query()->withoutGlobalScope('shelter')->where('shelter_id', $shelterId),
                [],
            ],
            'wings' => [
                Wing::query()->whereHas('facility', $ownRow)->with(['facility' => fn (Builder|Relation $query): Builder|Relation => $query->withoutGlobalScope('shelter')]),
                ['facility' => fn (Wing $wing): mixed => $wing->facility?->name],
            ],
            'cages' => [
                Cage::query()->whereHas('wing.facility', $ownRow)->with(['wing', 'species', 'volunteer']),
                [
                    'wing' => fn (Cage $cage): mixed => $cage->wing?->name,
                    'species' => fn (Cage $cage): mixed => $cage->species?->name,
                    'volunteer_name' => fn (Cage $cage): mixed => $cage->volunteer?->name,
                ],
            ],
        ];
    }

    /**
     * @param  Builder<Model>  $query
     * @param  array<string, Closure(Model): mixed>  $labels
     */
    private function toCsv(Builder $query, array $labels): string
    {
        $columns = array_values(array_diff(Schema::getColumnListing($query->getModel()->getTable()), self::EXCLUDED_COLUMNS));

        $stream = fopen('php://temp', 'r+');

        // BOM and ';' so Excel (Portuguese settings) opens the accents and columns correctly.
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, [...$columns, ...array_keys($labels)], ';', '"', '');

        $query->chunkById(500, function ($rows) use ($stream, $columns, $labels): void {
            foreach ($rows as $row) {
                $attributes = $row->getAttributes();

                fputcsv($stream, [
                    ...array_map(fn (string $column): string => $this->cell($attributes[$column] ?? null), $columns),
                    ...array_map(fn (Closure $label): string => $this->cell($label($row)), array_values($labels)),
                ], ';', '"', '');
            }
        });

        rewind($stream);

        return (string) stream_get_contents($stream);
    }

    /**
     * Cell text, with a leading apostrophe when a spreadsheet would read it as
     * a formula (typed-in names and notes are not trusted).
     */
    private function cell(mixed $value): string
    {
        $text = (string) ($value ?? '');

        return preg_match('/^([=+@\t\r]|-(?!\d))/', $text) === 1 ? "'".$text : $text;
    }
}

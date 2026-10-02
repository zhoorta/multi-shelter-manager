<?php

declare(strict_types=1);

namespace App\Livewire\Reports\Concerns;

use App\Actions\ExportShelterData;
use App\Models\Adoption;
use App\Models\Pet;
use App\Models\PetVaccine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The yearly lists the municipality and the regional agriculture office ask
 * shelters for (intakes, adoptions, deaths, vaccinations given), downloaded
 * as spreadsheets for the selected report period. Needs dateRange() from
 * BuildsShelterReport.
 */
trait ExportsReportLists
{
    /**
     * Animals that entered the shelter in the period.
     */
    public function exportIntakes(): StreamedResponse
    {
        [$start, $end] = $this->dateRange();

        $pets = $this->listedPets()
            ->whereBetween('checkin_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('checkin_date')
            ->get();

        return $this->downloadList(__('Intakes'), $this->petHeadings(__('Checkin Date')), $pets->map(
            fn (Pet $pet): array => [...$this->petColumns($pet), $pet->checkin_date?->format('d/m/Y'), ...$this->boxColumns($pet)],
        ));
    }

    /**
     * Approved adoptions made in the period.
     */
    public function exportAdoptions(): StreamedResponse
    {
        [$start, $end] = $this->dateRange();

        $adoptions = Adoption::query()
            ->whereIn('pet_id', Pet::query()->select('id')->where('shelter_id', Auth::user()->current_shelter_id))
            ->where('application_status', 'Approved')
            ->whereBetween('adoption_date', [$start->toDateString(), $end->toDateString()])
            ->with(['pet.species', 'pet.cage.wing.facility'])
            ->orderBy('adoption_date')
            ->get();

        return $this->downloadList(
            __('Adoptions'),
            [
                __('Adoption Date'), ...$this->petHeadings(null),
                __('Owner Name'), __('Phone'), __('Email'), __('Address'), __('Postal Code'), __('City'),
                __('Return Date'), __('Transferred in the pet registry'),
            ],
            $adoptions->map(fn (Adoption $adoption): array => [
                $adoption->adoption_date->format('d/m/Y'),
                ...$this->petColumns($adoption->pet),
                $adoption->name, $adoption->phone, $adoption->email, $adoption->address, $adoption->postal_code, $adoption->city,
                $adoption->return_date?->format('d/m/Y'),
                $adoption->siac_transferred_at?->format('d/m/Y'),
            ]),
        );
    }

    /**
     * Animals that died in the period.
     */
    public function exportDeaths(): StreamedResponse
    {
        [$start, $end] = $this->dateRange();

        $pets = $this->listedPets()
            ->whereBetween('date_of_death', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date_of_death')
            ->get();

        return $this->downloadList(__('Deaths'), [...$this->petHeadings(__('Date of Death')), __('Checkin Date')], $pets->map(
            fn (Pet $pet): array => [...$this->petColumns($pet), $pet->date_of_death?->format('d/m/Y'), ...$this->boxColumns($pet), $pet->checkin_date?->format('d/m/Y')],
        ));
    }

    /**
     * Vaccinations given in the period, one row per dose.
     */
    public function exportVaccinations(): StreamedResponse
    {
        abort_unless(Auth::user()->currentShelterHasModule('health_records'), 403);

        [$start, $end] = $this->dateRange();

        $vaccinations = PetVaccine::query()
            ->whereIn('pet_id', Pet::query()->select('id')->where('shelter_id', Auth::user()->current_shelter_id))
            ->where('status', 'administered')
            ->whereBetween('administered_date', [$start->toDateString(), $end->toDateString()])
            ->with(['pet.species', 'vaccine'])
            ->orderBy('administered_date')
            ->get();

        return $this->downloadList(
            __('Vaccinations given'),
            [__('Administered Date'), __('Vaccine'), __('Lot Number'), __('Veterinarian'), ...$this->petHeadings(null)],
            $vaccinations->map(fn (PetVaccine $vaccination): array => [
                $vaccination->administered_date->format('d/m/Y'),
                $vaccination->vaccine->name,
                $vaccination->lot_number,
                $vaccination->veterinarian_name,
                ...$this->petColumns($vaccination->pet),
            ]),
        );
    }

    /**
     * The shelter's pets with what the lists show.
     *
     * @return Builder<Pet>
     */
    private function listedPets(): Builder
    {
        return Pet::query()
            ->where('shelter_id', Auth::user()->current_shelter_id)
            ->with(['species', 'cage.wing.facility']);
    }

    /**
     * @return list<string>
     */
    private function petHeadings(?string $dateHeading): array
    {
        return array_values(array_filter([
            __('Reference'), __('Name'), __('Species'), __('Gender'), __('Microchip / Chip'), __('Birth Date'),
            $dateHeading,
            $dateHeading !== null ? __('Facility') : null,
            $dateHeading !== null ? __('Wing') : null,
            $dateHeading !== null ? __('Cage') : null,
        ], fn (?string $heading): bool => $heading !== null));
    }

    /**
     * @return list<string|null>
     */
    private function petColumns(Pet $pet): array
    {
        return [$pet->ref, $pet->name, $pet->species->name, __(ucfirst($pet->gender)), $pet->chip, $pet->birth_date?->format('d/m/Y')];
    }

    /**
     * @return list<string|null>
     */
    private function boxColumns(Pet $pet): array
    {
        return [$pet->cage?->wing?->facility?->name, $pet->cage?->wing?->name, $pet->cage?->code];
    }

    /**
     * @param  list<string>  $headings
     * @param  iterable<int, array<int, mixed>>  $rows
     */
    private function downloadList(string $title, array $headings, iterable $rows): StreamedResponse
    {
        $csv = ExportShelterData::csv($headings, $rows);
        [$start, $end] = $this->dateRange();

        return response()->streamDownload(
            fn () => print ($csv),
            Str::slug($title.' '.$start->format('Y-m-d').' '.$end->format('Y-m-d')).'.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }
}

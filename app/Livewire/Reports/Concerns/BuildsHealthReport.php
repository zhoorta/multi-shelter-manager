<?php

declare(strict_types=1);

namespace App\Livewire\Reports\Concerns;

use App\Models\Pet;
use App\Models\PetSickness;
use App\Models\PetVaccine;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

/**
 * The Health section of the reports. Sterilisation has no date, so it is
 * only reported for the animals in the shelter today. Needs BuildsShelterReport.
 */
trait BuildsHealthReport
{
    /**
     * @return array{
     *     totals: array{vaccinations: int, overdueVaccinations: int, diagnoses: int, openCases: int, neuteredRate: ?int},
     *     vaccinationBuckets: list<int>,
     *     vaccinationsByVaccine: list<array{label: string, value: int}>,
     *     diagnosesBySickness: list<array{label: string, value: int}>
     * }
     */
    #[Computed]
    public function healthReport(): array
    {
        [$start, $end] = $this->dateRange();
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();
        $petIds = Pet::query()->select('id')->where('shelter_id', Auth::user()->current_shelter_id);
        $bucketFormat = $this->bucketFormat();
        $vaccinationsByBucket = [];

        $vaccinations = PetVaccine::query()
            ->whereIn('pet_id', $petIds)
            ->where('status', 'administered')
            ->whereBetween('administered_date', [$startDate, $endDate])
            ->with('vaccine:id,name')
            ->get(['id', 'pet_id', 'vaccine_id', 'administered_date']);

        foreach ($vaccinations as $vaccination) {
            $bucketKey = $vaccination->administered_date->format($bucketFormat);
            $vaccinationsByBucket[$bucketKey] = ($vaccinationsByBucket[$bucketKey] ?? 0) + 1;
        }

        $diagnoses = PetSickness::query()
            ->join('sicknesses', 'sicknesses.id', '=', 'pet_sicknesses.sickness_id')
            ->whereIn('pet_sicknesses.pet_id', $petIds)
            ->get(['pet_sicknesses.pet_id', 'pet_sicknesses.diagnosed_at', 'pet_sicknesses.status', 'sicknesses.name as sickness_name']);

        $petsInShelter = $this->shelterPets()->filter(fn (Pet $pet): bool => $pet->status !== 'adopted' && $pet->date_of_death === null);
        $isInShelter = array_flip($petsInShelter->modelKeys());

        return [
            'totals' => [
                'vaccinations' => $vaccinations->count(),
                'overdueVaccinations' => PetVaccine::query()
                    ->whereIn('pet_id', $petIds)
                    ->where('status', 'scheduled')
                    ->where('due_date', '<', CarbonImmutable::today())
                    ->count(),
                'diagnoses' => $diagnoses->filter(fn (PetSickness $diagnosis): bool => $this->isBetween($diagnosis->diagnosed_at, $startDate, $endDate))->count(),
                'openCases' => $diagnoses
                    ->filter(fn (PetSickness $diagnosis): bool => in_array($diagnosis->status, ['active', 'chronic'], true) && isset($isInShelter[$diagnosis->pet_id]))
                    ->count(),
                'neuteredRate' => $petsInShelter->isEmpty() ? null : (int) round($petsInShelter->where('is_neutered', true)->count() / $petsInShelter->count() * 100),
            ],
            'vaccinationBuckets' => array_map(fn (array $bucket): int => $vaccinationsByBucket[$bucket['key']] ?? 0, $this->periodBuckets()),
            'vaccinationsByVaccine' => $this->countByLabel($vaccinations->map(fn (PetVaccine $vaccination): string => $vaccination->vaccine->name)->all()),
            'diagnosesBySickness' => $this->countByLabel($diagnoses
                ->filter(fn (PetSickness $diagnosis): bool => $this->isBetween($diagnosis->diagnosed_at, $startDate, $endDate))
                ->map(fn (PetSickness $diagnosis): string => (string) $diagnosis->getAttribute('sickness_name'))
                ->all()),
        ];
    }

    private function isBetween(?\DateTimeInterface $date, string $startDate, string $endDate): bool
    {
        return $date !== null && $date->format('Y-m-d') >= $startDate && $date->format('Y-m-d') <= $endDate;
    }

    /**
     * @param  array<int, string>  $labels
     * @return list<array{label: string, value: int}>
     */
    private function countByLabel(array $labels): array
    {
        $counts = array_count_values($labels);
        arsort($counts);

        return array_map(fn (string $label, int $count): array => ['label' => $label, 'value' => $count], array_keys($counts), $counts);
    }
}

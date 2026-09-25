<?php

declare(strict_types=1);

namespace App\Livewire\Reports\Concerns;

use App\Models\Adoption;
use App\Models\Pet;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * The period selection and every figure of the shelter reports, shared by
 * ShelterReports (the interactive page) and ShelterReportPrint (its
 * printable counterpart, which reads the same period from the query string
 * so the printout matches what was on screen).
 */
trait BuildsShelterReport
{
    /**
     * Ranges longer than this many months are grouped by year instead of by month.
     */
    protected const MAX_MONTHLY_BUCKETS = 24;

    /**
     * 'last_12_months', 'all', 'custom' or a four-digit year.
     */
    #[Url]
    public string $period = 'last_12_months';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    /**
     * @var Collection<int, Pet>|null
     */
    private ?Collection $loadedPets = null;

    /**
     * Every pet of the current shelter with the few columns and approved
     * adoptions the reports need; aggregated in PHP so the numbers are the
     * same on MySQL and SQLite.
     *
     * @return Collection<int, Pet>
     */
    private function shelterPets(): Collection
    {
        return $this->loadedPets ??= Pet::query()
            ->where('shelter_id', Auth::user()->current_shelter_id)
            ->select(['id', 'shelter_id', 'species_id', 'ref', 'name', 'status', 'birth_date', 'checkin_date', 'date_of_death'])
            ->with([
                'species:id,name,name_plural',
                'adoptions' => fn ($query) => $query
                    ->select(['id', 'pet_id', 'adoption_date', 'return_date'])
                    ->where('application_status', 'Approved'),
            ])
            ->get();
    }

    /**
     * Years offered in the period select, newest first.
     *
     * @return list<int>
     */
    #[Computed]
    public function availableYears(): array
    {
        $firstYear = $this->shelterPets()->min(fn (Pet $pet): ?int => $pet->checkin_date?->year) ?? today()->year;

        return range(today()->year, min($firstYear, today()->year));
    }

    /**
     * The first and last day covered by the selected period.
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    public function dateRange(): array
    {
        $today = CarbonImmutable::today();

        if (preg_match('/^\d{4}$/', $this->period) === 1) {
            $year = CarbonImmutable::create((int) $this->period);

            return [$year->startOfYear(), $year->endOfYear()->startOfDay()];
        }

        if ($this->period === 'all') {
            $firstCheckin = $this->shelterPets()->min('checkin_date');

            return [CarbonImmutable::parse($firstCheckin ?? $today)->startOfYear(), $today];
        }

        if ($this->period === 'custom') {
            $from = $this->parseDate($this->from);
            $to = $this->parseDate($this->to);

            if ($from !== null && $to !== null) {
                return $from->lessThanOrEqualTo($to) ? [$from, $to] : [$to, $from];
            }
        }

        return [$today->subMonthsNoOverflow(11)->startOfMonth(), $today];
    }

    /**
     * All the figures shown on the page for the selected period.
     *
     * @return array{
     *     start: CarbonImmutable,
     *     end: CarbonImmutable,
     *     groupedByYear: bool,
     *     buckets: list<array{label: string, fullLabel: string, sublabel: ?string, intakes: int, adoptions: int, deaths: int, population: ?int}>,
     *     totals: array{intakes: int, adoptions: int, returns: int, deaths: int, population: int, medianDaysToAdoption: ?int},
     *     intakesBySpecies: list<array{label: string, value: int}>,
     *     adoptionsBySpecies: list<array{label: string, value: int}>,
     *     adoptionsByAge: list<array{label: string, value: int}>,
     *     medianDaysByAge: list<array{label: string, value: int}>,
     *     longestWaiting: Collection<int, Pet>
     * }
     */
    #[Computed]
    public function report(): array
    {
        [$start, $end] = $this->dateRange();
        $pets = $this->shelterPets();
        $groupedByYear = $start->diffInMonths($end) >= self::MAX_MONTHLY_BUCKETS;
        $bucketFormat = $groupedByYear ? 'Y' : 'Y-m';
        $countsByBucket = ['intakes' => [], 'adoptions' => [], 'deaths' => []];
        $timelines = array_values($pets->map(fn (Pet $pet): array => [
            'checkin' => $pet->checkin_date?->toDateString(),
            'death' => $pet->date_of_death?->toDateString(),
            'adoptions' => array_values($pet->adoptions->map(fn (Adoption $adoption): array => [
                $adoption->adoption_date->toDateString(),
                $adoption->return_date?->toDateString(),
            ])->all()),
        ])->all());

        $isInRange = fn (?\DateTimeInterface $date): bool => $date !== null && $date >= $start && $date <= $end;
        $totals = ['intakes' => 0, 'adoptions' => 0, 'returns' => 0, 'deaths' => 0];
        $intakesBySpecies = [];
        $adoptionsBySpecies = [];
        $daysToAdoptionByAge = array_fill_keys(array_keys($this->ageGroupLabels()), []);
        $adoptionsByAge = array_fill_keys(array_keys($this->ageGroupLabels()), 0);

        foreach ($pets as $pet) {
            if ($isInRange($pet->checkin_date)) {
                $totals['intakes']++;
                $bucketKey = $pet->checkin_date->format($bucketFormat);
                $countsByBucket['intakes'][$bucketKey] = ($countsByBucket['intakes'][$bucketKey] ?? 0) + 1;
                $intakesBySpecies[$pet->species->name_plural] = ($intakesBySpecies[$pet->species->name_plural] ?? 0) + 1;
            }

            if ($isInRange($pet->date_of_death)) {
                $totals['deaths']++;
                $bucketKey = $pet->date_of_death->format($bucketFormat);
                $countsByBucket['deaths'][$bucketKey] = ($countsByBucket['deaths'][$bucketKey] ?? 0) + 1;
            }

            foreach ($pet->adoptions as $adoption) {
                if ($isInRange($adoption->return_date)) {
                    $totals['returns']++;
                }

                if (! $isInRange($adoption->adoption_date)) {
                    continue;
                }

                $totals['adoptions']++;
                $bucketKey = $adoption->adoption_date->format($bucketFormat);
                $countsByBucket['adoptions'][$bucketKey] = ($countsByBucket['adoptions'][$bucketKey] ?? 0) + 1;

                $speciesName = $pet->species->name_plural;
                $adoptionsBySpecies[$speciesName] = ($adoptionsBySpecies[$speciesName] ?? 0) + 1;

                $ageGroup = $this->ageGroupAt($pet, $adoption);
                $adoptionsByAge[$ageGroup]++;

                if ($pet->checkin_date !== null && $pet->checkin_date->lessThanOrEqualTo($adoption->adoption_date)) {
                    $daysToAdoptionByAge[$ageGroup][] = (int) $pet->checkin_date->diffInDays($adoption->adoption_date);
                }
            }
        }

        arsort($intakesBySpecies);
        arsort($adoptionsBySpecies);
        $allDaysToAdoption = array_merge(...array_values($daysToAdoptionByAge));
        $buckets = [];

        for ($bucketStart = $start; $bucketStart->lessThanOrEqualTo($end);) {
            $bucketKey = $bucketStart->format($bucketFormat);
            $bucketEnd = $groupedByYear ? $bucketStart->endOfYear()->startOfDay() : $bucketStart->endOfMonth()->startOfDay();

            $buckets[] = [
                'label' => $groupedByYear ? $bucketStart->format('Y') : $bucketStart->translatedFormat('M'),
                'fullLabel' => $groupedByYear ? $bucketStart->format('Y') : $bucketStart->translatedFormat('M Y'),
                'sublabel' => ! $groupedByYear && ($buckets === [] || $bucketStart->month === 1) ? $bucketStart->format('Y') : null,
                'intakes' => $countsByBucket['intakes'][$bucketKey] ?? 0,
                'adoptions' => $countsByBucket['adoptions'][$bucketKey] ?? 0,
                'deaths' => $countsByBucket['deaths'][$bucketKey] ?? 0,
                'population' => $bucketStart->isFuture() ? null : $this->populationOn($timelines, $bucketEnd->min($end)->min(CarbonImmutable::today())->toDateString()),
            ];

            $bucketStart = $groupedByYear ? $bucketStart->addYear()->startOfYear() : $bucketStart->addMonthNoOverflow()->startOfMonth();
        }

        return [
            'start' => $start,
            'end' => $end,
            'groupedByYear' => $groupedByYear,
            'buckets' => $buckets,
            'totals' => $totals + [
                'population' => (int) (collect($buckets)->whereNotNull('population')->last()['population'] ?? 0),
                'medianDaysToAdoption' => $this->median($allDaysToAdoption),
            ],
            'intakesBySpecies' => array_values(collect($intakesBySpecies)
                ->map(fn (int $count, string $speciesName): array => ['label' => $speciesName, 'value' => $count])
                ->all()),
            'adoptionsBySpecies' => array_values(collect($adoptionsBySpecies)
                ->map(fn (int $count, string $speciesName): array => ['label' => $speciesName, 'value' => $count])
                ->all()),
            'adoptionsByAge' => array_values(collect($adoptionsByAge)
                ->filter()
                ->map(fn (int $count, string $ageGroup): array => ['label' => $this->ageGroupLabels()[$ageGroup], 'value' => $count])
                ->all()),
            'medianDaysByAge' => array_values(collect($daysToAdoptionByAge)
                ->filter()
                ->map(fn (array $days, string $ageGroup): array => ['label' => $this->ageGroupLabels()[$ageGroup], 'value' => (int) $this->median($days)])
                ->all()),
            'longestWaiting' => $pets
                ->filter(fn (Pet $pet): bool => $pet->status === 'available' && $pet->date_of_death === null && $pet->checkin_date !== null)
                ->sortBy('checkin_date')
                ->take(10)
                ->values(),
        ];
    }

    /**
     * How many pets were in the shelter on the given day: checked in, not
     * yet dead and without an adoption that was open on that day. Dates are
     * compared as Y-m-d strings because this runs once per bucket for every
     * pet, and re-casting the date attributes each time is what made it slow.
     *
     * @param  list<array{checkin: ?string, death: ?string, adoptions: list<array{0: string, 1: ?string}>}>  $timelines
     */
    private function populationOn(array $timelines, string $day): int
    {
        $population = 0;

        foreach ($timelines as $timeline) {
            if ($timeline['checkin'] === null || $timeline['checkin'] > $day) {
                continue;
            }

            if ($timeline['death'] !== null && $timeline['death'] <= $day) {
                continue;
            }

            foreach ($timeline['adoptions'] as [$adoptionDate, $returnDate]) {
                if ($adoptionDate <= $day && ($returnDate === null || $returnDate > $day)) {
                    continue 2;
                }
            }

            $population++;
        }

        return $population;
    }

    /**
     * @return array<string, string>
     */
    private function ageGroupLabels(): array
    {
        return [
            'under_1' => __('Under 1 year'),
            '1_to_3' => __('1 to 3 years'),
            '3_to_7' => __('3 to 7 years'),
            'over_7' => __('Over 7 years'),
            'unknown' => __('Unknown age'),
        ];
    }

    private function ageGroupAt(Pet $pet, Adoption $adoption): string
    {
        if ($pet->birth_date === null) {
            return 'unknown';
        }

        $years = $pet->birth_date->diffInYears($adoption->adoption_date);

        return match (true) {
            $years < 1 => 'under_1',
            $years < 3 => '1_to_3',
            $years < 7 => '3_to_7',
            default => 'over_7',
        };
    }

    /**
     * @param  list<int>  $values
     */
    private function median(array $values): ?int
    {
        return $values === [] ? null : (int) round((float) collect($values)->median());
    }

    private function parseDate(string $value): ?CarbonImmutable
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return null;
        }

        $date = CarbonImmutable::createFromFormat('!Y-m-d', $value);

        return $date instanceof CarbonImmutable && $date->format('Y-m-d') === $value ? $date : null;
    }
}

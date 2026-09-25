<?php

declare(strict_types=1);

namespace App\Livewire\Reports\Concerns;

use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\SponsorshipPayment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

/**
 * The Finances section of the reports. Income is counted by payment date
 * (it matches the bank statement); only "active sponsorships" looks at the
 * period a payment covers. Needs BuildsShelterReport for the period.
 */
trait BuildsFinanceReport
{
    /**
     * Sponsorships whose paid period ends within this many days show up in
     * the "ending soon" list.
     */
    protected const SPONSORSHIP_ENDING_DAYS = 30;

    /**
     * @return array{
     *     totals: array{income: float, sponsorships: float, membershipFees: float, joiningFees: float, adoptionFees: float},
     *     incomeBuckets: list<array{sponsorships: float, membershipFees: float, joiningFees: float, adoptionFees: float}>,
     *     activeSponsorshipsBuckets: list<?int>,
     *     sponsorships: array{active: int, sponsoredPets: int, monthlyValue: float},
     *     members: array{active: int, joined: int, inArrears: int, feesExpected: float, feesCollected: float},
     *     paymentsByMethod: list<array{label: string, value: float}>,
     *     sponsorshipsEnding: Collection<int, SponsorshipPayment>
     * }
     */
    #[Computed]
    public function financeReport(): array
    {
        [$start, $end] = $this->dateRange();
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();
        $shelterId = Auth::user()->current_shelter_id;
        $bucketFormat = $this->bucketFormat();
        $buckets = $this->periodBuckets();
        $totals = ['sponsorships' => 0.0, 'membershipFees' => 0.0, 'joiningFees' => 0.0, 'adoptionFees' => 0.0];
        $incomeBySource = ['sponsorships' => [], 'membershipFees' => [], 'joiningFees' => [], 'adoptionFees' => []];

        $sponsorshipPayments = SponsorshipPayment::query()
            ->whereHas('sponsorship.pet', fn (Builder $query) => $query->where('shelter_id', $shelterId))
            ->with('sponsorship:id,pet_id')
            ->get(['id', 'sponsorship_id', 'start_date', 'end_date', 'payment_date', 'payment_value']);

        foreach ($sponsorshipPayments as $payment) {
            $paymentDate = $payment->payment_date->toDateString();

            if ($paymentDate >= $startDate && $paymentDate <= $endDate) {
                $totals['sponsorships'] += (float) $payment->payment_value;
                $bucketKey = $payment->payment_date->format($bucketFormat);
                $incomeBySource['sponsorships'][$bucketKey] = ($incomeBySource['sponsorships'][$bucketKey] ?? 0.0) + (float) $payment->payment_value;
            }
        }

        $memberPayments = MemberPayment::query()
            ->whereHas('member', fn (Builder $query) => $query->where('shelter_id', $shelterId))
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get(['id', 'member_id', 'type', 'payment_date', 'payment_value', 'payment_method']);

        $paymentsByMethod = [];

        foreach ($memberPayments as $payment) {
            $bucketKey = $payment->payment_date->format($bucketFormat);

            if ($payment->type === 'joining_fee') {
                $totals['joiningFees'] += (float) $payment->payment_value;
                $incomeBySource['joiningFees'][$bucketKey] = ($incomeBySource['joiningFees'][$bucketKey] ?? 0.0) + (float) $payment->payment_value;
            } else {
                $totals['membershipFees'] += (float) $payment->payment_value;
                $incomeBySource['membershipFees'][$bucketKey] = ($incomeBySource['membershipFees'][$bucketKey] ?? 0.0) + (float) $payment->payment_value;
            }

            $method = $payment->payment_method ?? 'other';
            $paymentsByMethod[$method] = ($paymentsByMethod[$method] ?? 0) + (float) $payment->payment_value;
        }

        foreach ($this->shelterPets() as $pet) {
            foreach ($pet->adoptions as $adoption) {
                $adoptionDate = $adoption->adoption_date->toDateString();

                if ((float) $adoption->adoption_fee > 0 && $adoptionDate >= $startDate && $adoptionDate <= $endDate) {
                    $totals['adoptionFees'] += (float) $adoption->adoption_fee;
                    $bucketKey = $adoption->adoption_date->format($bucketFormat);
                    $incomeBySource['adoptionFees'][$bucketKey] = ($incomeBySource['adoptionFees'][$bucketKey] ?? 0.0) + (float) $adoption->adoption_fee;
                }
            }
        }

        arsort($paymentsByMethod);
        $lastDay = $end->min(CarbonImmutable::today());
        $coveringPayments = $this->paymentsCovering($sponsorshipPayments, $lastDay->toDateString());

        return [
            'totals' => ['income' => round(array_sum($totals), 2)] + array_map(fn (float $amount): float => round($amount, 2), $totals),
            'incomeBuckets' => array_map(fn (array $bucket): array => [
                'sponsorships' => round($incomeBySource['sponsorships'][$bucket['key']] ?? 0.0, 2),
                'membershipFees' => round($incomeBySource['membershipFees'][$bucket['key']] ?? 0.0, 2),
                'joiningFees' => round($incomeBySource['joiningFees'][$bucket['key']] ?? 0.0, 2),
                'adoptionFees' => round($incomeBySource['adoptionFees'][$bucket['key']] ?? 0.0, 2),
            ], $buckets),
            'activeSponsorshipsBuckets' => array_map(
                fn (array $bucket): ?int => $bucket['start']->isFuture()
                    ? null
                    : $this->paymentsCovering($sponsorshipPayments, $bucket['end']->min(CarbonImmutable::today())->toDateString())->unique('sponsorship_id')->count(),
                $buckets,
            ),
            'sponsorships' => [
                'active' => $coveringPayments->unique('sponsorship_id')->count(),
                'sponsoredPets' => $coveringPayments->unique(fn (SponsorshipPayment $payment): int => $payment->sponsorship->pet_id)->count(),
                'monthlyValue' => round((float) $coveringPayments->sum(fn (SponsorshipPayment $payment): float => $this->monthlyValue($payment)), 2),
            ],
            'members' => $this->memberFigures($shelterId, $start, $end, $totals['membershipFees']),
            'paymentsByMethod' => array_values(collect($paymentsByMethod)
                ->map(fn (float $value, string $method): array => ['label' => __('payment_'.$method), 'value' => round($value, 2)])
                ->all()),
            'sponsorshipsEnding' => $this->sponsorshipsEnding($sponsorshipPayments),
        ];
    }

    /**
     * Payments whose paid period includes the given Y-m-d day.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, SponsorshipPayment>  $payments
     * @return \Illuminate\Database\Eloquent\Collection<int, SponsorshipPayment>
     */
    private function paymentsCovering(\Illuminate\Database\Eloquent\Collection $payments, string $day): \Illuminate\Database\Eloquent\Collection
    {
        return $payments->filter(
            fn (SponsorshipPayment $payment): bool => $payment->start_date->toDateString() <= $day && $payment->end_date->toDateString() >= $day,
        );
    }

    /**
     * A payment's value spread over the months it covers.
     */
    private function monthlyValue(SponsorshipPayment $payment): float
    {
        $months = max(1, (int) round($payment->start_date->diffInMonths($payment->end_date->copy()->addDay())));

        return (float) $payment->payment_value / $months;
    }

    /**
     * The latest payment of every sponsorship whose paid period ends in the
     * next days, soonest first; sponsorships already renewed are left out.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, SponsorshipPayment>  $payments
     * @return Collection<int, SponsorshipPayment>
     */
    private function sponsorshipsEnding(\Illuminate\Database\Eloquent\Collection $payments): Collection
    {
        $today = CarbonImmutable::today();
        $limit = $today->addDays(self::SPONSORSHIP_ENDING_DAYS);

        $latestPayments = $payments
            ->groupBy('sponsorship_id')
            ->map(fn (\Illuminate\Database\Eloquent\Collection $sponsorshipPayments): SponsorshipPayment => $sponsorshipPayments->sortByDesc('end_date')->first())
            ->filter(fn (SponsorshipPayment $payment): bool => $payment->end_date->betweenIncluded($today, $limit))
            ->sortBy('end_date')
            ->values();

        $latestPayments->load(['sponsorship:id,pet_id,name', 'sponsorship.pet:id,shelter_id,name,ref,species_id', 'sponsorship.pet.species:id,name']);

        return $latestPayments;
    }

    /**
     * @return array{active: int, joined: int, inArrears: int, feesExpected: float, feesCollected: float}
     */
    private function memberFigures(int $shelterId, CarbonImmutable $start, CarbonImmutable $end, float $feesCollected): array
    {
        $members = Member::query()
            ->where('shelter_id', $shelterId)
            ->get(['id', 'shelter_id', 'status', 'join_date', 'membership_fee', 'membership_fee_frequency']);

        $activeMembers = $members->filter(fn (Member $member): bool => $member->status === 'active' && $member->join_date->lessThanOrEqualTo($end));

        return [
            'active' => $activeMembers->count(),
            'joined' => $members->filter(fn (Member $member): bool => $member->join_date->betweenIncluded($start, $end))->count(),
            'inArrears' => Member::query()->where('shelter_id', $shelterId)->inArrears()->count(),
            'feesExpected' => round((float) $activeMembers->sum(fn (Member $member): float => $this->expectedFees($member, $start, $end)), 2),
            'feesCollected' => round($feesCollected, 2),
        ];
    }

    /**
     * The membership fees an active member should pay in the period: the fee
     * spread over the months of its frequency, for the months since joining.
     */
    private function expectedFees(Member $member, CarbonImmutable $start, CarbonImmutable $end): float
    {
        $from = $start->max(CarbonImmutable::parse($member->join_date));

        if ((float) $member->membership_fee <= 0 || $from->greaterThan($end)) {
            return 0.0;
        }

        $monthsPerFee = match ($member->membership_fee_frequency) {
            'monthly' => 1,
            'quarterly' => 3,
            'semiannual' => 6,
            default => 12,
        };
        $months = $from->diffInMonths($end->addDay());

        return (float) $member->membership_fee / $monthsPerFee * $months;
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\MultiShelterTrait;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A member (sócio) of the shelter's association, who pays a one-time joining
 * fee (joia, possibly 0) and a recurring membership fee (quota).
 *
 * @property int $id
 * @property int $shelter_id
 * @property int|null $volunteer_id
 * @property int $member_number
 * @property string $name
 * @property string|null $tin
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $city
 * @property Carbon $join_date
 * @property string $status
 * @property string $joining_fee
 * @property string $membership_fee
 * @property string $membership_fee_frequency
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'shelter_id', 'volunteer_id', 'member_number', 'name', 'tin', 'email', 'phone', 'address', 'postal_code',
    'city', 'join_date', 'status', 'joining_fee', 'membership_fee', 'membership_fee_frequency', 'notes',
])]
class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use Blameable, HasFactory, MultiShelterTrait, SoftDeletes;

    /**
     * Number new members sequentially within their shelter. Trashed members
     * are counted too, so a number is never reused.
     */
    protected static function booted(): void
    {
        static::creating(function (Member $member): void {
            if ($member->member_number) {
                return;
            }

            $lastNumber = self::query()
                ->withoutGlobalScope('shelter')
                ->withTrashed()
                ->where('shelter_id', $member->shelter_id)
                ->max('member_number');

            $member->member_number = (int) $lastNumber + 1;
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'joining_fee' => 'decimal:2',
            'membership_fee' => 'decimal:2',
        ];
    }

    /**
     * Get the shelter the member belongs to.
     *
     * @return BelongsTo<Shelter, $this>
     */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /**
     * Get the volunteer record of the same person, if any.
     *
     * @return BelongsTo<Volunteer, $this>
     */
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    /**
     * Get the payments (joining fee and membership fees) made by the member.
     *
     * @return HasMany<MemberPayment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(MemberPayment::class);
    }

    /**
     * Active members who owe the joining fee or have no membership fee
     * payment covering today. The query counterpart of isInArrears().
     *
     * @param  Builder<Member>  $query
     */
    #[Scope]
    protected function inArrears(Builder $query): void
    {
        $query->where('status', 'active')
            ->where(fn (Builder $query) => $query
                ->where(fn (Builder $query) => $query
                    ->where('joining_fee', '>', 0)
                    ->whereDoesntHave('payments', fn (Builder $query) => $query->where('type', 'joining_fee')))
                ->orWhere(fn (Builder $query) => $query
                    ->where('membership_fee', '>', 0)
                    ->whereDoesntHave('payments', fn (Builder $query) => $query
                        ->where('type', 'membership_fee')
                        ->whereDate('end_date', '>=', today()))));
    }

    /**
     * Whether the member still owes the joining fee. A joining fee of 0 is
     * never owed.
     */
    public function owesJoiningFee(): bool
    {
        return (float) $this->joining_fee > 0
            && ! $this->payments()->where('type', 'joining_fee')->exists();
    }

    /**
     * The last day covered by the member's membership fee payments.
     */
    public function feesPaidUntil(): ?Carbon
    {
        $endDate = $this->payments()->where('type', 'membership_fee')->max('end_date');

        return $endDate ? Carbon::parse($endDate) : null;
    }

    /**
     * Whether an active member owes the joining fee or has no membership fee
     * payment covering today.
     */
    public function isInArrears(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->owesJoiningFee()) {
            return true;
        }

        if ((float) $this->membership_fee <= 0) {
            return false;
        }

        return $this->feesPaidUntil()?->isBefore(today()) ?? true;
    }

    /**
     * The next membership fee period to pay: from the day after the last paid
     * period (or the join date) for one month, quarter or year.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function nextFeePeriod(): array
    {
        $start = $this->feesPaidUntil()?->addDay() ?? $this->join_date->copy();

        $months = match ($this->membership_fee_frequency) {
            'monthly' => 1,
            'quarterly' => 3,
            default => 12,
        };

        return [$start, $start->copy()->addMonthsNoOverflow($months)->subDay()];
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\MemberPaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A member's payment: either the one-time joining fee (joia) or a membership
 * fee (quota) covering start_date to end_date.
 *
 * @property int $id
 * @property int $member_id
 * @property string $type
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property Carbon $payment_date
 * @property string $payment_value
 * @property string|null $payment_method
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'member_id', 'type', 'start_date', 'end_date', 'payment_date', 'payment_value', 'payment_method', 'notes',
])]
class MemberPayment extends Model
{
    /** @use HasFactory<MemberPaymentFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'payment_date' => 'date',
            'payment_value' => 'decimal:2',
        ];
    }

    /**
     * Get the member this payment belongs to.
     *
     * @return BelongsTo<Member, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}

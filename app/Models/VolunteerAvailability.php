<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\VolunteerAvailabilityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $volunteer_id
 * @property int $day_index
 * @property string $frequency
 * @property bool $mornings
 * @property bool $afternoons
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['volunteer_id', 'day_index', 'frequency', 'mornings', 'afternoons'])]
class VolunteerAvailability extends Model
{
    /** @use HasFactory<VolunteerAvailabilityFactory> */
    use HasFactory;

    /**
     * Day-of-week labels for volunteer availability, indexed 0 (Monday) through 6 (Sunday).
     *
     * @var array<int, string>
     */
    public const DAYS = [
        0 => 'Monday',
        1 => 'Tuesday',
        2 => 'Wednesday',
        3 => 'Thursday',
        4 => 'Friday',
        5 => 'Saturday',
        6 => 'Sunday',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mornings' => 'boolean',
            'afternoons' => 'boolean',
        ];
    }

    /**
     * Get the volunteer this availability belongs to.
     *
     * @return BelongsTo<Volunteer, $this>
     */
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }
}

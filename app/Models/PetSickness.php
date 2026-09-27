<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pet_id
 * @property int $sickness_id
 * @property Carbon $diagnosed_at
 * @property string $status
 * @property Carbon|null $resolved_at
 * @property string|null $treatment_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
class PetSickness extends Pivot
{
    use Blameable, SoftDeletes;

    protected $table = 'pet_sicknesses';

    public const STATUSES = ['active', 'chronic', 'treated'];

    /** Statuses of a diagnosis that still needs care. */
    public const OPEN_STATUSES = ['active', 'chronic'];

    public $incrementing = true;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'diagnosed_at' => 'date',
            'resolved_at' => 'date',
        ];
    }

    /**
     * Translated label for a diagnosis status.
     */
    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'active' => __('Active'),
            'chronic' => __('Chronic'),
            'treated' => __('Treated'),
            default => $status,
        };
    }

    /**
     * Badge color for a diagnosis status: open cases stand out, treated
     * ones fade.
     */
    public static function statusColor(string $status): string
    {
        return match ($status) {
            'active' => 'red',
            'chronic' => 'amber',
            default => 'zinc',
        };
    }

    /**
     * Get the pet this diagnosis belongs to.
     *
     * @return BelongsTo<Pet, $this>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the sickness diagnosed.
     *
     * @return BelongsTo<Sickness, $this>
     */
    public function sickness(): BelongsTo
    {
        return $this->belongsTo(Sickness::class);
    }
}

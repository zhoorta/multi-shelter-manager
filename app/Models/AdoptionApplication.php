<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\AdoptionApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * An adoption request sent by the public through the portal. Kept apart from
 * Adoption on purpose: a pending application must never change the pet's
 * status (see Pet::determineStatus()).
 *
 * @property int $id
 * @property int $pet_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string|null $postal_code
 * @property string $city
 * @property string $housing_type
 * @property bool $has_garden
 * @property bool $has_children
 * @property string|null $other_animals
 * @property string $message
 * @property string $status
 * @property Carbon $consent_at
 * @property string|null $ip_address
 * @property int|null $adoption_id
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'pet_id', 'name', 'email', 'phone', 'postal_code', 'city', 'housing_type', 'has_garden',
    'has_children', 'other_animals', 'message', 'status', 'consent_at', 'ip_address',
    'adoption_id', 'reviewed_by', 'reviewed_at',
])]
class AdoptionApplication extends Model
{
    /** @use HasFactory<AdoptionApplicationFactory> */
    use Blameable, HasFactory, MassPrunable, SoftDeletes;

    /**
     * Months an application is kept after its last change, as stated in the
     * privacy policy.
     */
    public const RETENTION_MONTHS = 6;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_garden' => 'boolean',
            'has_children' => 'boolean',
            'consent_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Applications past the retention period, deleted for good by the
     * scheduled model:prune command.
     *
     * @return Builder<self>
     */
    public function prunable(): Builder
    {
        return self::withTrashed()->where('updated_at', '<=', now()->subMonths(self::RETENTION_MONTHS));
    }

    /**
     * Get the pet the application is for.
     *
     * @return BelongsTo<Pet, $this>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the adoption registered when the application was approved.
     *
     * @return BelongsTo<Adoption, $this>
     */
    public function adoption(): BelongsTo
    {
        return $this->belongsTo(Adoption::class);
    }

    /**
     * Get the user who approved or rejected the application.
     *
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by')->withTrashed();
    }
}

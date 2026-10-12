<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\MultiShelterTrait;
use Database\Factories\ColonyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A colony of free-roaming animals (usually cats) looked after under a CED/CER programme.
 *
 * @property int $id
 * @property int $shelter_id
 * @property int $species_id
 * @property string $name
 * @property string|null $location
 * @property string|null $city
 * @property string|null $caretaker_name
 * @property string|null $caretaker_phone
 * @property string|null $notes
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'shelter_id', 'species_id', 'name', 'location', 'city', 'caretaker_name', 'caretaker_phone', 'notes', 'is_active',
])]
class Colony extends Model
{
    /** @use HasFactory<ColonyFactory> */
    use Blameable, HasFactory, MultiShelterTrait, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Shelter, $this>
     */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /**
     * The species living in this colony (soft-deleted species still resolve).
     *
     * @return BelongsTo<Species, $this>
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class)->withTrashed();
    }

    /**
     * Animals registered in this colony, including those already deceased.
     *
     * @return HasMany<Pet, $this>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}

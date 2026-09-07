<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\BreedFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $species_id
 * @property string $name
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['species_id', 'name', 'is_default'])]
class Breed extends Model
{
    /** @use HasFactory<BreedFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the species the breed belongs to.
     *
     * @return BelongsTo<Species, $this>
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * Get the pets belonging to the breed.
     *
     * @return HasMany<Pet, $this>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}

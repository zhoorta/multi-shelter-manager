<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\SpeciesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $name_plural
 * @property bool $has_pure_breed_field
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'name_plural', 'has_pure_breed_field'])]
class Species extends Model
{
    /** @use HasFactory<SpeciesFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_pure_breed_field' => 'boolean',
        ];
    }

    /**
     * Get the breeds belonging to the species.
     *
     * @return HasMany<Breed, $this>
     */
    public function breeds(): HasMany
    {
        return $this->hasMany(Breed::class);
    }

    /**
     * Get the pets belonging to the species.
     *
     * @return HasMany<Pet, $this>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}

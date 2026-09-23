<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\SpeciesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $name_plural
 * @property bool $has_pure_breed_field
 * @property-read string $emoji
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
     * An emoji for the species, guessed from its (pt or en) name, used as a
     * placeholder on the public portal when a pet has no photo.
     *
     * @return Attribute<string, never>
     */
    protected function emoji(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $name = Str::lower($this->name);

                return match (true) {
                    Str::contains($name, ['cão', 'cao', 'dog', 'canin']) => '🐶',
                    Str::contains($name, ['gat', 'cat', 'felin']) => '🐱',
                    Str::contains($name, ['coelh', 'rabbit']) => '🐰',
                    Str::contains($name, ['ave', 'bird']) => '🐦',
                    default => '🐾',
                };
            },
        );
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

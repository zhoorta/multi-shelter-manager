<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ShelterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property string $city
 * @property string|null $logo_path
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'short_name', 'city', 'logo_path', 'address', 'postal_code', 'phone', 'email', 'website', 'description'])]
class Shelter extends Model
{
    /** @use HasFactory<ShelterFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the users belonging to the shelter.
     *
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the facilities belonging to the shelter.
     *
     * @return HasMany<Facility, $this>
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * Get the pets belonging to the shelter.
     *
     * @return HasMany<Pet, $this>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * Get the species enabled for the shelter. Controls which species
     * appear on the sidebar for the shelter's manager and staff users.
     *
     * @return BelongsToMany<Species, $this>
     */
    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'shelter_species')->withTimestamps();
    }
}

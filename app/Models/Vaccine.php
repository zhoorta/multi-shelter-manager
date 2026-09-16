<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\VaccineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name'])]
class Vaccine extends Model
{
    /** @use HasFactory<VaccineFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the pets administered the vaccine.
     *
     * @return BelongsToMany<Pet, $this, PetVaccine>
     */
    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(Pet::class, 'pet_vaccines')
            ->using(PetVaccine::class)
            ->withPivot(['id', 'administered_date', 'due_date', 'status', 'lot_number', 'veterinarian_name', 'notes', 'created_by', 'updated_by'])
            ->wherePivotNull('deleted_at')
            ->withTimestamps();
    }

    /**
     * Get the species this vaccine can be administered to.
     *
     * @return BelongsToMany<Species, $this>
     */
    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'vaccine_species')->withTimestamps();
    }
}

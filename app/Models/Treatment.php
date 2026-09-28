<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\TreatmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Global catalogue of recurring preventive treatments (internal/external
 * deworming, antiparasitic collars), kept apart from vaccines so vaccine
 * reports and reminders stay vaccine-only.
 *
 * @property int $id
 * @property string $name
 * @property int|null $frequency_months
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'frequency_months'])]
class Treatment extends Model
{
    /** @use HasFactory<TreatmentFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'frequency_months' => 'integer',
        ];
    }

    /**
     * Get the treatment records of this treatment.
     *
     * @return HasMany<PetTreatment, $this>
     */
    public function petTreatments(): HasMany
    {
        return $this->hasMany(PetTreatment::class);
    }

    /**
     * Get the species this treatment can be given to.
     *
     * @return BelongsToMany<Species, $this>
     */
    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'treatment_species')->withTimestamps();
    }
}

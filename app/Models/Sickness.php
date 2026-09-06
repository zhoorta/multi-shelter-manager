<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\SicknessFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'description'])]
class Sickness extends Model
{
    /** @use HasFactory<SicknessFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the pets diagnosed with the sickness.
     *
     * @return BelongsToMany<Pet, $this, PetSickness>
     */
    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(Pet::class, 'pet_sickness')
            ->using(PetSickness::class)
            ->withPivot(['diagnosed_at', 'status', 'treatment_notes', 'created_by', 'updated_by'])
            ->withTimestamps();
    }
}

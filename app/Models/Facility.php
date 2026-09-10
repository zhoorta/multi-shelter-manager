<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\MultiShelterTrait;
use Database\Factories\FacilityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $shelter_id
 * @property string|null $name
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $city
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['shelter_id', 'name', 'address', 'postal_code', 'city', 'notes'])]
class Facility extends Model
{
    /** @use HasFactory<FacilityFactory> */
    use Blameable, HasFactory, MultiShelterTrait, SoftDeletes;

    /**
     * Get the shelter the facility belongs to.
     *
     * @return BelongsTo<Shelter, $this>
     */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /**
     * Get the wings belonging to the facility.
     *
     * @return HasMany<Wing, $this>
     */
    public function wings(): HasMany
    {
        return $this->hasMany(Wing::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\CageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $wing_id
 * @property int|null $species_id
 * @property int|null $volunteer_id
 * @property string $code
 * @property int $capacity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['wing_id', 'species_id', 'volunteer_id', 'code', 'capacity'])]
class Cage extends Model
{
    /** @use HasFactory<CageFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the wing the cage belongs to.
     *
     * @return BelongsTo<Wing, $this>
     */
    public function wing(): BelongsTo
    {
        return $this->belongsTo(Wing::class);
    }

    /**
     * Get the species the cage is destined to. Null means any species.
     * Uses withTrashed() so a soft-deleted species still resolves.
     *
     * @return BelongsTo<Species, $this>
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class)->withTrashed();
    }

    /**
     * Get the volunteer who is the contact of a foster family cage (in a
     * foster wing, each cage is one family).
     *
     * @return BelongsTo<Volunteer, $this>
     */
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    /**
     * Limit to cages that can house the given species: those destined to it
     * and those with no species assigned.
     *
     * @param  Builder<Cage>  $query
     */
    #[Scope]
    protected function accepting(Builder $query, int $speciesId): void
    {
        $query->where(fn (Builder $query) => $query->where('species_id', $speciesId)->orWhereNull('species_id'));
    }

    /**
     * Get the pets housed in the cage.
     *
     * @return HasMany<Pet, $this>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}

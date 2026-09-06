<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\CageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $wing_id
 * @property string $code
 * @property int $capacity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['wing_id', 'code', 'capacity'])]
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
     * Get the pets housed in the cage.
     *
     * @return HasMany<Pet, $this>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}

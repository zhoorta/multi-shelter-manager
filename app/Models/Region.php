<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * An administrative region of a country (a "distrito" in Portugal).
 *
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
class Region extends Model
{
    /** @use HasFactory<RegionFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the shelters located in the region.
     *
     * @return HasMany<Shelter, $this>
     */
    public function shelters(): HasMany
    {
        return $this->hasMany(Shelter::class);
    }
}

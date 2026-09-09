<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\AdoptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pet_id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $city
 * @property Carbon $adoption_date
 * @property Carbon|null $return_date
 * @property string $adoption_fee
 * @property string|null $notes
 * @property string $application_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'pet_id', 'name', 'email', 'phone', 'address', 'postal_code', 'city',
    'adoption_date', 'return_date', 'adoption_fee', 'notes', 'application_status',
])]
class Adoption extends Model
{
    /** @use HasFactory<AdoptionFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'adoption_date' => 'date',
            'return_date' => 'date',
            'adoption_fee' => 'decimal:2',
        ];
    }

    /**
     * Get the pet that was adopted.
     *
     * @return BelongsTo<Pet, $this>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $shelter_id
 * @property int|null $cage_id
 * @property int $species_id
 * @property int $breed_id
 * @property int|null $primary_color_id
 * @property int|null $secondary_color_id
 * @property int|null $fur_type_id
 * @property string $name
 * @property string|null $chip
 * @property bool $is_neutered
 * @property string $gender
 * @property Carbon|null $birth_date
 * @property string $status
 * @property string|null $notes
 * @property string|null $description
 * @property bool $is_adoptable
 * @property bool $is_sponsorable
 * @property bool $publish_to_portal
 * @property bool $is_featured
 * @property Carbon|null $admission_date
 * @property Carbon|null $departure_date
 * @property Carbon|null $date_of_death
 * @property string|null $age
 * @property string|null $internal_notes
 * @property int $view_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'shelter_id', 'cage_id', 'species_id', 'breed_id', 'primary_color_id', 'secondary_color_id',
    'fur_type_id', 'name', 'chip', 'is_neutered', 'gender', 'birth_date', 'status', 'notes',
    'description', 'is_adoptable', 'is_sponsorable', 'publish_to_portal', 'is_featured',
    'admission_date', 'departure_date', 'date_of_death', 'age', 'internal_notes',
])]
class Pet extends Model
{
    /** @use HasFactory<PetFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the shelter the pet belongs to.
     *
     * @return BelongsTo<Shelter, $this>
     */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_neutered' => 'boolean',
            'birth_date' => 'date',
            'is_adoptable' => 'boolean',
            'is_sponsorable' => 'boolean',
            'publish_to_portal' => 'boolean',
            'is_featured' => 'boolean',
            'admission_date' => 'date',
            'departure_date' => 'date',
            'date_of_death' => 'date',
        ];
    }
}

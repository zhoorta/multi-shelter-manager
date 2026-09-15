<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\MultiShelterTrait;
use Carbon\CarbonInterface;
use Database\Factories\PetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $shelter_id
 * @property int|null $cage_id
 * @property int $species_id
 * @property int $breed_id
 * @property bool $is_pure_breed
 * @property int|null $primary_color_id
 * @property int|null $secondary_color_id
 * @property int|null $fur_type_id
 * @property int|null $size_id
 * @property string $ref
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
 * @property Carbon|null $checkin_date
 * @property Carbon|null $checkout_date
 * @property Carbon|null $date_of_death
 * @property string|null $age
 * @property string|null $internal_notes
 * @property int $view_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'shelter_id', 'cage_id', 'species_id', 'breed_id', 'is_pure_breed', 'primary_color_id', 'secondary_color_id',
    'fur_type_id', 'size_id', 'ref', 'name', 'chip', 'is_neutered', 'gender', 'birth_date', 'status', 'notes',
    'description', 'is_adoptable', 'is_sponsorable', 'publish_to_portal', 'is_featured',
    'checkin_date', 'checkout_date', 'date_of_death', 'age', 'internal_notes',
])]
class Pet extends Model
{
    /** @use HasFactory<PetFactory> */
    use Blameable, HasFactory, MultiShelterTrait, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pure_breed' => 'boolean',
            'is_neutered' => 'boolean',
            'birth_date' => 'date',
            'is_adoptable' => 'boolean',
            'is_sponsorable' => 'boolean',
            'publish_to_portal' => 'boolean',
            'is_featured' => 'boolean',
            'checkin_date' => 'date',
            'checkout_date' => 'date',
            'date_of_death' => 'date',
        ];
    }

    /**
     * The pet's current age, in whole years and months, as a translated
     * string (e.g. "1 ano e 3 meses"). Null when birth_date is unknown.
     */
    protected function ageInWords(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->birth_date === null ? null : self::periodInWords($this->birth_date),
        );
    }

    /**
     * How long the pet has been at the shelter, in whole years and months,
     * as a translated string. Null when checkin_date is unknown.
     */
    protected function timeInCaptivity(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->checkin_date === null ? null : self::periodInWords($this->checkin_date),
        );
    }

    /**
     * The elapsed time between $from and now, in whole years and months,
     * as a translated string (e.g. "1 ano e 3 meses").
     */
    private static function periodInWords(CarbonInterface $from): string
    {
        $diff = $from->diff(now());

        $parts = [];

        if ($diff->y > 0) {
            $parts[] = trans_choice(':count year|:count years', $diff->y, ['count' => $diff->y]);
        }

        if ($diff->m > 0) {
            $parts[] = trans_choice(':count month|:count months', $diff->m, ['count' => $diff->m]);
        }

        if ($parts === []) {
            $parts[] = trans_choice(':count month|:count months', 0, ['count' => 0]);
        }

        return implode(' '.__('and').' ', $parts);
    }

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
     * Get the cage the pet is housed in.
     *
     * @return BelongsTo<Cage, $this>
     */
    public function cage(): BelongsTo
    {
        return $this->belongsTo(Cage::class);
    }

    /**
     * Get the pet's species.
     *
     * @return BelongsTo<Species, $this>
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * Get the pet's breed.
     *
     * @return BelongsTo<Breed, $this>
     */
    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    /**
     * Get the pet's primary color.
     *
     * @return BelongsTo<Color, $this>
     */
    public function primaryColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'primary_color_id');
    }

    /**
     * Get the pet's secondary color.
     *
     * @return BelongsTo<Color, $this>
     */
    public function secondaryColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'secondary_color_id');
    }

    /**
     * Get the pet's fur type.
     *
     * @return BelongsTo<FurType, $this>
     */
    public function furType(): BelongsTo
    {
        return $this->belongsTo(FurType::class);
    }

    /**
     * Get the pet's size.
     *
     * @return BelongsTo<Size, $this>
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Get the images belonging to the pet.
     *
     * @return HasMany<PetImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(PetImage::class);
    }

    /**
     * Get the sicknesses diagnosed for the pet.
     *
     * @return BelongsToMany<Sickness, $this, PetSickness>
     */
    public function sicknesses(): BelongsToMany
    {
        return $this->belongsToMany(Sickness::class, 'pet_sicknesses')
            ->using(PetSickness::class)
            ->withPivot(['diagnosed_at', 'status', 'treatment_notes', 'created_by', 'updated_by'])
            ->withTimestamps();
    }

    /**
     * Get the vaccines administered to the pet.
     *
     * @return BelongsToMany<Vaccine, $this, PetVaccine>
     */
    public function vaccines(): BelongsToMany
    {
        return $this->belongsToMany(Vaccine::class, 'pet_vaccines')
            ->using(PetVaccine::class)
            ->withPivot(['id', 'administered_at', 'next_due_at', 'lot_number', 'veterinarian_name', 'notes', 'created_by', 'updated_by'])
            ->wherePivotNull('deleted_at')
            ->withTimestamps();
    }

    /**
     * Get the adoption records for the pet.
     *
     * @return HasMany<Adoption, $this>
     */
    public function adoptions(): HasMany
    {
        return $this->hasMany(Adoption::class);
    }

    /**
     * Get the pet's most recent adoption record, by adoption date.
     *
     * @return HasOne<Adoption, $this>
     */
    public function latestAdoption(): HasOne
    {
        return $this->hasOne(Adoption::class)->latestOfMany('adoption_date');
    }

    /**
     * Derive the pet's status from its current attributes and adoption
     * records, rather than trusting a manually set value: 'deceased' when
     * date_of_death is filled, else 'adopted' when it has an adoption with
     * no return_date, else 'available'/'not_available' based on is_adoptable.
     */
    public function determineStatus(): string
    {
        if ($this->date_of_death !== null) {
            return 'deceased';
        }

        $hasOpenAdoption = $this->exists && $this->adoptions()->whereNull('return_date')->exists();

        if ($hasOpenAdoption) {
            return 'adopted';
        }

        return $this->is_adoptable ? 'available' : 'not_available';
    }

    /**
     * Get the sponsorship records for the pet.
     *
     * @return HasMany<Sponsorship, $this>
     */
    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }
}

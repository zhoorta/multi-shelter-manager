<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\MultiShelterTrait;
use Carbon\CarbonInterface;
use Database\Factories\PetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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
 * @property string|null $clinical_notes
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
    'checkin_date', 'checkout_date', 'date_of_death', 'age', 'internal_notes', 'clinical_notes',
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
     * Pets a shelter has explicitly published for adoption on the public
     * portal. Does not remove the shelter global scope; public pages must
     * call withoutGlobalScope('shelter') themselves.
     *
     * @param  Builder<Pet>  $query
     */
    #[Scope]
    protected function publishedToPortal(Builder $query): void
    {
        $query->where('publish_to_portal', true)
            ->where('is_adoptable', true)
            ->where('status', 'available')
            ->whereNull('date_of_death')
            ->whereHas('shelter');
    }

    /**
     * Reduce the description editor's HTML to a small formatting-only
     * allowlist and strip attributes from the surviving tags, since both the
     * client-side rich text editor and imported portal pages send raw HTML
     * that could otherwise carry stray attributes (e.g. onclick) into the
     * stored value. Returns null when only empty markup remains.
     */
    public static function sanitizeDescription(string $html): ?string
    {
        $allowedTags = '<p><br><b><strong><i><em><u><ul><ol><li>';

        $stripped = strip_tags($html, $allowedTags);
        $stripped = preg_replace('/<(\w+)[^>]*>/', '<$1>', $stripped) ?? $stripped;
        $stripped = trim($stripped);

        return trim(strip_tags($stripped)) !== '' ? $stripped : null;
    }

    /**
     * The pet's age, in whole years and months, as a translated string
     * (e.g. "1 ano e 3 meses"), measured up to date_of_death when the pet
     * is deceased. Null when birth_date is unknown.
     */
    protected function ageInWords(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->birth_date === null ? null : self::periodInWords($this->birth_date, $this->date_of_death),
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
     * The elapsed time between $from and $to (defaults to now), in whole
     * years and months, as a translated string (e.g. "1 ano e 3 meses").
     */
    private static function periodInWords(CarbonInterface $from, ?CarbonInterface $to = null): string
    {
        $diff = $from->diff($to ?? now());

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
     * Get the pet's species. Includes soft-deleted species, since a pet
     * keeps its species_id (RESTRICT FK) even after the species itself is
     * soft-deleted, and the pet's display must still resolve the name.
     *
     * @return BelongsTo<Species, $this>
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class)->withTrashed();
    }

    /**
     * Get the pet's breed. Includes soft-deleted breeds, since a pet keeps
     * its breed_id (RESTRICT FK) even after the breed itself is
     * soft-deleted, and the pet's display must still resolve the name.
     *
     * @return BelongsTo<Breed, $this>
     */
    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class)->withTrashed();
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
     * Get the pet's fur type. Includes soft-deleted fur types, since a pet
     * keeps its fur_type_id (RESTRICT FK) even after the fur type itself
     * is soft-deleted, and the pet's display must still resolve the name.
     *
     * @return BelongsTo<FurType, $this>
     */
    public function furType(): BelongsTo
    {
        return $this->belongsTo(FurType::class)->withTrashed();
    }

    /**
     * Get the pet's size. Includes soft-deleted sizes, since a pet keeps
     * its size_id (RESTRICT FK) even after the size itself is
     * soft-deleted, and the pet's display must still resolve the name.
     *
     * @return BelongsTo<Size, $this>
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class)->withTrashed();
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
            ->withPivot(['id', 'administered_date', 'due_date', 'status', 'lot_number', 'veterinarian_name', 'notes', 'created_by', 'updated_by'])
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

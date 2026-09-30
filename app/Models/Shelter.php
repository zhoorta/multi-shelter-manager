<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ShelterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property string $slug
 * @property string $city
 * @property int|null $region_id
 * @property string|null $logo_path
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property string|null $description
 * @property string $joining_fee
 * @property string $membership_fee
 * @property string $membership_fee_frequency
 * @property array<string, bool>|null $modules
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'short_name', 'slug', 'city', 'region_id', 'logo_path', 'address', 'postal_code', 'phone', 'email', 'website', 'description', 'joining_fee', 'membership_fee', 'membership_fee_frequency', 'modules'])]
class Shelter extends Model
{
    /** @use HasFactory<ShelterFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The optional parts of the app a shelter can switch off when it does not
     * use them. Pets, adoptions, diagnoses and facilities are always on.
     *
     * @var list<string>
     */
    public const MODULES = ['members', 'volunteers', 'sponsorships', 'adoption_applications', 'reports', 'health_records'];

    /**
     * Give new shelters a public URL slug when none was chosen.
     */
    protected static function booted(): void
    {
        static::creating(function (Shelter $shelter): void {
            if (blank($shelter->slug)) {
                $shelter->slug = self::uniqueSlug($shelter->name, $shelter->city);
            }
        });
    }

    /**
     * A free slug for the shelter's public page: the name, then the name and
     * city, then a number. Soft-deleted shelters keep theirs reserved, so an
     * old link never lands on another shelter.
     */
    public static function uniqueSlug(string $name, ?string $city = null, ?int $ignoreId = null): string
    {
        $isFree = fn (string $slug): bool => $slug !== '' && ! self::query()
            ->withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        $withCity = Str::slug($name.' '.$city) ?: 'shelter';

        foreach ([Str::slug($name), $withCity] as $candidate) {
            if ($isFree($candidate)) {
                return $candidate;
            }
        }

        $suffix = 2;

        while (! $isFree($withCity.'-'.$suffix)) {
            $suffix++;
        }

        return $withCity.'-'.$suffix;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'joining_fee' => 'decimal:2',
            'membership_fee' => 'decimal:2',
            'modules' => 'array',
        ];
    }

    /**
     * Whether an optional module is on for the shelter. A module that was
     * never switched off (no stored choice) counts as on.
     */
    public function hasModule(string $module): bool
    {
        return ($this->modules[$module] ?? true) !== false;
    }

    /**
     * Get the region (distrito) the shelter is located in. Includes
     * soft-deleted regions so a shelter keeps resolving its historical region.
     *
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class)->withTrashed();
    }

    /**
     * Get the users belonging to the shelter, with their role and
     * notification preference for this shelter on the pivot.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shelter_users')
            ->using(ShelterUser::class)
            ->withPivot(['role', 'vaccination_notifications', 'adoption_application_notifications'])
            ->withTimestamps();
    }

    /**
     * Get the facilities belonging to the shelter.
     *
     * @return HasMany<Facility, $this>
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * Get the pets belonging to the shelter.
     *
     * @return HasMany<Pet, $this>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * Get the pets the shelter has published for adoption on the public
     * portal. Removes the shelter global scope so a logged-in user of another
     * shelter sees the same pets as a guest.
     *
     * @return HasMany<Pet, $this>
     */
    public function publishedPets(): HasMany
    {
        return $this->pets()->withoutGlobalScope('shelter')->publishedToPortal();
    }

    /**
     * Get the species enabled for the shelter. Controls which species
     * appear on the sidebar for the shelter's manager and staff users.
     *
     * @return BelongsToMany<Species, $this>
     */
    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'shelter_species')->withTimestamps();
    }

    /**
     * Get the members (sócios) of the shelter's association.
     *
     * @return HasMany<Member, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}

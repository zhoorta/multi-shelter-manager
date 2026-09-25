<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\MultiShelterTrait;
use Database\Factories\VolunteerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
 * @property string $name
 * @property string|null $gender
 * @property string|null $id_card
 * @property string|null $tin
 * @property Carbon|null $birth_date
 * @property string|null $image_path
 * @property string|null $professional_activity
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $city
 * @property string|null $transport_mode
 * @property string|null $attendance_evaluation
 * @property string|null $performance_evaluation
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property bool $send_newsletter
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'shelter_id', 'name', 'gender', 'id_card', 'tin', 'birth_date', 'image_path', 'professional_activity',
    'email', 'phone', 'address', 'postal_code', 'city', 'transport_mode', 'attendance_evaluation',
    'performance_evaluation', 'start_date', 'end_date', 'send_newsletter', 'notes',
])]
class Volunteer extends Model
{
    /** @use HasFactory<VolunteerFactory> */
    use Blameable, HasFactory, MultiShelterTrait, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'send_newsletter' => 'boolean',
        ];
    }

    /**
     * Get the shelter the volunteer belongs to.
     *
     * @return BelongsTo<Shelter, $this>
     */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /**
     * Get the activities the volunteer is willing to participate in.
     *
     * @return BelongsToMany<Activity, $this>
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'volunteer_activities')->withTimestamps();
    }

    /**
     * Get the volunteer's weekly availability records.
     *
     * @return HasMany<VolunteerAvailability, $this>
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(VolunteerAvailability::class)->orderBy('day_index');
    }

    /**
     * Get the species (sections) the volunteer is available to work with.
     *
     * @return BelongsToMany<Species, $this>
     */
    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'volunteer_species')->withTimestamps();
    }

    /**
     * Get the member (sócio) record of the same person, if any.
     *
     * @return HasOne<Member, $this>
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    /**
     * Get the foster family cages this volunteer is the contact of.
     *
     * @return HasMany<Cage, $this>
     */
    public function fosterCages(): HasMany
    {
        return $this->hasMany(Cage::class)->whereHas('wing', fn ($query) => $query->where('is_foster', true));
    }
}

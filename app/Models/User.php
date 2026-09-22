<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Blameable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property bool $is_admin
 * @property int|null $current_shelter_id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $last_login
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'email', 'password', 'is_admin', 'current_shelter_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Blameable, HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Every shelter this user belongs to, with their role and notification
     * preference for that shelter on the pivot. Empty for an admin.
     *
     * @return BelongsToMany<Shelter, $this>
     */
    public function shelters(): BelongsToMany
    {
        return $this->belongsToMany(Shelter::class, 'shelter_users')
            ->using(ShelterUser::class)
            ->withPivot(['role', 'vaccination_notifications'])
            ->withTimestamps();
    }

    /**
     * The shelter this user is currently acting within — what every
     * shelter-scoped screen and query reads. Null for an admin, or for a
     * non-admin who has not yet been assigned/selected an active shelter.
     *
     * @return BelongsTo<Shelter, $this>
     */
    public function currentShelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class, 'current_shelter_id');
    }

    /**
     * This user's role for a given shelter, or null if they have no
     * membership there.
     */
    public function roleForShelter(int $shelterId): ?string
    {
        return $this->shelters()->wherePivot('shelter_id', $shelterId)->first()?->pivot->role;
    }

    public function isManagerOf(int $shelterId): bool
    {
        return $this->roleForShelter($shelterId) === 'manager';
    }

    public function isStaffOf(int $shelterId): bool
    {
        return $this->roleForShelter($shelterId) === 'staff';
    }

    public function belongsToShelter(int $shelterId): bool
    {
        return $this->roleForShelter($shelterId) !== null;
    }

    /**
     * Convenience for shelter-scoped screens: whether this user manages
     * the shelter they are currently acting within.
     */
    public function isManagerOfCurrentShelter(): bool
    {
        return $this->current_shelter_id !== null && $this->isManagerOf($this->current_shelter_id);
    }

    /**
     * @return array<int, int>
     */
    public function managedShelterIds(): array
    {
        return $this->shelters()->wherePivot('role', 'manager')->pluck('shelters.id')->all();
    }

    public function isManagerOfAnyShelter(): bool
    {
        return $this->managedShelterIds() !== [];
    }
}

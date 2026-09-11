<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\SponsorshipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property bool $send_feedback
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
    'pet_id', 'name', 'email', 'phone', 'address', 'postal_code', 'city',
    'send_feedback', 'send_newsletter', 'notes',
])]
class Sponsorship extends Model
{
    /** @use HasFactory<SponsorshipFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'send_feedback' => 'boolean',
            'send_newsletter' => 'boolean',
        ];
    }

    /**
     * Get the pet that is sponsored.
     *
     * @return BelongsTo<Pet, $this>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the payments made for this sponsorship.
     *
     * @return HasMany<SponsorshipPayment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SponsorshipPayment::class);
    }
}

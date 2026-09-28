<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\TracksDueDates;
use Database\Factories\PetTreatmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pet_id
 * @property int $treatment_id
 * @property Carbon|null $administered_date
 * @property Carbon|null $due_date
 * @property string $status
 * @property string|null $product
 * @property string|null $veterinarian_name
 * @property string|null $notes
 * @property Carbon|null $notification_date
 * @property array<int, string>|null $notification_recipients
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['pet_id', 'treatment_id', 'administered_date', 'due_date', 'status', 'product', 'veterinarian_name', 'notes'])]
class PetTreatment extends Model
{
    /** @use HasFactory<PetTreatmentFactory> */
    use Blameable, HasFactory, SoftDeletes, TracksDueDates;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'administered_date' => 'date',
            'due_date' => 'date',
            'notification_date' => 'datetime',
            'notification_recipients' => 'array',
        ];
    }

    /**
     * Get the pet this treatment record belongs to. PetTreatment has no
     * shelter_id of its own, so shelter-wide queries scope through it.
     *
     * @return BelongsTo<Pet, $this>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the treatment given in this record. Includes soft-deleted
     * treatments so history keeps its name after the catalogue changes.
     *
     * @return BelongsTo<Treatment, $this>
     */
    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class)->withTrashed();
    }

    /**
     * Records of the same treatment are grouped for the pending rule.
     */
    protected static function dueDateKindColumn(): string
    {
        return 'treatment_id';
    }
}

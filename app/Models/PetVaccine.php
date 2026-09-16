<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pet_id
 * @property int $vaccine_id
 * @property Carbon|null $administered_date
 * @property Carbon|null $due_date
 * @property string|null $lot_number
 * @property string|null $veterinarian_name
 * @property string|null $notes
 * @property string $status
 * @property Carbon|null $notification_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
class PetVaccine extends Pivot
{
    use Blameable, SoftDeletes;

    protected $table = 'pet_vaccines';

    public $incrementing = true;

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
        ];
    }

    /**
     * Get the pet this vaccination record belongs to. Needed to list
     * vaccinations across the shelter (see App\Livewire\Pets\ManageVaccinations)
     * rather than just accessed via Pet::vaccines()'s pivot.
     *
     * @return BelongsTo<Pet, $this>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the vaccine administered in this vaccination record.
     *
     * @return BelongsTo<Vaccine, $this>
     */
    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }
}

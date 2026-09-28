<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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
 * @property array<int, string>|null $notification_recipients
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
            'notification_recipients' => 'array',
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

    /**
     * Vaccinations whose due date is still open. A scheduled row stays
     * pending until VaccinationForm fulfils it with a dose. A logged dose
     * with its own due date (the next booster) stays pending until a later
     * dose of the same vaccine is logged for the pet, or until an open
     * scheduled row for that vaccine takes over the planning, so a pet is
     * never counted twice for the same vaccine.
     *
     * @param  Builder<PetVaccine>  $query
     */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->whereNotNull('pet_vaccines.due_date')
            ->where(fn (Builder $query) => $query->where('pet_vaccines.status', 'scheduled')
                ->orWhere(fn (Builder $query) => $query->where('pet_vaccines.status', 'administered')
                    ->whereNotExists(fn ($query) => $query->selectRaw('1')
                        ->from('pet_vaccines as other_vaccinations')
                        ->whereColumn('other_vaccinations.pet_id', 'pet_vaccines.pet_id')
                        ->whereColumn('other_vaccinations.vaccine_id', 'pet_vaccines.vaccine_id')
                        ->whereNull('other_vaccinations.deleted_at')
                        ->where(fn ($query) => $query->whereColumn('other_vaccinations.administered_date', '>', 'pet_vaccines.administered_date')
                            ->orWhere(fn ($query) => $query->where('other_vaccinations.status', 'scheduled')->whereNotNull('other_vaccinations.due_date'))))));
    }
}

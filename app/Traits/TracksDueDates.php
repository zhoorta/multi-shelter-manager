<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Shared "pending" rule for per-pet health records that carry a dose date
 * and a next due date (pet_vaccines, pet_treatments). The using model names
 * the column that groups records of the same kind (e.g. vaccine_id).
 */
trait TracksDueDates
{
    /**
     * The column identifying records of the same kind for a pet, e.g.
     * vaccine_id or treatment_id.
     */
    abstract protected static function dueDateKindColumn(): string;

    /**
     * Records whose due date is still open. A scheduled row stays pending
     * until a form fulfils it with a dose. A logged dose with its own due
     * date (the next dose) stays pending until a later dose of the same kind
     * is logged for the pet, or until an open scheduled row of that kind
     * takes over the planning, so a pet is never counted twice for the
     * same vaccine or treatment.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $table = $this->getTable();
        $kindColumn = static::dueDateKindColumn();

        $query->whereNotNull("{$table}.due_date")
            ->where(fn (Builder $query) => $query->where("{$table}.status", 'scheduled')
                ->orWhere(fn (Builder $query) => $query->where("{$table}.status", 'administered')
                    ->whereNotExists(fn ($query) => $query->selectRaw('1')
                        ->from("{$table} as other_records")
                        ->whereColumn('other_records.pet_id', "{$table}.pet_id")
                        ->whereColumn("other_records.{$kindColumn}", "{$table}.{$kindColumn}")
                        ->whereNull('other_records.deleted_at')
                        ->where(fn ($query) => $query->whereColumn('other_records.administered_date', '>', "{$table}.administered_date")
                            ->orWhere(fn ($query) => $query->where('other_records.status', 'scheduled')->whereNotNull('other_records.due_date'))))));
    }

    /**
     * The open scheduled record (earliest due first) that a newly logged
     * dose fulfils, so the dose closes it instead of leaving it pending next
     * to a new row. A dose older than one already logged for the same kind
     * is history being backfilled and fulfils nothing.
     */
    public static function scheduledRecordFulfilledBy(int $petId, int $kindId, string $administeredDate): ?static
    {
        $kindColumn = static::dueDateKindColumn();

        $isBackfilledDose = static::query()
            ->where('pet_id', $petId)
            ->where($kindColumn, $kindId)
            ->where('administered_date', '>', $administeredDate)
            ->exists();

        if ($isBackfilledDose) {
            return null;
        }

        return static::query()
            ->where('pet_id', $petId)
            ->where($kindColumn, $kindId)
            ->where('status', 'scheduled')
            ->orderBy('due_date')
            ->first();
    }
}

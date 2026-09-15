<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pet_id
 * @property int $vaccine_id
 * @property Carbon $administered_at
 * @property Carbon|null $next_due_at
 * @property string|null $lot_number
 * @property string|null $veterinarian_name
 * @property string|null $notes
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
            'administered_at' => 'date',
            'next_due_at' => 'date',
        ];
    }
}

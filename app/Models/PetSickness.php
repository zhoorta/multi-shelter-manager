<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pet_id
 * @property int $sickness_id
 * @property Carbon $diagnosed_at
 * @property string $status
 * @property string|null $treatment_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class PetSickness extends Pivot
{
    use Blameable;

    protected $table = 'pet_sickness';

    public $incrementing = true;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'diagnosed_at' => 'date',
        ];
    }
}

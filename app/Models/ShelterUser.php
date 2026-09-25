<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $shelter_id
 * @property int $user_id
 * @property string $role
 * @property bool $vaccination_notifications
 * @property bool $adoption_application_notifications
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ShelterUser extends Pivot
{
    protected $table = 'shelter_users';

    public $incrementing = true;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vaccination_notifications' => 'boolean',
            'adoption_application_notifications' => 'boolean',
        ];
    }
}

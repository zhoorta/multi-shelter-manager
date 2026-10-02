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
 * @property array<int, string>|null $edit_areas
 * @property bool $vaccination_notifications
 * @property bool $adoption_application_notifications
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ShelterUser extends Pivot
{
    /**
     * The areas of daily work a staff member can be limited to editing; a
     * manager always edits all of them and a viewer none.
     *
     * @var array<int, string>
     */
    public const EDIT_AREAS = ['pets', 'health', 'adoptions', 'sponsorships', 'members'];

    protected $table = 'shelter_users';

    /**
     * Translated name of each editable area.
     *
     * @return array<string, string>
     */
    public static function areaLabels(): array
    {
        return [
            'pets' => __('Animals'),
            'health' => __('Health'),
            'adoptions' => __('Adoptions'),
            'sponsorships' => __('Sponsorships'),
            'members' => __('Members'),
        ];
    }

    public $incrementing = true;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'edit_areas' => 'array',
            'vaccination_notifications' => 'boolean',
            'adoption_application_notifications' => 'boolean',
        ];
    }
}

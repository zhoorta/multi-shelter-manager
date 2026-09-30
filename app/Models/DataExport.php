<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DataExportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Audit trail of a shelter's data exports (who downloaded them, when and from where).
 *
 * @property int $id
 * @property int $shelter_id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['shelter_id', 'user_id', 'ip_address'])]
class DataExport extends Model
{
    /** @use HasFactory<DataExportFactory> */
    use HasFactory;

    /**
     * Get the shelter whose data was exported.
     *
     * @return BelongsTo<Shelter, $this>
     */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    /**
     * Get the user who downloaded the export, even if their account was deleted since.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}

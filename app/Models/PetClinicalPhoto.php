<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use Database\Factories\PetClinicalPhotoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * One dated photo in the clinical follow-up of a pet (a lump, a wound), grouped
 * with the other photos of the same label. The files live on the private disk
 * and are only served through the authenticated pets.clinical-photos.file route.
 *
 * @property int $id
 * @property int $pet_id
 * @property string $label
 * @property Carbon $taken_at
 * @property string|null $notes
 * @property string $image_path
 * @property string|null $thumb_path
 * @property int|null $file_size
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 */
#[Fillable(['pet_id', 'label', 'taken_at', 'notes', 'image_path', 'thumb_path', 'file_size'])]
class PetClinicalPhoto extends Model
{
    /** @use HasFactory<PetClinicalPhotoFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'taken_at' => 'date',
        ];
    }

    /**
     * Get the pet this photo belongs to. The photo has no shelter_id of its
     * own, so shelter-wide queries scope through the pet.
     *
     * @return BelongsTo<Pet, $this>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}

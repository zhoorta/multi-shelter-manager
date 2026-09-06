<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FurTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name'])]
class FurType extends Model
{
    /** @use HasFactory<FurTypeFactory> */
    use HasFactory;
}

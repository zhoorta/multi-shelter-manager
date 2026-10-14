<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\PetImage;
use App\Models\Shelter;
use App\Models\Volunteer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

#[Signature('app:backfill-file-sizes')]
#[Description('Store the size of uploaded files (pet photos, volunteer photos, shelter logos) that have none yet')]
class BackfillFileSizes extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targets = [
            'pet photos' => [PetImage::query(), 'image_path', 'file_size'],
            'volunteer photos' => [Volunteer::query()->withoutGlobalScopes()->withTrashed(), 'image_path', 'image_size'],
            'shelter logos' => [Shelter::query()->withTrashed(), 'logo_path', 'logo_size'],
        ];

        foreach ($targets as $label => [$query, $pathColumn, $sizeColumn]) {
            $this->backfill($label, $query, $pathColumn, $sizeColumn);
        }

        return self::SUCCESS;
    }

    /**
     * @param  Builder<*>  $query
     */
    private function backfill(string $label, Builder $query, string $pathColumn, string $sizeColumn): void
    {
        $updated = 0;
        $missing = 0;

        $query->whereNotNull($pathColumn)->whereNull($sizeColumn)->each(function ($model) use ($pathColumn, $sizeColumn, &$updated, &$missing): void {
            if (! Storage::exists($model->{$pathColumn})) {
                $missing++;

                return;
            }

            $model->forceFill([$sizeColumn => Storage::size($model->{$pathColumn})])->saveQuietly();
            $updated++;
        });

        $this->info("{$label}: {$updated} updated, {$missing} missing on disk.");
    }
}

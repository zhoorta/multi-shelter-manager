<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds one species' breeds from the subclass's SPECIES, BREEDS and
 * DEFAULT_BREED constants.
 */
abstract class SpeciesBreedSeeder extends Seeder
{
    /**
     * Seed the species' breeds and mark DEFAULT_BREED as its only default.
     * Safe to re-run: breeds already present for the species (including
     * soft-deleted ones) are matched by name and never duplicated.
     */
    public function run(): void
    {
        $speciesId = DB::table('species')->where('name', static::SPECIES)->value('id');

        if ($speciesId === null) {
            $this->command?->warn('Species "'.static::SPECIES.'" not found — skipping its breeds.');

            return;
        }

        $existingBreeds = DB::table('breeds')->where('species_id', $speciesId)->pluck('name')->all();

        $newBreeds = array_values(array_diff(static::BREEDS, $existingBreeds));

        DB::transaction(function () use ($speciesId, $newBreeds): void {
            DB::table('breeds')->insert(array_map(fn (string $breed): array => [
                'species_id' => $speciesId,
                'name' => $breed,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ], $newBreeds));

            DB::table('breeds')->where('species_id', $speciesId)->update(['is_default' => false]);

            DB::table('breeds')
                ->where('species_id', $speciesId)
                ->where('name', static::DEFAULT_BREED)
                ->update(['is_default' => true]);
        });
    }
}

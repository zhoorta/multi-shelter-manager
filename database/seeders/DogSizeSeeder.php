<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DogSizeSeeder extends Seeder
{
    public const SPECIES = 'Cão';

    /**
     * Dog sizes ("Porte"), taken from the PortugalZoofilo.net animal form
     * and listed in that system's id order.
     *
     * @var list<string>
     */
    public const SIZES = ['Pequeno', 'Médio', 'Grande', 'Gigante'];

    /**
     * Seed the dog sizes. Safe to re-run: sizes already present for the
     * species (including soft-deleted ones) are matched by name and never
     * duplicated.
     */
    public function run(): void
    {
        $speciesId = DB::table('species')->where('name', self::SPECIES)->value('id');

        if ($speciesId === null) {
            $this->command?->warn('Species "'.self::SPECIES.'" not found — skipping its sizes.');

            return;
        }

        $existingSizes = DB::table('sizes')->where('species_id', $speciesId)->pluck('name')->all();

        DB::table('sizes')->insert(array_map(fn (string $size): array => [
            'species_id' => $speciesId,
            'name' => $size,
            'created_at' => now(),
            'updated_at' => now(),
        ], array_values(array_diff(self::SIZES, $existingSizes))));
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FurTypeSeeder extends Seeder
{
    /**
     * Pet fur types, taken from the PortugalZoofilo.net animal form and
     * listed in that system's id order, so a fresh database gets the same ids.
     *
     * @var list<string>
     */
    public const FUR_TYPES = [
        'Curto e Grosso', 'Curto e Sedoso', 'Comprido e Liso', 'Comprido e Encaracolado', 'Comprido e Ondulado',
        'Médio e Liso', 'Médio e Encaracolado', 'Médio e Ondulado', 'Curto', 'Médio', 'Longo',
    ];

    /**
     * Seed the pet fur types. Safe to re-run: existing fur types are matched
     * by name and never duplicated.
     */
    public function run(): void
    {
        DB::table('fur_types')->upsert(
            array_map(fn (string $furType): array => ['name' => $furType, 'created_at' => now(), 'updated_at' => now()], self::FUR_TYPES),
            ['name'],
            ['updated_at'],
        );
    }
}

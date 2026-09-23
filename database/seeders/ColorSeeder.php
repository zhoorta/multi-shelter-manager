<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Pet colors, taken from the PortugalZoofilo.net animal form and listed
     * in that system's id order, so a fresh database gets the same ids.
     *
     * @var list<string>
     */
    public const COLORS = [
        'Preto', 'Branco', 'Castanho', 'Amarelo', 'Dourado', 'Cinza', 'Chocolate', 'Azul',
        'Vermelho', 'Laranja', 'Bicolor', 'Tricolor', 'Tartaruga', 'Tigrado', 'Creme',
    ];

    /**
     * Seed the pet colors. Safe to re-run: existing colors are matched by
     * name and never duplicated.
     */
    public function run(): void
    {
        DB::table('colors')->upsert(
            array_map(fn (string $color): array => ['name' => $color, 'created_at' => now(), 'updated_at' => now()], self::COLORS),
            ['name'],
            ['updated_at'],
        );
    }
}

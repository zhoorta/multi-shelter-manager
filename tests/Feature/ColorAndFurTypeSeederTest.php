<?php

use App\Models\Color;
use App\Models\FurType;
use Database\Seeders\ColorSeeder;
use Database\Seeders\FurTypeSeeder;

test('seeds every color and fur type in legacy id order', function () {
    $this->seed([ColorSeeder::class, FurTypeSeeder::class]);

    expect(Color::query()->orderBy('id')->pluck('name')->all())->toBe(ColorSeeder::COLORS)
        ->and(FurType::query()->orderBy('id')->pluck('name')->all())->toBe(FurTypeSeeder::FUR_TYPES);
});

test('can be re-run without duplicating colors or fur types', function () {
    $this->seed([ColorSeeder::class, FurTypeSeeder::class]);
    $this->seed([ColorSeeder::class, FurTypeSeeder::class]);

    expect(Color::query()->count())->toBe(count(ColorSeeder::COLORS))
        ->and(FurType::query()->count())->toBe(count(FurTypeSeeder::FUR_TYPES));
});

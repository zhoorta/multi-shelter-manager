<?php

use App\Models\Species;

test('guesses an emoji from the species name in pt or en', function (string $name, string $emoji) {
    expect(Species::factory()->make(['name' => $name])->emoji)->toBe($emoji);
})->with([
    ['Cão', '🐶'],
    ['Dog', '🐶'],
    ['Gato', '🐱'],
    ['Coelho', '🐰'],
    ['Hamster', '🐾'],
]);

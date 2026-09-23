<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\FurType;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\Size;
use App\Models\Species;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Demo data for the public welcome page: a handful of shelters spread over
 * several regions, each with published dogs and cats that have real photos
 * (downloaded from dog.ceo / thecatapi.com), plus a few pets that must stay
 * hidden from the portal (unpublished, not adoptable, adopted).
 *
 * Run with: php artisan db:seed --class=PublicPortalDemoSeeder
 */
class PublicPortalDemoSeeder extends Seeder
{
    private const DOG_SPECIES = 'Cão';

    private const CAT_SPECIES = 'Gato';

    /**
     * Demo shelters, keyed by name.
     *
     * @var array<string, array{region: string, city: string, short_name: string, phone: string, email: string}>
     */
    private const SHELTERS = [
        'Patinhas de Lisboa' => ['region' => 'Lisboa', 'city' => 'Lisboa', 'short_name' => 'Patinhas', 'phone' => '213 000 111', 'email' => 'adotar@patinhas.test'],
        'Abrigo Sol do Algarve' => ['region' => 'Faro', 'city' => 'Loulé', 'short_name' => 'Sol do Algarve', 'phone' => '289 000 222', 'email' => 'ola@soldoalgarve.test'],
        'Amigos de Quatro Patas' => ['region' => 'Braga', 'city' => 'Guimarães', 'short_name' => 'Quatro Patas', 'phone' => '253 000 333', 'email' => 'geral@quatropatas.test'],
        'Refúgio do Mondego' => ['region' => 'Coimbra', 'city' => 'Coimbra', 'short_name' => 'Refúgio Mondego', 'phone' => '239 000 444', 'email' => 'adocoes@mondego.test'],
    ];

    /**
     * dog.ceo breed path for each seeded dog breed name.
     *
     * @var array<string, string>
     */
    private const DOG_PHOTO_BREEDS = [
        'Cão Rafeiro' => 'mix',
        'Podengo Médio Português' => 'hound/ibizan',
        'Cão da Serra da Estrela' => 'pyrenees',
        'Rafeiro do Alentejo' => 'mastiff/english',
        'Cão de Água Português' => 'waterdog/spanish',
        'Cão Pastor Alemão' => 'german/shepherd',
        'Labrador Retriever' => 'labrador',
    ];

    /**
     * thecatapi.com breed id for each seeded cat breed name (null = any cat).
     *
     * @var array<string, string|null>
     */
    private const CAT_PHOTO_BREEDS = [
        'Europeu Comum' => null,
        'Siamês' => 'siam',
        'Persa' => 'pers',
        'Angorá' => 'tang',
    ];

    private const DOG_NAMES = ['Bolinha', 'Farrusco', 'Pipoca', 'Tareco', 'Zeus', 'Kika', 'Nala', 'Bóris', 'Estrela', 'Fofinho', 'Lua', 'Pantufa', 'Bento', 'Mel', 'Tobias', 'Amora', 'Rufus', 'Canela'];

    private const CAT_NAMES = ['Bigodes', 'Mimi', 'Novelo', 'Pérola', 'Salem', 'Pimenta', 'Tigre', 'Nuvem', 'Olívia', 'Chico', 'Luna', 'Castanha', 'Faísca', 'Neve'];

    private const DESCRIPTIONS = [
        '<p>Super meigo e brincalhão, adora passeios e mimos no sofá. Dá-se bem com outros animais.</p>',
        '<p>Um pouco tímido no início, mas depois de ganhar confiança é o companheiro mais fiel do mundo.</p>',
        '<p>Cheio de energia! Ideal para uma família ativa que goste de correr e brincar ao ar livre.</p>',
        '<p>Calmo e muito carinhoso. Perfeito para um apartamento e para quem procura um amigo tranquilo.</p>',
        '<p>Adora crianças e brinquedos que fazem barulho. Já está <b>vacinado</b> e <b>desparasitado</b>.</p>',
        '<p>Sobreviveu à rua e continua a confiar nas pessoas. Merece muito uma segunda oportunidade.</p>',
    ];

    public function run(): void
    {
        $species = Species::query()->pluck('id', 'name');
        $dogSpeciesId = $species[self::DOG_SPECIES];
        $catSpeciesId = $species[self::CAT_SPECIES];

        $shelters = $this->seedShelters([$dogSpeciesId, $catSpeciesId]);

        if ($shelters->last()->pets()->exists()) {
            $this->command?->warn('Demo portal pets already exist — skipping.');

            return;
        }

        $dogBreeds = Breed::query()->where('species_id', $dogSpeciesId)->pluck('id', 'name');
        $catBreeds = Breed::query()->where('species_id', $catSpeciesId)->pluck('id', 'name');
        $dogSizes = Size::query()->where('species_id', $dogSpeciesId)->pluck('id')->all();
        $furTypeIds = FurType::query()->pluck('id')->all();

        $petIndex = 0;
        $nameCounters = ['dog' => 0, 'cat' => 0];

        foreach ($shelters as $shelterIndex => $shelter) {
            foreach (range(1, 8) as $slot) {
                $isDog = ($petIndex + $shelterIndex) % 3 !== 0;
                $breedName = $isDog
                    ? array_keys(self::DOG_PHOTO_BREEDS)[$petIndex % count(self::DOG_PHOTO_BREEDS)]
                    : array_keys(self::CAT_PHOTO_BREEDS)[$petIndex % count(self::CAT_PHOTO_BREEDS)];
                $names = $isDog ? self::DOG_NAMES : self::CAT_NAMES;
                $name = $names[$nameCounters[$isDog ? 'dog' : 'cat']++ % count($names)];

                // The last slots of each shelter hold the pets that must NOT show on the portal.
                $visibility = match ($slot) {
                    7 => ['publish_to_portal' => false, 'is_adoptable' => true, 'status' => 'available'],
                    8 => ['publish_to_portal' => true, 'is_adoptable' => $shelterIndex % 2 === 0, 'status' => $shelterIndex % 2 === 0 ? 'adopted' : 'not_available'],
                    default => ['publish_to_portal' => true, 'is_adoptable' => true, 'status' => 'available'],
                };

                $pet = Pet::query()->create([
                    'shelter_id' => $shelter->id,
                    'species_id' => $isDog ? $dogSpeciesId : $catSpeciesId,
                    'breed_id' => ($isDog ? $dogBreeds : $catBreeds)[$breedName],
                    'size_id' => $isDog ? $dogSizes[$petIndex % count($dogSizes)] : null,
                    'fur_type_id' => $furTypeIds[$petIndex % count($furTypeIds)] ?? null,
                    'ref' => '',
                    'name' => $name,
                    'gender' => $petIndex % 2 === 0 ? 'male' : 'female',
                    'is_neutered' => $petIndex % 3 !== 1,
                    'birth_date' => now()->subMonths(random_int(3, 120)),
                    'checkin_date' => now()->subDays(random_int(5, 400)),
                    'description' => self::DESCRIPTIONS[$petIndex % count(self::DESCRIPTIONS)],
                    'is_sponsorable' => true,
                    'is_featured' => $slot === 1,
                    ...$visibility,
                ]);

                $pet->update(['ref' => 'PET'.str_pad((string) $pet->id, 5, '0', STR_PAD_LEFT)]);

                // The photo APIs occasionally fail or return an unreadable image, so retry a few times.
                $imagePath = null;

                for ($attempt = 0; $attempt < 4 && $imagePath === null; $attempt++) {
                    $imagePath = $isDog
                        ? $this->downloadDogPhoto(self::DOG_PHOTO_BREEDS[$breedName], $pet)
                        : $this->downloadCatPhoto(self::CAT_PHOTO_BREEDS[$breedName], $pet);
                }

                if ($imagePath !== null) {
                    PetImage::query()->create(['pet_id' => $pet->id, 'image_path' => $imagePath, 'is_main' => true]);
                }

                $this->command?->info("  {$pet->ref} {$pet->name} ({$shelter->short_name})".($imagePath ? ' 📷' : ''));

                $petIndex++;
            }
        }
    }

    /**
     * The existing first shelter plus the demo shelters, created once.
     *
     * @param  array<int, int>  $speciesIds
     * @return Collection<int, Shelter>
     */
    private function seedShelters(array $speciesIds): Collection
    {
        $shelters = collect([Shelter::query()->orderBy('id')->firstOrFail()]);

        foreach (self::SHELTERS as $name => $attributes) {
            $shelter = Shelter::query()->firstOrCreate(['name' => $name], [
                'short_name' => $attributes['short_name'],
                'city' => $attributes['city'],
                'region_id' => Region::query()->where('name', $attributes['region'])->value('id'),
                'phone' => $attributes['phone'],
                'email' => $attributes['email'],
                'description' => 'Abrigo de demonstração.',
            ]);

            $shelter->species()->syncWithoutDetaching($speciesIds);

            $shelters->push($shelter);
        }

        return $shelters;
    }

    private function downloadDogPhoto(string $breedPath, Pet $pet): ?string
    {
        try {
            $url = Http::timeout(15)->get("https://dog.ceo/api/breed/{$breedPath}/images/random")->json('message');

            return is_string($url) ? $this->storePhoto($url, $pet) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function downloadCatPhoto(?string $breedId, Pet $pet): ?string
    {
        try {
            $url = Http::timeout(15)
                ->get('https://api.thecatapi.com/v1/images/search', array_filter(['mime_types' => 'jpg', 'breed_ids' => $breedId]))
                ->json('0.url');

            return is_string($url) ? $this->storePhoto($url, $pet) : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Download the photo, shrink it to at most 1000px wide and store it on
     * the public disk where PetForm keeps uploaded pet photos.
     */
    private function storePhoto(string $url, Pet $pet): ?string
    {
        $image = @imagecreatefromstring(Http::timeout(30)->get($url)->throw()->body());

        if ($image === false) {
            return null;
        }

        if (imagesx($image) > 1000) {
            $image = imagescale($image, 1000);
        }

        ob_start();
        imagejpeg($image, null, 82);
        $contents = (string) ob_get_clean();

        $path = 'pets/demo-'.Str::slug($pet->name).'-'.$pet->id.'.jpg';
        Storage::disk('public')->put($path, $contents);

        return $path;
    }
}

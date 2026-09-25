<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Adoption;
use App\Models\AdoptionApplication;
use App\Models\Breed;
use App\Models\Cage;
use App\Models\Color;
use App\Models\Facility;
use App\Models\FurType;
use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\PetVaccine;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\Sickness;
use App\Models\Size;
use App\Models\Species;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use App\Models\User;
use App\Models\Vaccine;
use App\Models\Volunteer;
use App\Models\VolunteerAvailability;
use App\Models\Wing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * English demo data for the GitHub documentation screenshots: global lookups
 * (species, breeds, colors, fur types, sizes, vaccines, sicknesses, activities,
 * regions), four shelters with facilities, wings and cages, dogs and cats with
 * real photos (dog.ceo / thecatapi.com), vaccinations, sicknesses, adoptions,
 * sponsorships, volunteers and members (sócios) with their fee payments.
 * Meant for an empty database:
 *
 *   php artisan migrate:fresh --seeder=DocumentationDemoSeeder
 *
 * Logins (password "password"): admin@example.com, manager@example.com, staff@example.com,
 *         viewer@example.com.
 */
class DocumentationDemoSeeder extends Seeder
{
    /**
     * Dog breeds and their dog.ceo photo path; the first one is the default breed.
     *
     * @var array<string, string>
     */
    private const DOG_BREEDS = [
        'Mixed Breed' => 'mix',
        'Labrador Retriever' => 'labrador',
        'German Shepherd' => 'german/shepherd',
        'Golden Retriever' => 'retriever/golden',
        'Beagle' => 'beagle',
        'Border Collie' => 'collie/border',
        'Portuguese Water Dog' => 'waterdog/spanish',
        'Boxer' => 'boxer',
        'Pit Bull' => 'pitbull',
        'Husky' => 'husky',
    ];

    /**
     * Cat breeds and their thecatapi.com breed id; the first one is the default breed.
     *
     * @var array<string, string|null>
     */
    private const CAT_BREEDS = [
        'Domestic Shorthair' => null,
        'Siamese' => 'siam',
        'Persian' => 'pers',
        'Maine Coon' => 'mcoo',
        'British Shorthair' => 'bsho',
        'Bengal' => 'beng',
        'Ragdoll' => 'ragd',
    ];

    private const COLORS = ['Black', 'White', 'Brown', 'Grey', 'Cream', 'Ginger', 'Golden', 'Brindle', 'Tabby', 'Tricolor'];

    private const FUR_TYPES = ['Short', 'Medium', 'Long', 'Wiry', 'Curly', 'Double Coat'];

    private const DOG_SIZES = ['Small', 'Medium', 'Large', 'Giant'];

    private const ACTIVITIES = [
        'Infirmary Support', 'Animal Care', 'Dog Walking', 'Kennel Cleaning', 'Foster Care', 'Fundraising Campaigns',
        'Animal Transport', 'Administrative Support', 'Data Entry', 'Animal Rescue',
    ];

    private const REGIONS = ['Aveiro', 'Braga', 'Coimbra', 'Faro', 'Leiria', 'Lisboa', 'Porto', 'Setúbal'];

    /**
     * Shelters keyed by name; the first one is the manager's shelter used for most screenshots.
     *
     * @var array<string, array{short_name: string, region: string, city: string, address: string, postal_code: string, phone: string, email: string, website: string, description: string}>
     */
    private const SHELTERS = [
        'Happy Paws Animal Shelter' => [
            'short_name' => 'Happy Paws', 'region' => 'Lisboa', 'city' => 'Lisbon', 'address' => 'Rua das Flores, 120',
            'postal_code' => '1200-195', 'phone' => '+351 213 000 111', 'email' => 'hello@happypaws.example',
            'website' => 'https://happypaws.example',
            'description' => 'A non-profit shelter rescuing, rehabilitating and rehoming abandoned dogs and cats since 2009.',
        ],
        'Sunny Tails Rescue' => [
            'short_name' => 'Sunny Tails', 'region' => 'Faro', 'city' => 'Loulé', 'address' => 'Estrada Nacional 125, km 12',
            'postal_code' => '8100-000', 'phone' => '+351 289 000 222', 'email' => 'adopt@sunnytails.example',
            'website' => 'https://sunnytails.example',
            'description' => 'Rescuing stray animals across the Algarve and finding them loving families.',
        ],
        'Four Paws Friends' => [
            'short_name' => 'Four Paws', 'region' => 'Braga', 'city' => 'Guimarães', 'address' => 'Avenida da Liberdade, 45',
            'postal_code' => '4800-000', 'phone' => '+351 253 000 333', 'email' => 'info@fourpaws.example',
            'website' => 'https://fourpaws.example',
            'description' => 'Volunteer-run association caring for more than 80 animals in the north of Portugal.',
        ],
        'Riverside Animal Refuge' => [
            'short_name' => 'Riverside', 'region' => 'Coimbra', 'city' => 'Coimbra', 'address' => 'Rua do Mondego, 8',
            'postal_code' => '3000-000', 'phone' => '+351 239 000 444', 'email' => 'contact@riverside.example',
            'website' => 'https://riverside.example',
            'description' => 'A small refuge by the river, specialised in senior and special-needs pets.',
        ],
    ];

    /**
     * Pets of the main shelter: [name, species, breed, gender, age in months, status, color, fur type, size].
     *
     * @var list<array{0: string, 1: string, 2: string, 3: string, 4: int, 5: string, 6: string, 7: string, 8: string|null}>
     */
    private const MAIN_PETS = [
        ['Max', 'dog', 'Labrador Retriever', 'male', 36, 'available', 'Golden', 'Short', 'Large'],
        ['Bella', 'dog', 'Mixed Breed', 'female', 18, 'available', 'Brown', 'Short', 'Medium'],
        ['Rocky', 'dog', 'German Shepherd', 'male', 60, 'available', 'Black', 'Double Coat', 'Large'],
        ['Daisy', 'dog', 'Beagle', 'female', 24, 'available', 'Tricolor', 'Short', 'Small'],
        ['Charlie', 'dog', 'Border Collie', 'male', 30, 'available', 'Black', 'Medium', 'Medium'],
        ['Luna', 'dog', 'Golden Retriever', 'female', 8, 'available', 'Golden', 'Long', 'Large'],
        ['Buddy', 'dog', 'Mixed Breed', 'male', 96, 'not_available', 'Cream', 'Wiry', 'Medium'],
        ['Rosie', 'dog', 'Portuguese Water Dog', 'female', 42, 'available', 'Black', 'Curly', 'Medium'],
        ['Duke', 'dog', 'Boxer', 'male', 54, 'adopted', 'Brindle', 'Short', 'Large'],
        ['Molly', 'dog', 'Pit Bull', 'female', 28, 'available', 'Grey', 'Short', 'Medium'],
        ['Zeus', 'dog', 'Husky', 'male', 20, 'available', 'Grey', 'Double Coat', 'Large'],
        ['Coco', 'dog', 'Mixed Breed', 'female', 4, 'available', 'Brown', 'Short', 'Small'],
        ['Bruno', 'dog', 'Mixed Breed', 'male', 120, 'adopted', 'Black', 'Medium', 'Large'],
        ['Oliver', 'cat', 'Domestic Shorthair', 'male', 14, 'available', 'Ginger', 'Short', null],
        ['Cleo', 'cat', 'Siamese', 'female', 30, 'available', 'Cream', 'Short', null],
        ['Simba', 'cat', 'Maine Coon', 'male', 40, 'available', 'Tabby', 'Long', null],
        ['Nala', 'cat', 'Domestic Shorthair', 'female', 6, 'available', 'Tabby', 'Short', null],
        ['Shadow', 'cat', 'Domestic Shorthair', 'male', 72, 'not_available', 'Black', 'Short', null],
        ['Misty', 'cat', 'Persian', 'female', 50, 'available', 'White', 'Long', null],
        ['Tiger', 'cat', 'Bengal', 'male', 22, 'adopted', 'Tabby', 'Short', null],
        ['Willow', 'cat', 'British Shorthair', 'female', 34, 'available', 'Grey', 'Short', null],
        ['Pepper', 'cat', 'Domestic Shorthair', 'female', 3, 'available', 'Tricolor', 'Short', null],
        ['Milo', 'cat', 'Ragdoll', 'male', 26, 'available', 'Cream', 'Long', null],
        ['Smokey', 'cat', 'Domestic Shorthair', 'male', 150, 'deceased', 'Grey', 'Short', null],
    ];

    private const OTHER_DOG_NAMES = ['Lucky', 'Sadie', 'Toby', 'Ruby', 'Jack', 'Maggie', 'Bear', 'Penny', 'Oscar', 'Lola', 'Finn', 'Honey'];

    private const OTHER_CAT_NAMES = ['Felix', 'Kitty', 'Leo', 'Chloe', 'Jasper', 'Lily', 'Ziggy', 'Mittens', 'Salem', 'Ivy'];

    private const DESCRIPTIONS = [
        '<p>Super sweet and playful, loves long walks and cuddles on the sofa. Gets along well with other animals.</p>',
        '<p>A little shy at first, but once they trust you they become the most loyal companion in the world.</p>',
        '<p>Full of energy! Ideal for an active family that enjoys running and playing outdoors.</p>',
        '<p>Calm and very affectionate. Perfect for an apartment and for anyone looking for a quiet friend.</p>',
        '<p>Loves children and squeaky toys. Already <b>vaccinated</b>, <b>dewormed</b> and <b>microchipped</b>.</p>',
        '<p>Survived life on the street and still trusts people. Truly deserves a second chance.</p>',
    ];

    /**
     * Volunteers of the main shelter: [name, gender, profession, city, transport, attendance, performance, portrait].
     *
     * @var list<array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string, 7: string}>
     */
    private const VOLUNTEERS = [
        ['Emma Carter', 'female', 'Veterinary Nurse', 'Lisbon', 'own vehicule', 'excellent', 'excellent', 'women/44'],
        ['Liam Walker', 'male', 'Software Engineer', 'Lisbon', 'public transportation', 'high', 'very high', 'men/32'],
        ['Sofia Martins', 'female', 'Student', 'Amadora', 'public transportation', 'regular', 'high', 'women/65'],
        ['Noah Bennett', 'male', 'Photographer', 'Oeiras', 'bycicle', 'high', 'high', 'men/75'],
        ['Olivia Hughes', 'female', 'Teacher', 'Lisbon', 'foot', 'very high', 'excellent', 'women/21'],
        ['Lucas Silva', 'male', 'Retired', 'Sintra', 'own vehicule', 'excellent', 'very high', 'men/85'],
        ['Mia Thompson', 'female', 'Graphic Designer', 'Cascais', 'public transportation', 'low', 'regular', 'women/12'],
        ['Ethan Reed', 'male', 'Nurse', 'Lisbon', 'foot', 'regular', 'regular', 'men/41'],
    ];

    /**
     * @var array<string, int>
     */
    private array $species = [];

    /**
     * @var array<string, array<string, int>>
     */
    private array $breeds = [];

    /**
     * @var array<string, int>
     */
    private array $colors = [];

    /**
     * @var array<string, int>
     */
    private array $furTypes = [];

    /**
     * @var array<string, int>
     */
    private array $sizes = [];

    public function run(): void
    {
        if (Species::query()->withTrashed()->exists()) {
            $this->command?->error('The database is not empty — run: php artisan migrate:fresh --seeder=DocumentationDemoSeeder');

            return;
        }

        $this->seedLookups();

        $shelters = $this->seedShelters();
        $mainShelter = $shelters->first();

        $this->seedUsers($shelters);

        $mainPets = $this->seedMainShelterPets($mainShelter);
        $this->seedOtherShelterPets($shelters->slice(1));

        $shelters->each(fn (Shelter $shelter) => $this->seedFacilities($shelter));

        $this->seedVaccinations($mainPets);
        $this->seedSicknesses($mainPets);
        $this->seedAdoptions($mainPets);
        $this->seedAdoptionApplications($mainPets);
        $this->seedSponsorships($mainPets);
        $this->seedVolunteers($mainShelter);
        $this->seedFosterFamilies($mainShelter, $mainPets);
        $this->seedMembers($mainShelter);
    }

    private function seedLookups(): void
    {
        $dog = Species::query()->create(['name' => 'Dog', 'name_plural' => 'Dogs', 'has_pure_breed_field' => true]);
        $cat = Species::query()->create(['name' => 'Cat', 'name_plural' => 'Cats', 'has_pure_breed_field' => true]);
        $this->species = ['dog' => $dog->id, 'cat' => $cat->id];

        foreach (['dog' => self::DOG_BREEDS, 'cat' => self::CAT_BREEDS] as $speciesKey => $breeds) {
            foreach (array_keys($breeds) as $index => $breedName) {
                $this->breeds[$speciesKey][$breedName] = Breed::query()->create([
                    'species_id' => $this->species[$speciesKey],
                    'name' => $breedName,
                    'is_default' => $index === 0,
                ])->id;
            }
        }

        $this->colors = collect(self::COLORS)->mapWithKeys(fn (string $name) => [$name => Color::query()->create(['name' => $name])->id])->all();
        $this->furTypes = collect(self::FUR_TYPES)->mapWithKeys(fn (string $name) => [$name => FurType::query()->create(['name' => $name])->id])->all();
        $this->sizes = collect(self::DOG_SIZES)->mapWithKeys(fn (string $name) => [$name => Size::query()->create(['species_id' => $dog->id, 'name' => $name])->id])->all();

        $vaccines = [
            'Rabies' => ['dog', 'cat'],
            'DHPP (Distemper, Hepatitis, Parvovirus, Parainfluenza)' => ['dog'],
            'Leptospirosis' => ['dog'],
            'Kennel Cough (Bordetella)' => ['dog'],
            'FVRCP (Feline Rhinotracheitis, Calicivirus, Panleukopenia)' => ['cat'],
            'FeLV (Feline Leukemia)' => ['cat'],
        ];

        foreach ($vaccines as $name => $speciesKeys) {
            Vaccine::query()->create(['name' => $name])->species()->attach(array_map(fn ($key) => $this->species[$key], $speciesKeys));
        }

        $sicknesses = [
            'Parvovirus' => ['Highly contagious viral infection that attacks the gastrointestinal tract of dogs.', ['dog']],
            'Canine Distemper' => ['Serious viral disease affecting the respiratory and nervous systems of dogs.', ['dog']],
            'Leishmaniasis' => ['Chronic parasitic disease transmitted by sandflies.', ['dog']],
            'Cat Flu' => ['Highly contagious feline upper respiratory complex.', ['cat']],
            'FIV (Feline Immunodeficiency Virus)' => ['Lifelong viral infection that weakens the immune system of cats.', ['cat']],
            'Otitis' => ['Inflammation of the ear canal, usually bacterial or fungal.', ['dog', 'cat']],
        ];

        foreach ($sicknesses as $name => [$description, $speciesKeys]) {
            Sickness::query()->create(['name' => $name, 'description' => $description])->species()->attach(array_map(fn ($key) => $this->species[$key], $speciesKeys));
        }

        foreach (self::ACTIVITIES as $activity) {
            Activity::query()->create(['name' => $activity]);
        }

        foreach (self::REGIONS as $region) {
            Region::query()->create(['name' => $region]);
        }
    }

    /**
     * @return Collection<int, Shelter>
     */
    private function seedShelters(): Collection
    {
        return collect(self::SHELTERS)->map(function (array $attributes, string $name): Shelter {
            $shelter = Shelter::query()->create([
                'name' => $name,
                ...collect($attributes)->except('region')->all(),
                'region_id' => Region::query()->where('name', $attributes['region'])->value('id'),
            ]);

            $shelter->species()->attach(array_values($this->species));

            return $shelter;
        })->values();
    }

    /**
     * @param  Collection<int, Shelter>  $shelters
     */
    private function seedUsers(Collection $shelters): void
    {
        $this->createUser('Alex Morgan', 'admin@example.com', null, isAdmin: true);

        $users = [
            ['Sarah Johnson', 'manager@example.com', 0, 'manager'],
            ['David Miller', 'staff@example.com', 0, 'staff'],
            ['Laura Costa', 'laura.costa@example.com', 0, 'staff'],
            ['James Wilson', 'james.wilson@example.com', 0, 'staff'],
            ['Emma Reed', 'viewer@example.com', 0, 'viewer'],
            ['Ana Ferreira', 'ana.ferreira@example.com', 1, 'manager'],
            ['Tom Harris', 'tom.harris@example.com', 1, 'staff'],
            ['Rita Sousa', 'rita.sousa@example.com', 2, 'manager'],
            ['Peter Clark', 'peter.clark@example.com', 3, 'manager'],
        ];

        foreach ($users as [$name, $email, $shelterIndex, $role]) {
            $shelter = $shelters[$shelterIndex];
            $user = $this->createUser($name, $email, $shelter->id);

            DB::table('shelter_users')->insert([
                'shelter_id' => $shelter->id,
                'user_id' => $user->id,
                'role' => $role,
                'vaccination_notifications' => $role === 'manager',
                'adoption_application_notifications' => $role === 'manager',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createUser(string $name, string $email, ?int $shelterId, bool $isAdmin = false): User
    {
        $user = new User;
        $user->forceFill([
            'is_admin' => $isAdmin,
            'current_shelter_id' => $shelterId,
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'last_login' => now()->subHours(random_int(1, 72)),
        ])->save();

        return $user;
    }

    /**
     * @return Collection<string, Pet>
     */
    private function seedMainShelterPets(Shelter $shelter): Collection
    {
        return collect(self::MAIN_PETS)->mapWithKeys(function (array $data, int $index) use ($shelter): array {
            [$name, $speciesKey, $breed, $gender, $ageInMonths, $status, $color, $furType, $size] = $data;

            $pet = $this->createPet($shelter, $speciesKey, $breed, [
                'name' => $name,
                'gender' => $gender,
                'birth_date' => now()->subMonths($ageInMonths)->subDays(random_int(0, 25)),
                'status' => $status,
                'is_adoptable' => $status !== 'not_available',
                'is_pure_breed' => ! in_array($breed, ['Mixed Breed', 'Domestic Shorthair'], true),
                'is_featured' => in_array($name, ['Max', 'Luna', 'Cleo', 'Simba'], true),
                'publish_to_portal' => $status === 'available',
                'primary_color_id' => $this->colors[$color],
                'secondary_color_id' => $index % 4 === 0 ? $this->colors['White'] : null,
                'fur_type_id' => $this->furTypes[$furType],
                'size_id' => $size ? $this->sizes[$size] : null,
                'chip' => $index % 5 === 4 ? null : '620'.str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT),
                'is_neutered' => $ageInMonths > 8 && $index % 6 !== 5,
                'checkin_date' => now()->subDays(random_int(10, 500)),
                'date_of_death' => $status === 'deceased' ? now()->subDays(20) : null,
                'notes' => $status === 'not_available' ? 'Under behavioural assessment before being listed for adoption.' : null,
                'internal_notes' => $index % 3 === 0 ? 'Needs to be walked separately from other males.' : null,
                'clinical_notes' => $index % 4 === 1 ? 'Sensitive stomach — hypoallergenic diet only.' : null,
                'view_count' => random_int(15, 900),
            ]);

            return [$name => $pet];
        });
    }

    /**
     * @param  Collection<int, Shelter>  $shelters
     */
    private function seedOtherShelterPets(Collection $shelters): void
    {
        $dogCounter = 0;
        $catCounter = 0;

        foreach ($shelters as $shelter) {
            foreach (range(0, 6) as $slot) {
                $isDog = $slot % 3 !== 2;
                $breeds = $isDog ? array_keys(self::DOG_BREEDS) : array_keys(self::CAT_BREEDS);
                $counter = $isDog ? $dogCounter++ : $catCounter++;

                $this->createPet($shelter, $isDog ? 'dog' : 'cat', $breeds[$counter % count($breeds)], [
                    'name' => $isDog ? self::OTHER_DOG_NAMES[$counter % count(self::OTHER_DOG_NAMES)] : self::OTHER_CAT_NAMES[$counter % count(self::OTHER_CAT_NAMES)],
                    'gender' => $slot % 2 === 0 ? 'male' : 'female',
                    'birth_date' => now()->subMonths(random_int(3, 110)),
                    'status' => 'available',
                    'is_adoptable' => true,
                    'is_featured' => $slot === 0,
                    'publish_to_portal' => true,
                    'primary_color_id' => array_values($this->colors)[$counter % count($this->colors)],
                    'fur_type_id' => array_values($this->furTypes)[$counter % 3],
                    'size_id' => $isDog ? array_values($this->sizes)[$counter % count($this->sizes)] : null,
                    'is_neutered' => $slot !== 1,
                    'checkin_date' => now()->subDays(random_int(5, 300)),
                    'view_count' => random_int(5, 400),
                ]);
            }
        }
    }

    /**
     * Create a pet with a generated ref, a description and a downloaded main photo.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function createPet(Shelter $shelter, string $speciesKey, string $breed, array $attributes): Pet
    {
        $viewCount = $attributes['view_count'];
        unset($attributes['view_count']);

        $pet = Pet::query()->create([
            'shelter_id' => $shelter->id,
            'species_id' => $this->species[$speciesKey],
            'breed_id' => $this->breeds[$speciesKey][$breed],
            'ref' => '',
            'description' => self::DESCRIPTIONS[array_rand(self::DESCRIPTIONS)],
            'is_sponsorable' => true,
            ...$attributes,
        ]);

        $pet->forceFill(['ref' => 'PET'.str_pad((string) $pet->id, 5, '0', STR_PAD_LEFT), 'view_count' => $viewCount])->save();

        $imagePath = null;

        for ($attempt = 0; $attempt < 4 && $imagePath === null; $attempt++) {
            $imagePath = $speciesKey === 'dog'
                ? $this->downloadDogPhoto(self::DOG_BREEDS[$breed], $pet)
                : $this->downloadCatPhoto(self::CAT_BREEDS[$breed], $pet);
        }

        if ($imagePath !== null) {
            PetImage::query()->create(['pet_id' => $pet->id, 'image_path' => $imagePath, 'is_main' => true]);
        }

        $this->command?->info("  {$pet->ref} {$pet->name} ({$shelter->short_name})".($imagePath ? ' 📷' : ''));

        return $pet;
    }

    /**
     * Give the shelter a main building (dog and cat wings) plus a clinic with a
     * quarantine wing, and house every pet still at the shelter in a cage.
     */
    private function seedFacilities(Shelter $shelter): void
    {
        $mainBuilding = Facility::query()->create([
            'shelter_id' => $shelter->id,
            'name' => 'Main Building',
            'address' => $shelter->address,
            'postal_code' => $shelter->postal_code,
            'city' => $shelter->city,
        ]);

        $clinic = Facility::query()->create([
            'shelter_id' => $shelter->id,
            'name' => 'Veterinary Clinic',
            'address' => $shelter->address,
            'postal_code' => $shelter->postal_code,
            'city' => $shelter->city,
            'notes' => 'Quarantine and recovery area for newly arrived or sick animals.',
        ]);

        $wings = [
            [$mainBuilding, 'Dog Wing A', 'Kennels for large and medium dogs, with direct access to the exercise yard.', 'dog', 'A', 6, 2],
            [$mainBuilding, 'Dog Wing B', 'Kennels for small dogs and puppies.', 'dog', 'B', 4, 2],
            [$mainBuilding, 'Cattery', 'Heated cat rooms with climbing structures.', 'cat', 'C', 6, 3],
            [$clinic, 'Quarantine', 'Isolation cages for new arrivals during their first 15 days.', 'dog', 'Q', 3, 1],
        ];

        $cagesBySpecies = collect();

        foreach ($wings as [$facility, $name, $description, $speciesKey, $prefix, $cageCount, $capacity]) {
            $wing = Wing::query()->create(['facility_id' => $facility->id, 'name' => $name, 'description' => $description]);

            foreach (range(1, $cageCount) as $number) {
                $cagesBySpecies->push(Cage::query()->create([
                    'wing_id' => $wing->id,
                    'species_id' => $this->species[$speciesKey],
                    'code' => $prefix.'-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                    'capacity' => $capacity,
                ]));
            }
        }

        $occupancy = [];

        Pet::query()->where('shelter_id', $shelter->id)->whereIn('status', ['available', 'not_available'])->each(function (Pet $pet) use ($cagesBySpecies, &$occupancy): void {
            $cage = $cagesBySpecies->first(fn (Cage $cage) => $cage->species_id === $pet->species_id && ($occupancy[$cage->id] ?? 0) < $cage->capacity);

            if ($cage !== null) {
                $pet->update(['cage_id' => $cage->id]);
                $occupancy[$cage->id] = ($occupancy[$cage->id] ?? 0) + 1;
            }
        });
    }

    /**
     * Administered, upcoming, overdue and canceled vaccinations for the main shelter's pets.
     *
     * @param  Collection<string, Pet>  $pets
     */
    private function seedVaccinations(Collection $pets): void
    {
        $vaccinesBySpecies = Vaccine::query()->with('species')->get()
            ->flatMap(fn (Vaccine $vaccine) => $vaccine->species->map(fn (Species $species) => [$species->id, $vaccine->id]))
            ->groupBy(0)
            ->map(fn (Collection $pairs) => $pairs->pluck(1)->all());

        $veterinarians = ['Dr. Helen Brooks', 'Dr. Miguel Santos', 'Dr. Rachel Green'];
        $index = 0;

        foreach ($pets->where('status', '!=', 'deceased') as $pet) {
            foreach ($vaccinesBySpecies[$pet->species_id] as $vaccineIndex => $vaccineId) {
                $bucket = ($index++ + $vaccineIndex) % 6;

                [$status, $administeredDate, $dueDate] = match ($bucket) {
                    0, 1, 2 => ['administered', now()->subDays(random_int(30, 330)), null],
                    3 => ['scheduled', null, now()->addDays(random_int(1, 7))],
                    4 => ['scheduled', null, now()->addDays(random_int(10, 60))],
                    default => $index % 4 === 0
                        ? ['canceled', null, now()->subDays(random_int(5, 40))]
                        : ['scheduled', null, now()->subDays(random_int(1, 14))],
                };

                PetVaccine::query()->create([
                    'pet_id' => $pet->id,
                    'vaccine_id' => $vaccineId,
                    'status' => $status,
                    'administered_date' => $administeredDate,
                    'due_date' => $dueDate,
                    'lot_number' => $status === 'administered' ? strtoupper(Str::random(2)).random_int(10000, 99999) : null,
                    'veterinarian_name' => $status === 'administered' ? $veterinarians[$index % 3] : null,
                ]);
            }
        }
    }

    /**
     * @param  Collection<string, Pet>  $pets
     */
    private function seedSicknesses(Collection $pets): void
    {
        $sicknesses = Sickness::query()->pluck('id', 'name');

        $records = [
            ['Buddy', 'Leishmaniasis', 'chronic', 200, 'Allopurinol daily. Blood test every 6 months.'],
            ['Rocky', 'Otitis', 'treated', 90, 'Ear drops for 10 days. Fully recovered.'],
            ['Coco', 'Parvovirus', 'active', 5, 'IV fluids and antiemetics. Kept in quarantine.'],
            ['Shadow', 'FIV (Feline Immunodeficiency Virus)', 'chronic', 300, 'Indoor only. Regular check-ups.'],
            ['Pepper', 'Cat Flu', 'active', 3, 'Antibiotics and eye drops twice a day.'],
            ['Misty', 'Otitis', 'treated', 45, 'Cleaned and treated with topical ointment.'],
        ];

        foreach ($records as [$petName, $sicknessName, $status, $daysAgo, $notes]) {
            $pets[$petName]->sicknesses()->attach($sicknesses[$sicknessName], [
                'diagnosed_at' => now()->subDays($daysAgo)->toDateString(),
                'status' => $status,
                'treatment_notes' => $notes,
            ]);
        }
    }

    /**
     * @param  Collection<string, Pet>  $pets
     */
    private function seedAdoptions(Collection $pets): void
    {
        $adoptions = [
            ['Duke', 'Michael Brown', 'Lisbon', 40, null, 'Approved', 75],
            ['Bruno', 'Catherine Lee', 'Almada', 120, null, 'Approved', 50],
            ['Tiger', 'Robert Davies', 'Cascais', 15, null, 'Approved', 60],
            ['Zeus', 'Hannah White', 'Lisbon', 200, 150, 'Approved', 75],
            ['Max', 'Daniel Young', 'Oeiras', 2, null, 'Pending', 75],
            ['Cleo', 'Grace Turner', 'Lisbon', 1, null, 'Pending', 60],
            ['Molly', 'Kevin Scott', 'Sintra', 10, null, 'Rejected', 75],
        ];

        foreach ($adoptions as [$petName, $name, $city, $daysAgo, $returnedDaysAgo, $applicationStatus, $fee]) {
            Adoption::query()->create([
                'pet_id' => $pets[$petName]->id,
                'name' => $name,
                'email' => Str::slug($name, '.').'@example.com',
                'phone' => '+351 91'.random_int(1000000, 9999999),
                'address' => 'Rua '.fake()->lastName().', '.random_int(1, 200),
                'postal_code' => random_int(1000, 2999).'-'.random_int(100, 999),
                'city' => $city,
                'adoption_date' => now()->subDays($daysAgo)->toDateString(),
                'return_date' => $returnedDaysAgo ? now()->subDays($returnedDaysAgo)->toDateString() : null,
                'adoption_fee' => $fee,
                'application_status' => $applicationStatus,
                'notes' => $returnedDaysAgo ? 'Returned: the family moved abroad.' : null,
            ]);

            if ($applicationStatus === 'Approved' && $returnedDaysAgo === null) {
                $pets[$petName]->update(['checkout_date' => now()->subDays($daysAgo)->toDateString()]);
            }
        }
    }

    /**
     * Applications sent from the public portal: pending ones, one approved
     * (linked to its adoption), one rejected, and one still pending for a pet
     * that has since been adopted, so the "reject them" banner shows.
     *
     * @param  Collection<string, Pet>  $pets
     */
    private function seedAdoptionApplications(Collection $pets): void
    {
        $reviewerId = User::query()->where('email', 'manager@example.com')->value('id');

        // [pet, name, city, housing, garden, children, other animals, message, status, days ago]
        $applications = [
            ['Charlie', 'Sophie Martin', 'Lisbon', 'house', true, true, 'A 5-year-old cat', 'We have a big garden and two kids who have been asking for a dog for years. We walk every weekend.', 'pending', 1],
            ['Luna', 'Miguel Santos', 'Amadora', 'apartment', false, false, null, 'I work from home and have time for a puppy. I have had dogs all my life.', 'pending', 2],
            ['Luna', 'Claire Dubois', 'Oeiras', 'house', true, false, 'An older Labrador', 'Our Labrador would love a young friend. We live near the beach.', 'pending', 4],
            ['Duke', 'Paulo Ramos', 'Setúbal', 'house', true, true, null, 'Duke looks like the dog we lost last year. We would give him a lot of love.', 'pending', 20],
            ['Bruno', 'Catherine Lee', 'Almada', 'house', true, false, null, 'Bruno is exactly the calm companion I am looking for.', 'approved', 125],
            ['Molly', 'Kevin Scott', 'Sintra', 'apartment', false, false, 'Two cats', 'I would like to adopt Molly to keep my cats company.', 'rejected', 12],
        ];

        foreach ($applications as [$petName, $name, $city, $housing, $garden, $children, $otherAnimals, $message, $status, $daysAgo]) {
            $sentAt = now()->subDays($daysAgo)->setTime(random_int(9, 21), random_int(0, 59));

            $application = AdoptionApplication::query()->create([
                'pet_id' => $pets[$petName]->id,
                'name' => $name,
                'email' => Str::slug($name, '.').'@example.com',
                'phone' => '+351 92'.random_int(1000000, 9999999),
                'postal_code' => random_int(1000, 2999).'-'.random_int(100, 999),
                'city' => $city,
                'housing_type' => $housing,
                'has_garden' => $garden,
                'has_children' => $children,
                'other_animals' => $otherAnimals,
                'message' => $message,
                'status' => $status,
                'consent_at' => $sentAt,
                'adoption_id' => $status === 'approved' ? Adoption::query()->where('pet_id', $pets[$petName]->id)->value('id') : null,
                'reviewed_by' => $status === 'pending' ? null : $reviewerId,
                'reviewed_at' => $status === 'pending' ? null : $sentAt->copy()->addDays(2),
            ]);

            $application->forceFill(['created_at' => $sentAt, 'updated_at' => $sentAt])->saveQuietly();
        }
    }

    /**
     * A wing of foster families, as shelters like Faial run it: each cage is
     * one family, linked to a volunteer as its contact, with animals placed
     * in it like in any other cage.
     *
     * @param  Collection<string, Pet>  $pets
     */
    private function seedFosterFamilies(Shelter $shelter, Collection $pets): void
    {
        $facility = Facility::query()->create([
            'shelter_id' => $shelter->id,
            'name' => 'Foster homes',
            'city' => $shelter->city,
            'notes' => 'Families who look after animals at home until they are adopted.',
        ]);

        $wing = Wing::query()->create([
            'facility_id' => $facility->id,
            'name' => 'Foster families',
            'description' => 'Each cage is one foster family.',
            'is_foster' => true,
        ]);

        foreach ([['Carter family', 'Emma Carter', 2, 'Coco'], ['Silva family', 'Lucas Silva', 1, 'Nala']] as [$family, $volunteerName, $capacity, $petName]) {
            $cage = Cage::query()->create([
                'wing_id' => $wing->id,
                'volunteer_id' => Volunteer::query()->where('shelter_id', $shelter->id)->where('name', $volunteerName)->value('id'),
                'code' => $family,
                'capacity' => $capacity,
            ]);

            $pets[$petName]->update(['cage_id' => $cage->id]);
        }
    }

    /**
     * @param  Collection<string, Pet>  $pets
     */
    private function seedSponsorships(Collection $pets): void
    {
        $sponsorships = [
            ['Buddy', 'Jennifer Adams', 'Lisbon', 10, 15],
            ['Rocky', 'Acme Software Lda.', 'Porto', 6, 25],
            ['Shadow', 'Paul Roberts', 'Setúbal', 12, 10],
            ['Simba', 'Emily Clarke', 'Lisbon', 3, 20],
            ['Bella', 'Marco Rossi', 'Braga', 1, 15],
        ];

        foreach ($sponsorships as [$petName, $name, $city, $months, $value]) {
            $sponsorship = Sponsorship::query()->create([
                'pet_id' => $pets[$petName]->id,
                'name' => $name,
                'email' => Str::slug($name, '.').'@example.com',
                'phone' => '+351 93'.random_int(1000000, 9999999),
                'city' => $city,
                'send_feedback' => true,
                'send_newsletter' => $months % 2 === 0,
            ]);

            foreach (range($months - 1, 0) as $monthsAgo) {
                $start = now()->subMonths($monthsAgo)->startOfMonth();

                SponsorshipPayment::query()->create([
                    'sponsorship_id' => $sponsorship->id,
                    'start_date' => $start->toDateString(),
                    'end_date' => $start->copy()->endOfMonth()->toDateString(),
                    'payment_date' => $start->copy()->addDays(random_int(0, 5))->toDateString(),
                    'payment_value' => $value,
                ]);
            }
        }
    }

    private function seedVolunteers(Shelter $shelter): void
    {
        $activityIds = Activity::query()->pluck('id')->all();

        foreach (self::VOLUNTEERS as $index => [$name, $gender, $profession, $city, $transport, $attendance, $performance, $portrait]) {
            $volunteer = Volunteer::query()->create([
                'shelter_id' => $shelter->id,
                'name' => $name,
                'gender' => $gender,
                'birth_date' => now()->subYears(random_int(19, 68))->subDays(random_int(0, 360)),
                'professional_activity' => $profession,
                'email' => Str::slug($name, '.').'@example.com',
                'phone' => '+351 96'.random_int(1000000, 9999999),
                'address' => 'Avenida '.fake()->lastName().', '.random_int(1, 300),
                'postal_code' => '1'.random_int(100, 999).'-'.random_int(100, 999),
                'city' => $city,
                'transport_mode' => $transport,
                'attendance_evaluation' => $attendance,
                'performance_evaluation' => $performance,
                'start_date' => now()->subDays(random_int(30, 1500)),
                'end_date' => $index === 7 ? now()->subDays(20) : null,
                'send_newsletter' => $index % 2 === 0,
                'image_path' => $this->downloadPortrait($portrait),
            ]);

            $volunteer->activities()->attach(collect($activityIds)->shuffle()->take(random_int(2, 4))->all());
            $volunteer->species()->attach($index % 3 === 0 ? array_values($this->species) : [$this->species[$index % 2 === 0 ? 'dog' : 'cat']]);

            foreach (collect(range(0, 6))->shuffle()->take(random_int(1, 3)) as $dayIndex) {
                VolunteerAvailability::query()->create([
                    'volunteer_id' => $volunteer->id,
                    'day_index' => $dayIndex,
                    'frequency' => ['weekly', 'biweekly', 'occasionally'][random_int(0, 2)],
                    'mornings' => (bool) random_int(0, 1),
                    'afternoons' => true,
                ]);
            }
        }
    }

    /**
     * Members of the main shelter's association, with the shelter's default fees
     * (10 joining fee, 24 yearly) and a mix of frequencies, statuses and arrears.
     */
    private function seedMembers(Shelter $shelter): void
    {
        $shelter->update(['joining_fee' => 10, 'membership_fee' => 24, 'membership_fee_frequency' => 'yearly']);

        // [name, city, joined months ago, fee, frequency, status, months left unpaid, joining fee paid]
        $members = [
            ['Helen Morris', 'Lisbon', 40, 24, 'yearly', 'active', 0, true],
            ['George Wilson', 'Porto', 30, 2, 'monthly', 'active', 0, true],
            ['Emma Carter', 'Lisbon', 26, 24, 'yearly', 'active', 0, true],
            ['Ana Ferreira', 'Sintra', 22, 6, 'quarterly', 'active', 0, true],
            ['Daniel Brooks', 'Cascais', 18, 12, 'semiannual', 'active', 7, true],
            ['Rita Almeida', 'Amadora', 14, 24, 'yearly', 'active', 0, true],
            ['Thomas Green', 'Oeiras', 9, 2, 'monthly', 'active', 3, true],
            ['Laura Pinto', 'Lisbon', 5, 24, 'yearly', 'active', 0, false],
            ['Peter Collins', 'Setúbal', 36, 24, 'yearly', 'suspended', 14, true],
            ['Maria Lopes', 'Braga', 48, 24, 'yearly', 'left', 20, true],
            ['Sam Turner', 'Lisbon', 1, 0, 'yearly', 'active', 0, false],
        ];

        $volunteerIds = Volunteer::query()->where('shelter_id', $shelter->id)->pluck('id', 'name');

        foreach ($members as [$name, $city, $joinedMonthsAgo, $fee, $frequency, $status, $monthsUnpaid, $joiningFeePaid]) {
            $joinDate = now()->subMonths($joinedMonthsAgo)->startOfMonth()->addDays(random_int(0, 20));

            $member = Member::query()->create([
                'shelter_id' => $shelter->id,
                'volunteer_id' => $volunteerIds[$name] ?? null,
                'name' => $name,
                'tin' => (string) random_int(100000000, 299999999),
                'email' => Str::slug($name, '.').'@example.com',
                'phone' => '+351 91'.random_int(1000000, 9999999),
                'address' => 'Rua '.fake()->lastName().', '.random_int(1, 200),
                'postal_code' => '1'.random_int(100, 999).'-'.random_int(100, 999),
                'city' => $city,
                'join_date' => $joinDate->toDateString(),
                'status' => $status,
                'joining_fee' => $name === 'Sam Turner' ? 0 : 10,
                'membership_fee' => $fee,
                'membership_fee_frequency' => $frequency,
            ]);

            if ($joiningFeePaid) {
                MemberPayment::query()->create([
                    'member_id' => $member->id,
                    'type' => 'joining_fee',
                    'payment_date' => $joinDate->toDateString(),
                    'payment_value' => 10,
                    'payment_method' => 'cash',
                ]);
            }

            if ($fee <= 0) {
                continue;
            }

            // Pay period after period from the join date, stopping $monthsUnpaid months before today.
            $paidUntilLimit = now()->subMonths($monthsUnpaid);

            while (true) {
                [$start, $end] = $member->nextFeePeriod();

                if ($start->isAfter($paidUntilLimit)) {
                    break;
                }

                MemberPayment::query()->create([
                    'member_id' => $member->id,
                    'type' => 'membership_fee',
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'payment_date' => $start->copy()->addDays(random_int(0, 10))->toDateString(),
                    'payment_value' => $fee,
                    'payment_method' => ['bank_transfer', 'mobile', 'cash', 'bank_transfer'][random_int(0, 3)],
                ]);
            }
        }
    }

    private function downloadDogPhoto(string $breedPath, Pet $pet): ?string
    {
        try {
            $url = Http::timeout(15)->get("https://dog.ceo/api/breed/{$breedPath}/images/random")->json('message');

            return is_string($url) ? $this->storePhoto($url, 'pets/demo-'.Str::slug($pet->name).'-'.$pet->id.'.jpg') : null;
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

            return is_string($url) ? $this->storePhoto($url, 'pets/demo-'.Str::slug($pet->name).'-'.$pet->id.'.jpg') : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function downloadPortrait(string $portrait): ?string
    {
        try {
            return $this->storePhoto("https://randomuser.me/api/portraits/{$portrait}.jpg", 'volunteers/demo-'.Str::slug($portrait).'.jpg');
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Download the photo, shrink it to at most 1000px wide and store it on the public disk.
     */
    private function storePhoto(string $url, string $path): ?string
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

        Storage::disk('public')->put($path, $contents);

        return $path;
    }
}

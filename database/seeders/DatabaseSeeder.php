<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEED SPECIES (Espécies)
        $dogId = DB::table('species')->insertGetId(['name' => 'Cão', 'name_plural' => 'Cães', 'created_at' => now(), 'updated_at' => now()]);
        $catId = DB::table('species')->insertGetId(['name' => 'Gato', 'name_plural' => 'Gatos', 'created_at' => now(), 'updated_at' => now()]);

        // 2. SEED BREEDS (Raças)
        // Defaults: Cão Rafeiro (dogs), Europeu Comum (cats)
        $this->call([DogBreedSeeder::class, CatBreedSeeder::class]);

        // 3. SEED COLORS (Cores) & 4. FUR TYPES (Tipos de Pelo)
        $this->call([ColorSeeder::class, FurTypeSeeder::class]);

        // Common Dog Sizes
        $this->call(DogSizeSeeder::class);

        // 5. SEED VACCINES (Vacinas) & LINK TO SPECIES
        $vacRaiva = DB::table('vaccines')->insertGetId(['name' => 'Antirrábica (Raiva)', 'created_at' => now(), 'updated_at' => now()]);
        $vacPolivalenteCao = DB::table('vaccines')->insertGetId(['name' => 'Polivalente Canina (DHPPi/L)', 'created_at' => now(), 'updated_at' => now()]);
        $vacTripliceFelina = DB::table('vaccines')->insertGetId(['name' => 'Tríplice Felina (FVRCP)', 'created_at' => now(), 'updated_at' => now()]);
        $vacLeucemiaFelina = DB::table('vaccines')->insertGetId(['name' => 'Leucemia Felina (FeLV)', 'created_at' => now(), 'updated_at' => now()]);

        // Pivot relationships for Vaccines
        DB::table('vaccine_species')->insert([
            ['vaccine_id' => $vacRaiva, 'species_id' => $dogId, 'created_at' => now(), 'updated_at' => now()],
            ['vaccine_id' => $vacRaiva, 'species_id' => $catId, 'created_at' => now(), 'updated_at' => now()],
            ['vaccine_id' => $vacPolivalenteCao, 'species_id' => $dogId, 'created_at' => now(), 'updated_at' => now()],
            ['vaccine_id' => $vacTripliceFelina, 'species_id' => $catId, 'created_at' => now(), 'updated_at' => now()],
            ['vaccine_id' => $vacLeucemiaFelina, 'species_id' => $catId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. SEED SICKNESSES (Doenças) & LINK TO SPECIES
        $doencaParvovirose = DB::table('sicknesses')->insertGetId(['name' => 'Parvovirose', 'description' => 'Infeção viral altamente contagiosa em cães.', 'created_at' => now(), 'updated_at' => now()]);
        $doencaEsgana = DB::table('sicknesses')->insertGetId(['name' => 'Esgana', 'description' => 'Doença viral grave que afeta os sistemas respiratório e nervoso dos cães.', 'created_at' => now(), 'updated_at' => now()]);
        $doencaLeishmaniose = DB::table('sicknesses')->insertGetId(['name' => 'Leishmaniose', 'description' => 'Doença parasitária crónica transmitida por insetos.', 'created_at' => now(), 'updated_at' => now()]);
        $doencaCoryza = DB::table('sicknesses')->insertGetId(['name' => 'Coriza Felina', 'description' => 'Complexo respiratório felino altamente contagioso (gripe do gato).', 'created_at' => now(), 'updated_at' => now()]);
        $doencaFIV = DB::table('sicknesses')->insertGetId(['name' => 'FIV (Sida Felina)', 'description' => 'Vírus da Imunodeficiência Felina.', 'created_at' => now(), 'updated_at' => now()]);
        $doencaFeLV = DB::table('sicknesses')->insertGetId(['name' => 'FeLV (Leucemia Felina)', 'description' => 'Vírus da Leucemia Felina.', 'created_at' => now(), 'updated_at' => now()]);

        // Pivot relationships for Sicknesses
        DB::table('sickness_species')->insert([
            ['sickness_id' => $doencaParvovirose, 'species_id' => $dogId, 'created_at' => now(), 'updated_at' => now()],
            ['sickness_id' => $doencaEsgana, 'species_id' => $dogId, 'created_at' => now(), 'updated_at' => now()],
            ['sickness_id' => $doencaLeishmaniose, 'species_id' => $dogId, 'created_at' => now(), 'updated_at' => now()],
            ['sickness_id' => $doencaCoryza, 'species_id' => $catId, 'created_at' => now(), 'updated_at' => now()],
            ['sickness_id' => $doencaFIV, 'species_id' => $catId, 'created_at' => now(), 'updated_at' => now()],
            ['sickness_id' => $doencaFeLV, 'species_id' => $catId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('activities')->insert([
            ['name' => 'Apoio na Enfermaria', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tratamento e Cuidados aos Animais', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Passeios no Exterior', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Limpeza de Boxes', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'FAT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Participação em Campanhas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transporte de Animais', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tarefas na Área Profissional', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suporte Administrativo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Operador de Dados em Sistema Informático', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Captura de Animais', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 7. SEED GLOBAL SYSTEM ADMIN USER (no shelter membership)
        DB::table('users')->insert([
            'is_admin' => true,
            'current_shelter_id' => null,
            'name' => 'Administrador Acolhe',
            'email' => 'admin@acolhe.pt',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 8. SEED REGIONS (Distritos)
        $regions = [
            'Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra', 'Évora', 'Faro', 'Guarda',
            'Leiria', 'Lisboa', 'Portalegre', 'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real', 'Viseu',
            'Açores - Faial', 'Açores - Santa Maria',
        ];
        foreach ($regions as $region) {
            DB::table('regions')->insert(['name' => $region, 'created_at' => now(), 'updated_at' => now()]);
        }

        // 9. SEED SYSTEM MANAGER SHELTER & USER
        $adminShelterId = DB::table('shelters')->insertGetId([
            'name' => 'Acolhe Central',
            'city' => 'Porto',
            'region_id' => DB::table('regions')->where('name', 'Porto')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shelter_species')->insert([
            ['shelter_id' => $adminShelterId, 'species_id' => $dogId, 'created_at' => now(), 'updated_at' => now()],
            ['shelter_id' => $adminShelterId, 'species_id' => $catId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $managerId = DB::table('users')->insertGetId([
            'is_admin' => false,
            'current_shelter_id' => $adminShelterId,
            'name' => 'Gestor Acolhe',
            'email' => 'manager@acolhe.pt',
            'password' => bcrypt('password'), // Native secure hashing
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shelter_users')->insert([
            'shelter_id' => $adminShelterId,
            'user_id' => $managerId,
            'role' => 'manager', // Sets shelter management access
            'vaccination_notifications' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}

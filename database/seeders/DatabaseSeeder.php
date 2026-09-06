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
        $dogId = DB::table('species')->insertGetId(['name' => 'Cão', 'created_at' => now(), 'updated_at' => now()]);
        $catId = DB::table('species')->insertGetId(['name' => 'Gato', 'created_at' => now(), 'updated_at' => now()]);
        $rabbitId = DB::table('species')->insertGetId(['name' => 'Coelho', 'created_at' => now(), 'updated_at' => now()]);

        // 2. SEED BREEDS (Raças)
        // Global defaults (SRD / Indefinida)
        DB::table('breeds')->insert([
            ['species_id' => $dogId, 'name' => 'Indefinida / SRD', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $catId, 'name' => 'Indefinida / SRD', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $rabbitId, 'name' => 'Indefinida / SRD', 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Common Portuguese Dog Breeds
        DB::table('breeds')->insert([
            ['species_id' => $dogId, 'name' => 'Podengo Português', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $dogId, 'name' => 'Cão de Serra da Estrela', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $dogId, 'name' => 'Rafeiro do Alentejo', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $dogId, 'name' => 'Cão de Água Português', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $dogId, 'name' => 'Pastor Alemão', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $dogId, 'name' => 'Labrador Retriever', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Common Cat Breeds
        DB::table('breeds')->insert([
            ['species_id' => $catId, 'name' => 'Europeu Comum', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $catId, 'name' => 'Siamês', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $catId, 'name' => 'Persa', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['species_id' => $catId, 'name' => 'Angorá', 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. SEED COLORS (Cores)
        $colors = ['Preto', 'Branco', 'Castanho', 'Cinzento', 'Bege', 'Laranja / Ruivo', 'Mel', 'Tigrado', 'Bicolor', 'Tricolor'];
        foreach ($colors as $color) {
            DB::table('colors')->insert(['name' => $color, 'created_at' => now(), 'updated_at' => now()]);
        }

        // 4. SEED FUR TYPES (Tipos de Pelo)
        $furTypes = ['Curto', 'Longo', 'Sem Pelo', 'Cerdoso / Cerda', 'Ondulado'];
        foreach ($furTypes as $fur) {
            DB::table('fur_types')->insert(['name' => $fur, 'created_at' => now(), 'updated_at' => now()]);
        }

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

        // 7. SEED GLOBAL SYSTEM ADMIN USER (No shelter attached)
        DB::table('users')->insert([
            'shelter_id' => null, // Explicitly global manager
            'name' => 'Administrador Acolhe',
            'email' => 'admin@acolhe.pt',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}

<?php

namespace Database\Seeders;

class CatBreedSeeder extends SpeciesBreedSeeder
{
    public const SPECIES = 'Gato';

    /**
     * The breed pre-selected for new cats, matching the PortugalZoofilo.net
     * animal form's default.
     */
    public const DEFAULT_BREED = 'Europeu Comum';

    /**
     * Cat breeds, taken from the PortugalZoofilo.net animal form.
     *
     * @var list<string>
     */
    public const BREEDS = [
        'Abissínio', 'Americano De Pêlo Curto', 'Angorá', 'Azul Inglês De Pêlo Curto', 'Balinês', 'Bengali',
        'Birmanês', 'Bobtail Japonês', 'Burmilla', 'Chinchila', 'Cornish Rex', 'Creme Inglês De Pêlo Curto',
        'Curl Americano', 'Devon Rex', 'Esfinge', 'Europeu Comum', 'Exótico De Pêlo Curto', 'Havana', 'Javanês',
        'Korat', 'Maine Coon', 'Manx', 'Mau Egípcio', 'Norueguês Da Floresta', 'Persa',
        'Preto Inglês De Pêlo Curto', 'Ragdoll', 'Scottish Fold', 'Siamês', 'Somali',
    ];
}

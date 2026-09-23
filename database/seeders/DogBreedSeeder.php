<?php

namespace Database\Seeders;

class DogBreedSeeder extends SpeciesBreedSeeder
{
    public const SPECIES = 'Cão';

    /**
     * The breed pre-selected for new dogs (mixed breed), matching the
     * PortugalZoofilo.net animal form's default.
     */
    public const DEFAULT_BREED = 'Cão Rafeiro';

    /**
     * Dog breeds, taken from the PortugalZoofilo.net animal form.
     *
     * @var list<string>
     */
    public const BREEDS = [
        'Affenpinscher', 'Aidi', 'Ainu', 'Airedale Terrier', 'Akita', 'Alaskan Malamute',
        'Anglo-Français de Petite Vénerie', 'Appensell Mountain Dog', 'Australian Cattle Dog', 'Australian Kelpie',
        'Australian Terrier', 'Azawakh', 'Balkan Hound', 'Barbet', 'Basenji', 'Basset Artésian Normand',
        'Basset Bleu de Gascogne', 'Basset Fauve de Bretagne', 'Basset Hound', 'Beagle', 'Bearded Collie',
        'Beauceron', 'Bedlington Terrier', 'Bergamasco', 'Berger de Picard', 'Berner Laufhund',
        'Bernese Mountain Dog', 'Bichon Frise', 'Billy', 'Black and Tan Coonhound', 'Bloodhound',
        'Bluetick Coonhound', 'Bolonhês', 'Border Collie', 'Border Terrier', 'Borzoi', 'Boston Terrier',
        'Bouvier da Flandres', 'Boxer', 'Braco de Auvergne', 'Braco de Bourbonnais', 'Braco Italiano',
        'Braco St. Germain', 'Briard', 'Briquet Griffon Vendéen', 'Buhund da Noruega', 'Bulldog',
        'Bulldog Americano', 'Bulldog Francês', 'Bullmastiff', 'Bull Terrier', 'Bull Terrier Miniatura',
        'Cairn Terrier', 'Cão da Gronelândia', 'Cão da Islândia', 'Cão das Canárias', 'Cão da Serra da Estrela',
        'Cão da Serra de Aires', 'Cão de Água Português', 'Cão de Caça de Maiorca', 'Cão de Canaan',
        'Cão de Castro Laboreiro', 'Cão dos Pirinéus', 'Cão Leopardo de Catahoula', 'Cão Pastor Alemão',
        'Cão Pastor Australiano', 'Cão Pastor da Catalunha', 'Cão Pastor de Maiorca', 'Cão Pastor de Maremma',
        'Cão Rafeiro', 'Carolina Dog', 'Cavaleiro Rei Charles', 'Cesky Terrier', 'Chesapeake Bay Retriever',
        "Chien D'Artois", 'Chihuahua', 'Chinese Crested Dog', 'Chin Japonês', 'Chinook', 'Chow Chow',
        "Cirneco dell'Etna", 'Clumber Spaniel', 'Cocker Spaniel', 'Cocker Spaniel Americano',
        'Continental Toy Spaniel: Papillon', 'Continental Toy Spaniel: Phalene', 'Coonhound Inglês',
        'Cotton de Tulear', 'Curly-Coated Retriever', 'Czesky Fousek', 'Dachshund Miniatura', 'Dálmata',
        'Dandie Dinmont Terrier', 'Deerhound', 'Dobermann', 'Dogo Argentino', 'Dogue Alemão', 'Dogue de Bordéus',
        'Drever', 'Dunker', 'Dutch Partridge Dog', 'Dutch Shepherd Dog', 'Elkhound da Noruega',
        'Elkhound da Suécia', 'Elkhound Negro da Noruega', 'English Springer Spaniel', 'Entelbuch Mountain Dog',
        'Épagneul Bleu de Picardie', 'Épagneul Breton', 'Épagneul de Point-Audemere', 'Épagneul Francês',
        'Épagneul Picard', 'Eskimo Dog', 'Eurasier', 'Field Spaniel', 'Fila Brasileiro', 'Fila de Sao Miguel',
        'Finnish Hound', 'Flat-Coated Retriever', 'Foxhound', 'Foxhound Americano', 'Fox Terrier de Pêlo Duro',
        'Fox Terrier de Pêlo Macio', 'Glen of Imaal Terrier', 'Golden Retriever', 'Gordon Setter',
        'Grand Basset Griffon Vendéen', 'Grand Bleu de Gascogne', 'Grande Braco Francês',
        'Grande Cão Suíço de Montanha', 'Grande Munsterlander', 'Grand Gascon- Saintongenois',
        'Grand Griffon Vendéen', 'Greyhound', 'Greyhound Espanhol', 'Greyhound Húngaro', 'Greyhound Italiano',
        'Griffon Bruxellois', 'Griffon Fauve de Bretagne', 'Griffon Nivernais', 'Groenendael', 'Haldenstövare',
        'Hamiltonstövare', 'Harrier', 'Havanês', 'Hound Afegão', 'Hound de Ibiza', 'Hound Faraó',
        'Hound Italiano', 'Hound Jugoslavo Tricolor', 'Hovawart', 'Hunting Terrier Alemão', 'Husky Siberiano',
        'Hygenhund', 'Inca Hairless Dog', 'Irish Water Spaniel', 'Irish Wolfhound', 'Jura Laufhund: Bruno',
        'Jura Laufhund: St. Hubert', 'Kai Dog', 'Karelian Bear Dog', 'Keeshond', 'Kerry Beagle',
        'Kerry Blue Terrier', 'Komondor', 'Kooiker Dog', 'Krasky Ovcar', 'Kromfohrlander', 'Kuvasz', 'Kyi Leo',
        'Labrador Retriever', 'Laekenois', 'Laika do Leste Siberiano', 'Laika do Oeste Siberiano',
        'Laika Russo-Europeu', 'Lakeland Terrier', 'Lancashire Heeler', 'Landseer', 'Lapinporokoira',
        'Lapphund da Suécia', 'Lapphund Finlandês', 'Leonberger', 'Lhasa Apso', 'Lowchen', 'Lundehund',
        'Lurcher', 'Luzerner Laufhund', 'Malinois', 'Maltês', 'Mastim', 'Mastim dos Pirinéus', 'Mastim Espanhol',
        'Mastim Napolitano', 'Mastim Tibetano', 'Mexican Hairless Dog', 'Mountain Hound da Bavária',
        'Mountain Hound Jugoslavo', 'Mudi', 'Newfoundland', 'New Guinea Singing Dog', 'Norfolk Terrier',
        'Norrbottenspets', 'Norwich Terrier', 'Nova Scotia Duck Tolling Retriever', 'Old Danish Pointer',
        'Olde English Bulldogge', 'Old English SheepDog', 'Otter Hound', 'Owczarek Podhalanski',
        'Parson Jack Russel Terrier', 'Patterdale Terrier', 'Pequeno Munsterlander', 'Pequinês',
        'Perdigueiro de Burgos', 'Perdigueiro Português', 'Petit Bleu de Gascogne',
        'Petit Griffon Bleu de Gascogne', 'Pinscher', 'Pinscher Austríaco', 'Pinscher Miniatura',
        'Pit Bull Terrier Americano', 'Plott Hound', 'Podengo Médio Português', 'Podengo Pequeno Português',
        'Pointer', 'Pointer de Pêlo Duro', 'Pointing Griffon de Pêlo Duro', 'Polish Hound',
        'Polish Lowland Sheepdog', 'Pomerânia', 'Poodle Miniatura', 'Porcelaine', 'Posavac Hound', 'Pug', 'Puli',
        'Pumi', 'Rafeiro do Alentejo', 'Redbone Coonhound', 'Ridgeback Rodesiano', 'Rottweiler', 'Rough Collie',
        'Saarloos Wolfhound', 'Sabueso Espanhol', 'Saluki', 'Samoiedo', 'São Bernardo', 'Sar Planina',
        'Schapendoes', 'Schillerstovare', 'Schipperke', 'Schnauzer Gigante', 'Schnauzer Miniatura',
        'Schweisshund de Hannover', 'Schweizer Laufhund', 'Sealyham Terrier', 'Setter Inglês', 'Setter Irlandês',
        'Setter Irlandês Vermelho e Branco', 'Shar Pei', 'Shetland Sheepdog', 'Shiba Inu', 'Shih Tzu',
        'Silky Terrier Australiano', 'Skye Terrier', 'Sloughi', 'Smalandsstovare', 'Smooth Collie',
        'Soft-coated Wheaten Terrier', 'Spaniel Alemão', 'Spaniel Rei Charles', 'Spaniel Tibetano', 'Spinone',
        'Spitz Alemão Gigante', 'Spitz Alemão Médio', 'Spitz Alemão Pequeno', 'Spitz da Finlândia',
        'Spitz Japonês', 'Stabyhoun', 'Staffordshire Bull Terrier', 'Staffordshire Terrier Americano',
        'Standard Poodle', 'Sussex Spaniel', 'Swedish Vallhund', 'Terrier de Manchester', 'Terrier Escocês',
        'Terrier Irlandês', 'Terrier Japonês', 'Terrier Tibetano', 'Tervuren', 'Tosa Inu', 'Toy Eskimo Americano',
        'Toy Poodle', 'Toy Terrier Americano', 'Toy Terrier Inglês', 'Treeing Walker Coonhound',
        'Vizsla de Pêlo Duro', 'Vizsla Húngaro', 'Volpino Italiano', 'Weimaraner', 'Welsh Corgi Cardigan',
        'Welsh Corgi Pembroke', 'Welsh Springer Spaniel', 'Welsh Terrier', 'West Highland White Terrier',
        'Wetterhoun', 'Whippet', 'Yorkshire Terrier',
    ];
}

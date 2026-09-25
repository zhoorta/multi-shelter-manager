<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\ShowsPublicPets;
use App\Models\Breed;
use App\Models\Pet;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\Size;
use App\Models\Species;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Public welcome page: presents the multi-shelter project and lets anyone
 * browse and filter the pets that every shelter has published for adoption.
 */
class Welcome extends Component
{
    use ShowsPublicPets, WithPagination;

    #[Url(as: 'species')]
    public string $speciesFilter = '';

    #[Url(as: 'gender')]
    public string $genderFilter = '';

    #[Url(as: 'size')]
    public string $sizeFilter = '';

    #[Url(as: 'breed')]
    public string $breedFilter = '';

    #[Url(as: 'region')]
    public string $regionFilter = '';

    /**
     * Breeds and sizes belong to a species, so a species change clears them.
     */
    public function updatedSpeciesFilter(): void
    {
        $this->breedFilter = '';
        $this->sizeFilter = '';
        $this->resetPage();
    }

    public function updatedGenderFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSizeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBreedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRegionFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['speciesFilter', 'genderFilter', 'sizeFilter', 'breedFilter', 'regionFilter']);
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Pet>
     */
    #[Computed]
    public function pets(): LengthAwarePaginator
    {
        return $this->publicPetsQuery()
            ->when($this->speciesFilter !== '', fn (Builder $query) => $query->where('species_id', $this->speciesFilter))
            ->when($this->genderFilter !== '', fn (Builder $query) => $query->where('gender', $this->genderFilter))
            ->when($this->sizeFilter !== '', fn (Builder $query) => $query->where('size_id', $this->sizeFilter))
            ->when($this->breedFilter !== '', fn (Builder $query) => $query->where('breed_id', $this->breedFilter))
            ->when($this->regionFilter !== '', fn (Builder $query) => $query->whereHas('shelter', fn (Builder $query) => $query->where('region_id', $this->regionFilter)))
            ->with(['species', 'breed', 'size', 'shelter.region', 'images', 'cage:id,wing_id', 'cage.wing:id,is_foster'])
            ->orderByDesc('is_featured')
            ->latest('checkin_date')
            ->latest('id')
            ->paginate(12);
    }

    /**
     * @return Collection<int, Species>
     */
    #[Computed]
    public function species(): Collection
    {
        return Species::query()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Breed>
     */
    #[Computed]
    public function breeds(): Collection
    {
        if ($this->speciesFilter === '') {
            return new Collection;
        }

        return Breed::query()->where('species_id', $this->speciesFilter)->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Size>
     */
    #[Computed]
    public function sizes(): Collection
    {
        if ($this->speciesFilter === '') {
            return new Collection;
        }

        return Size::query()->where('species_id', $this->speciesFilter)->orderBy('id')->get();
    }

    /**
     * Only regions that actually have a shelter, so the filter never offers
     * a district that can't return any pets.
     *
     * @return Collection<int, Region>
     */
    #[Computed]
    public function regions(): Collection
    {
        return Region::query()->whereHas('shelters')->orderBy('name')->get();
    }

    /**
     * @return array{shelters: int, pets: int, regions: int}
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'shelters' => Shelter::query()->count(),
            'pets' => $this->publicPetsQuery()->count(),
            'regions' => Shelter::query()->whereNotNull('region_id')->distinct()->count('region_id'),
        ];
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        return view('livewire.welcome')
            ->title(__('Adopt a friend'))
            ->layoutData([
                'description' => __('Find a shelter animal waiting for a home. Browse the dogs and cats of our partner shelters and adopt your new best friend.'),
                'structuredData' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'name' => config('app.name'),
                    'url' => route('home'),
                    'inLanguage' => app()->getLocale(),
                ],
            ]);
    }
}

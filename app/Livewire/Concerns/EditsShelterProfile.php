<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\Cage;
use App\Models\Colony;
use App\Models\Pet;
use App\Models\Region;
use App\Models\Shelter;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

/**
 * The shelter profile fields (contacts, location, description, logo) shared by
 * the admin ShelterForm and the manager's Settings > Shelter page, together
 * with the switches for the optional modules. The shelter name and species are
 * admin-only and live in ShelterForm.
 */
trait EditsShelterProfile
{
    public string $shelterShortName = '';

    public string $shelterCity = '';

    public ?int $shelterRegionId = null;

    public string $shelterAddress = '';

    public string $shelterPostalCode = '';

    public string $shelterPhone = '';

    public string $shelterEmail = '';

    public string $shelterWebsite = '';

    public string $shelterDescription = '';

    public ?string $existingLogoPath = null;

    public mixed $shelterLogo = null;

    /**
     * Whether each optional module (see Shelter::MODULES) is on.
     *
     * @var array<string, bool>
     */
    public array $shelterModules = [];

    /**
     * The species enabled for the shelter, kept in memory until it is saved.
     *
     * @var array<int, int>
     */
    public array $shelterSpeciesIds = [];

    /**
     * All species available to be enabled for the shelter.
     *
     * @return Collection<int, Species>
     */
    #[Computed]
    public function species(): Collection
    {
        return Species::query()->orderBy('name')->get();
    }

    public function toggleSpecies(int $speciesId): void
    {
        if (! in_array($speciesId, $this->shelterSpeciesIds, true)) {
            $this->shelterSpeciesIds[] = $speciesId;

            return;
        }

        if ($this->speciesIsInUse($speciesId)) {
            Flux::toast(variant: 'warning', text: __('This species has animals, cages or colonies in the shelter, so it cannot be turned off.'));

            return;
        }

        $this->shelterSpeciesIds = array_values(array_diff($this->shelterSpeciesIds, [$speciesId]));
    }

    /**
     * The shelter being edited, or null while an admin is creating it.
     */
    abstract protected function editedShelter(): ?Shelter;

    /**
     * Whether the shelter has animals, cages or colonies of the species, which
     * would be orphaned by turning it off.
     */
    private function speciesIsInUse(int $speciesId): bool
    {
        $shelter = $this->editedShelter();

        if ($shelter === null) {
            return false;
        }

        return Pet::query()->withoutGlobalScope('shelter')->where('shelter_id', $shelter->id)->where('species_id', $speciesId)->exists()
            || Colony::query()->withoutGlobalScope('shelter')->where('shelter_id', $shelter->id)->where('species_id', $speciesId)->exists()
            || Cage::query()->where('species_id', $speciesId)->whereHas('wing.facility', fn (Builder $query) => $query->withoutGlobalScope('shelter')->where('shelter_id', $shelter->id))->exists();
    }

    /**
     * Whether the shelter being edited has a species that can live in colonies.
     */
    protected function offersColonies(): bool
    {
        return Species::query()->whereIn('id', $this->shelterSpeciesIds)->where('has_colonies', true)->exists();
    }

    /**
     * All regions (distritos) a shelter can be located in.
     *
     * @return Collection<int, Region>
     */
    #[Computed]
    public function regions(): Collection
    {
        return Region::query()->orderBy('name')->get();
    }

    protected function fillShelterProfile(Shelter $shelter): void
    {
        $this->shelterShortName = (string) $shelter->short_name;
        $this->shelterCity = $shelter->city;
        $this->shelterRegionId = $shelter->region_id;
        $this->shelterAddress = (string) $shelter->address;
        $this->shelterPostalCode = (string) $shelter->postal_code;
        $this->shelterPhone = (string) $shelter->phone;
        $this->shelterEmail = (string) $shelter->email;
        $this->shelterWebsite = (string) $shelter->website;
        $this->shelterDescription = (string) $shelter->description;
        $this->existingLogoPath = $shelter->logo_path;
        $this->shelterModules = $this->moduleChoices($shelter);
    }

    /**
     * The on/off choice for every module, on where the shelter never chose.
     *
     * @return array<string, bool>
     */
    protected function moduleChoices(?Shelter $shelter = null): array
    {
        return collect(Shelter::MODULES)
            ->mapWithKeys(fn (string $module): array => [$module => $shelter?->hasModule($module) ?? true])
            ->all();
    }

    /**
     * Label shown next to each module switch.
     *
     * @return array<string, string>
     */
    public function moduleLabels(): array
    {
        $labels = [
            'members' => __('Members'),
            'volunteers' => __('Volunteers'),
            'sponsorships' => __('Sponsorships'),
            'adoption_applications' => __('Adoption Applications'),
            'reports' => __('Reports'),
            'health_records' => __('Vaccinations and Treatments'),
            'colonies' => __('Colonies'),
        ];

        return $this->offersColonies() ? $labels : Arr::except($labels, 'colonies');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function shelterProfileRules(): array
    {
        return [
            'shelterShortName' => ['nullable', 'string', 'max:255'],
            'shelterCity' => ['required', 'string', 'max:255'],
            'shelterRegionId' => ['nullable', 'integer', Rule::exists('regions', 'id')->withoutTrashed()],
            'shelterAddress' => ['nullable', 'string', 'max:255'],
            'shelterPostalCode' => ['nullable', 'string', 'max:255'],
            'shelterPhone' => ['nullable', 'string', 'max:255'],
            'shelterEmail' => ['required', 'string', 'email', 'max:255'],
            'shelterWebsite' => ['nullable', 'string', 'url', 'max:255'],
            'shelterDescription' => ['nullable', 'string'],
            'shelterLogo' => ['nullable', 'image', 'max:2048'],
            'shelterModules' => ['array'],
            'shelterModules.*' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function shelterProfileAttributes(): array
    {
        return [
            'shelterShortName' => __('Short Name'),
            'shelterCity' => __('City'),
            'shelterRegionId' => __('Region'),
            'shelterAddress' => __('Address'),
            'shelterPostalCode' => __('Postal Code'),
            'shelterPhone' => __('Phone'),
            'shelterEmail' => __('Email'),
            'shelterWebsite' => __('Website'),
            'shelterDescription' => __('Description'),
            'shelterLogo' => __('Logo'),
            'shelterModules.*' => __('Modules'),
        ];
    }

    /**
     * Map the validated profile fields to Shelter attributes, storing a newly
     * uploaded logo and deleting the one it replaces.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function shelterProfileData(array $validated): array
    {
        $data = [
            'short_name' => $validated['shelterShortName'] !== '' ? $validated['shelterShortName'] : null,
            'city' => $validated['shelterCity'],
            'region_id' => $validated['shelterRegionId'],
            'address' => $validated['shelterAddress'] !== '' ? $validated['shelterAddress'] : null,
            'postal_code' => $validated['shelterPostalCode'] !== '' ? $validated['shelterPostalCode'] : null,
            'phone' => $validated['shelterPhone'] !== '' ? $validated['shelterPhone'] : null,
            'email' => $validated['shelterEmail'],
            'website' => $validated['shelterWebsite'] !== '' ? $validated['shelterWebsite'] : null,
            'description' => $validated['shelterDescription'] !== '' ? $validated['shelterDescription'] : null,
            'modules' => collect(Shelter::MODULES)
                ->mapWithKeys(fn (string $module): array => [$module => (bool) ($validated['shelterModules'][$module] ?? true)])
                ->all(),
        ];

        if ($this->shelterLogo !== null) {
            if ($this->existingLogoPath !== null) {
                Storage::delete($this->existingLogoPath);
            }

            $data['logo_path'] = $this->shelterLogo->store('shelters');
            $data['logo_size'] = $this->shelterLogo->getSize();
        }

        return $data;
    }
}

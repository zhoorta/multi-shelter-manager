<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\Region;
use App\Models\Shelter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

/**
 * The shelter profile fields (contacts, location, description, logo) shared by
 * the admin ShelterForm and the manager's Settings > Shelter page. The shelter
 * name and species are admin-only and live in ShelterForm.
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
        ];

        if ($this->shelterLogo !== null) {
            if ($this->existingLogoPath !== null) {
                Storage::delete($this->existingLogoPath);
            }

            $data['logo_path'] = $this->shelterLogo->store('shelters');
        }

        return $data;
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Shelter;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShelterForm extends Component
{
    use WithFileUploads;

    public ?Shelter $shelter = null;

    public string $shelterName = '';

    public string $shelterShortName = '';

    public string $shelterCity = '';

    public string $shelterAddress = '';

    public string $shelterPostalCode = '';

    public string $shelterPhone = '';

    public string $shelterEmail = '';

    public string $shelterWebsite = '';

    public string $shelterDescription = '';

    public ?string $existingLogoPath = null;

    public mixed $shelterLogo = null;

    /**
     * @var array<int, int>
     */
    public array $shelterSpeciesIds = [];

    public function mount(?Shelter $shelter = null): void
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        if ($shelter === null) {
            return;
        }

        $this->shelter = $shelter;
        $this->shelterName = $shelter->name;
        $this->shelterShortName = (string) $shelter->short_name;
        $this->shelterCity = $shelter->city;
        $this->shelterAddress = (string) $shelter->address;
        $this->shelterPostalCode = (string) $shelter->postal_code;
        $this->shelterPhone = (string) $shelter->phone;
        $this->shelterEmail = (string) $shelter->email;
        $this->shelterWebsite = (string) $shelter->website;
        $this->shelterDescription = (string) $shelter->description;
        $this->existingLogoPath = $shelter->logo_path;
        $this->shelterSpeciesIds = $shelter->species()->pluck('species.id')->all();
    }

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
        if (in_array($speciesId, $this->shelterSpeciesIds, true)) {
            $this->shelterSpeciesIds = array_values(array_diff($this->shelterSpeciesIds, [$speciesId]));

            return;
        }

        $this->shelterSpeciesIds[] = $speciesId;
    }

    public function saveShelter(): void
    {
        $validated = $this->validate([
            'shelterName' => ['required', 'string', 'max:255'],
            'shelterShortName' => ['nullable', 'string', 'max:255'],
            'shelterCity' => ['required', 'string', 'max:255'],
            'shelterAddress' => ['nullable', 'string', 'max:255'],
            'shelterPostalCode' => ['nullable', 'string', 'max:255'],
            'shelterPhone' => ['nullable', 'string', 'max:255'],
            'shelterEmail' => ['nullable', 'string', 'email', 'max:255'],
            'shelterWebsite' => ['nullable', 'string', 'url', 'max:255'],
            'shelterDescription' => ['nullable', 'string'],
            'shelterLogo' => ['nullable', 'image', 'max:2048'],
            'shelterSpeciesIds' => ['array'],
            'shelterSpeciesIds.*' => ['integer', 'exists:species,id'],
        ], [], [
            'shelterName' => __('Name'),
            'shelterShortName' => __('Short Name'),
            'shelterCity' => __('City'),
            'shelterAddress' => __('Address'),
            'shelterPostalCode' => __('Postal Code'),
            'shelterPhone' => __('Phone'),
            'shelterEmail' => __('Email'),
            'shelterWebsite' => __('Website'),
            'shelterDescription' => __('Description'),
            'shelterLogo' => __('Logo'),
            'shelterSpeciesIds.*' => __('Species'),
        ]);

        $data = [
            'name' => $validated['shelterName'],
            'short_name' => $validated['shelterShortName'] !== '' ? $validated['shelterShortName'] : null,
            'city' => $validated['shelterCity'],
            'address' => $validated['shelterAddress'] !== '' ? $validated['shelterAddress'] : null,
            'postal_code' => $validated['shelterPostalCode'] !== '' ? $validated['shelterPostalCode'] : null,
            'phone' => $validated['shelterPhone'] !== '' ? $validated['shelterPhone'] : null,
            'email' => $validated['shelterEmail'] !== '' ? $validated['shelterEmail'] : null,
            'website' => $validated['shelterWebsite'] !== '' ? $validated['shelterWebsite'] : null,
            'description' => $validated['shelterDescription'] !== '' ? $validated['shelterDescription'] : null,
        ];

        if ($this->shelterLogo !== null) {
            if ($this->existingLogoPath !== null) {
                Storage::disk('public')->delete($this->existingLogoPath);
            }

            $data['logo_path'] = $this->shelterLogo->store('shelters', 'public');
        }

        $isEditing = $this->shelter !== null;

        if ($isEditing) {
            $this->shelter->update($data);
        } else {
            $this->shelter = Shelter::query()->create($data);
        }

        $this->shelter->species()->sync($validated['shelterSpeciesIds'] ?? []);

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('admin.shelters.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.shelter-form')->title(
            $this->shelter !== null ? __('Edit').' — '.$this->shelter->name : __('Create').' — '.__('Shelters'),
        );
    }
}

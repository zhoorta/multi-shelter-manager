<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Shelter;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Manage Shelters')]
class ManageShelters extends Component
{
    use WithFileUploads;

    public ?int $editingShelterId = null;

    public string $shelterName = '';

    public string $shelterCity = '';

    public string $shelterAddress = '';

    public string $shelterPostalCode = '';

    public string $shelterPhone = '';

    public string $shelterEmail = '';

    public string $shelterWebsite = '';

    public string $shelterDescription = '';

    public ?string $existingLogoPath = null;

    public mixed $shelterLogo = null;

    public function mount(): void
    {
        abort_unless(Auth::user()->role === 'admin', 403);
    }

    /**
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function shelters(): Collection
    {
        return Shelter::query()
            ->withCount(['users', 'pets'])
            ->orderBy('name')
            ->get();
    }

    public function createShelter(): void
    {
        $this->resetShelterForm();
    }

    public function editShelter(int $shelterId): void
    {
        $shelter = Shelter::query()->findOrFail($shelterId);

        $this->editingShelterId = $shelter->id;
        $this->shelterName = $shelter->name;
        $this->shelterCity = $shelter->city;
        $this->shelterAddress = (string) $shelter->address;
        $this->shelterPostalCode = (string) $shelter->postal_code;
        $this->shelterPhone = (string) $shelter->phone;
        $this->shelterEmail = (string) $shelter->email;
        $this->shelterWebsite = (string) $shelter->website;
        $this->shelterDescription = (string) $shelter->description;
        $this->existingLogoPath = $shelter->logo_path;
        $this->shelterLogo = null;
    }

    public function saveShelter(): void
    {
        $validated = $this->validate([
            'shelterName' => ['required', 'string', 'max:255'],
            'shelterCity' => ['required', 'string', 'max:255'],
            'shelterAddress' => ['nullable', 'string', 'max:255'],
            'shelterPostalCode' => ['nullable', 'string', 'max:255'],
            'shelterPhone' => ['nullable', 'string', 'max:255'],
            'shelterEmail' => ['nullable', 'string', 'email', 'max:255'],
            'shelterWebsite' => ['nullable', 'string', 'url', 'max:255'],
            'shelterDescription' => ['nullable', 'string'],
            'shelterLogo' => ['nullable', 'image', 'max:2048'],
        ], [], [
            'shelterName' => __('Name'),
            'shelterCity' => __('City'),
            'shelterAddress' => __('Address'),
            'shelterPostalCode' => __('Postal Code'),
            'shelterPhone' => __('Phone'),
            'shelterEmail' => __('Email'),
            'shelterWebsite' => __('Website'),
            'shelterDescription' => __('Description'),
            'shelterLogo' => __('Logo'),
        ]);

        $data = [
            'name' => $validated['shelterName'],
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

        if ($this->editingShelterId !== null) {
            Shelter::query()->findOrFail($this->editingShelterId)->update($data);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Shelter::query()->create($data);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetShelterForm();
        unset($this->shelters);

        Flux::modal('shelter-form')->close();
    }

    public function deleteShelter(int $shelterId): void
    {
        Shelter::query()->findOrFail($shelterId)->delete();

        if ($this->editingShelterId === $shelterId) {
            $this->resetShelterForm();
        }

        unset($this->shelters);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetShelterForm(): void
    {
        $this->reset([
            'editingShelterId',
            'shelterName',
            'shelterCity',
            'shelterAddress',
            'shelterPostalCode',
            'shelterPhone',
            'shelterEmail',
            'shelterWebsite',
            'shelterDescription',
            'existingLogoPath',
            'shelterLogo',
        ]);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-shelters');
    }
}

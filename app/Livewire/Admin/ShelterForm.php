<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Livewire\Concerns\EditsShelterProfile;
use App\Models\Shelter;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShelterForm extends Component
{
    use EditsShelterProfile;
    use WithFileUploads;

    public ?Shelter $shelter = null;

    public string $shelterName = '';

    /**
     * The public page's URL slug; left blank, one is made from the name.
     */
    public string $shelterSlug = '';

    public function mount(?Shelter $shelter = null): void
    {
        abort_unless(Auth::user()->is_admin, 403);

        if ($shelter === null) {
            return;
        }

        $this->shelter = $shelter;
        $this->shelterName = $shelter->name;
        $this->shelterSlug = $shelter->slug;
        $this->fillShelterProfile($shelter);
        $this->shelterSpeciesIds = $shelter->species()->pluck('species.id')->all();
    }

    protected function editedShelter(): ?Shelter
    {
        return $this->shelter;
    }

    public function saveShelter(): void
    {
        $validated = $this->validate([
            'shelterName' => ['required', 'string', 'max:255'],
            // Lowercase words joined by hyphens.
            'shelterSlug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('shelters', 'slug')->ignore($this->shelter?->id)],
            ...$this->shelterProfileRules(),
            'shelterSpeciesIds' => ['array'],
            'shelterSpeciesIds.*' => ['integer', 'exists:species,id'],
        ], [], [
            'shelterName' => __('Name'),
            'shelterSlug' => __('Public address'),
            ...$this->shelterProfileAttributes(),
            'shelterSpeciesIds.*' => __('Species'),
        ]);

        $data = [
            'name' => $validated['shelterName'],
            ...$this->shelterProfileData($validated),
        ];
        $data['slug'] = filled($validated['shelterSlug'])
            ? $validated['shelterSlug']
            : Shelter::uniqueSlug($data['name'], $data['city'], $this->shelter?->id);

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
        return view('livewire.admin.shelter-form', [
            'publicPagePrefix' => Str::after(route('shelters'), '://').'/',
        ])->title(
            $this->shelter !== null ? __('Edit').' — '.$this->shelter->name : __('Create').' — '.__('Shelters'),
        );
    }
}

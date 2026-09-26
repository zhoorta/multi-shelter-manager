<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Livewire\Concerns\EditsShelterProfile;
use App\Models\Shelter;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShelterForm extends Component
{
    use EditsShelterProfile;
    use WithFileUploads;

    public ?Shelter $shelter = null;

    public string $shelterName = '';

    /**
     * @var array<int, int>
     */
    public array $shelterSpeciesIds = [];

    public function mount(?Shelter $shelter = null): void
    {
        abort_unless(Auth::user()->is_admin, 403);

        if ($shelter === null) {
            return;
        }

        $this->shelter = $shelter;
        $this->shelterName = $shelter->name;
        $this->fillShelterProfile($shelter);
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
            ...$this->shelterProfileRules(),
            'shelterSpeciesIds' => ['array'],
            'shelterSpeciesIds.*' => ['integer', 'exists:species,id'],
        ], [], [
            'shelterName' => __('Name'),
            ...$this->shelterProfileAttributes(),
            'shelterSpeciesIds.*' => __('Species'),
        ]);

        $data = [
            'name' => $validated['shelterName'],
            ...$this->shelterProfileData($validated),
        ];

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

<?php

declare(strict_types=1);

namespace App\Livewire\Colonies;

use App\Models\Colony;
use App\Models\Species;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Colonies')]
class ManageColonies extends Component
{
    public ?int $editingColonyId = null;

    public ?int $colonySpeciesId = null;

    public string $colonyName = '';

    public string $colonyLocation = '';

    public string $colonyCity = '';

    public string $colonyCaretakerName = '';

    public string $colonyCaretakerPhone = '';

    public string $colonyNotes = '';

    public bool $colonyIsActive = true;

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
    }

    protected function canEdit(): bool
    {
        return Auth::user()->canEditArea('pets');
    }

    /**
     * Species an animal can live in a colony as.
     *
     * @return Collection<int, Species>
     */
    #[Computed]
    public function colonySpecies(): Collection
    {
        return Species::query()->where('has_colonies', true)->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Colony>
     */
    #[Computed]
    public function colonies(): Collection
    {
        return Colony::query()
            ->with('species')
            ->withCount([
                'pets as cats_count' => fn (Builder $query) => $query->whereNull('date_of_death'),
                'pets as neutered_count' => fn (Builder $query) => $query->whereNull('date_of_death')->where('is_neutered', true),
            ])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();
    }

    public function createColony(): void
    {
        abort_unless($this->canEdit(), 403);

        $this->resetColonyForm();
        $this->colonySpeciesId = $this->colonySpecies->count() === 1 ? $this->colonySpecies->first()->id : null;
    }

    public function editColony(int $colonyId): void
    {
        abort_unless($this->canEdit(), 403);

        $colony = Colony::query()->findOrFail($colonyId);

        $this->editingColonyId = $colony->id;
        $this->colonySpeciesId = $colony->species_id;
        $this->colonyName = $colony->name;
        $this->colonyLocation = (string) $colony->location;
        $this->colonyCity = (string) $colony->city;
        $this->colonyCaretakerName = (string) $colony->caretaker_name;
        $this->colonyCaretakerPhone = (string) $colony->caretaker_phone;
        $this->colonyNotes = (string) $colony->notes;
        $this->colonyIsActive = $colony->is_active;
    }

    public function saveColony(): void
    {
        abort_unless($this->canEdit(), 403);

        $validated = $this->validate([
            'colonySpeciesId' => ['required', 'integer', Rule::exists('species', 'id')->where('has_colonies', true)->withoutTrashed()],
            'colonyName' => ['required', 'string', 'max:255'],
            'colonyLocation' => ['nullable', 'string', 'max:255'],
            'colonyCity' => ['nullable', 'string', 'max:255'],
            'colonyCaretakerName' => ['nullable', 'string', 'max:255'],
            'colonyCaretakerPhone' => ['nullable', 'string', 'max:255'],
            'colonyNotes' => ['nullable', 'string'],
            'colonyIsActive' => ['boolean'],
        ], [], [
            'colonySpeciesId' => __('Species'),
            'colonyName' => __('Name'),
            'colonyLocation' => __('Location'),
            'colonyCity' => __('City'),
            'colonyCaretakerName' => __('Caretaker'),
            'colonyCaretakerPhone' => __('Phone'),
            'colonyNotes' => __('Notes'),
            'colonyIsActive' => __('Active'),
        ]);

        $colony = $this->editingColonyId !== null ? Colony::query()->findOrFail($this->editingColonyId) : null;

        if ($colony !== null && $colony->species_id !== $validated['colonySpeciesId'] && $colony->pets()->exists()) {
            $this->addError('colonySpeciesId', __('The species cannot change while the colony has animals'));

            return;
        }

        $attributes = [
            'species_id' => $validated['colonySpeciesId'],
            'name' => $validated['colonyName'],
            'location' => $validated['colonyLocation'] !== '' ? $validated['colonyLocation'] : null,
            'city' => $validated['colonyCity'] !== '' ? $validated['colonyCity'] : null,
            'caretaker_name' => $validated['colonyCaretakerName'] !== '' ? $validated['colonyCaretakerName'] : null,
            'caretaker_phone' => $validated['colonyCaretakerPhone'] !== '' ? $validated['colonyCaretakerPhone'] : null,
            'notes' => $validated['colonyNotes'] !== '' ? $validated['colonyNotes'] : null,
            'is_active' => $validated['colonyIsActive'],
        ];

        if ($colony !== null) {
            $colony->update($attributes);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Colony::query()->create([...$attributes, 'shelter_id' => Auth::user()->current_shelter_id]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetColonyForm();
        unset($this->colonies);

        Flux::modal('colony-form')->close();
    }

    public function deleteColony(int $colonyId): void
    {
        abort_unless($this->canEdit(), 403);

        $colony = Colony::query()->findOrFail($colonyId);

        // A deleted colony must not keep its animals: without a colony they
        // are shown (and editable) as animals with no location again.
        DB::transaction(function () use ($colony): void {
            $colony->pets()->get()->each->update(['colony_id' => null]);
            $colony->delete();
        });

        unset($this->colonies);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetColonyForm(): void
    {
        $this->reset([
            'editingColonyId', 'colonySpeciesId', 'colonyName', 'colonyLocation', 'colonyCity',
            'colonyCaretakerName', 'colonyCaretakerPhone', 'colonyNotes',
        ]);
        $this->colonyIsActive = true;
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.colonies.manage-colonies');
    }
}

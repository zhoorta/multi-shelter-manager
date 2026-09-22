<?php

declare(strict_types=1);

namespace App\Livewire\Facilities;

use App\Models\Cage;
use App\Models\Facility;
use App\Models\Wing;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Facilities')]
class ManageSpaces extends Component
{
    public ?int $editingFacilityId = null;

    public string $facilityName = '';

    public string $facilityAddress = '';

    public string $facilityPostalCode = '';

    public string $facilityCity = '';

    public string $facilityNotes = '';

    public ?int $editingWingId = null;

    public ?int $wingFacilityId = null;

    public string $wingName = '';

    public string $wingDescription = '';

    public ?int $editingCageId = null;

    public ?int $cageWingId = null;

    public string $cageCode = '';

    public int $cageCapacity = 1;

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
    }

    protected function isManager(): bool
    {
        return Auth::user()->isManagerOfCurrentShelter();
    }

    /**
     * @return Collection<int, Facility>
     */
    #[Computed]
    public function facilities(): Collection
    {
        return Facility::query()
            ->with([
                'wings' => fn ($query) => $query->orderBy('name'),
                'wings.cages' => fn ($query) => $query->orderBy('code'),
            ])
            ->orderBy('name')
            ->get();
    }

    public function createFacility(): void
    {
        abort_unless($this->isManager(), 403);

        $this->resetFacilityForm();
    }

    public function editFacility(int $facilityId): void
    {
        abort_unless($this->isManager(), 403);

        $facility = Facility::query()->findOrFail($facilityId);

        $this->editingFacilityId = $facility->id;
        $this->facilityName = (string) $facility->name;
        $this->facilityAddress = (string) $facility->address;
        $this->facilityPostalCode = (string) $facility->postal_code;
        $this->facilityCity = (string) $facility->city;
        $this->facilityNotes = (string) $facility->notes;
    }

    public function saveFacility(): void
    {
        abort_unless($this->isManager(), 403);

        $validated = $this->validate([
            'facilityName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('facilities', 'name')
                    ->where(fn ($query) => $query->where('shelter_id', Auth::user()->current_shelter_id))
                    ->ignore($this->editingFacilityId),
            ],
            'facilityAddress' => ['nullable', 'string', 'max:255'],
            'facilityPostalCode' => ['nullable', 'string', 'max:20'],
            'facilityCity' => ['nullable', 'string', 'max:100'],
            'facilityNotes' => ['nullable', 'string'],
        ], [], [
            'facilityName' => __('Name'),
            'facilityAddress' => __('Address'),
            'facilityPostalCode' => __('Postal Code'),
            'facilityCity' => __('City'),
            'facilityNotes' => __('Notes'),
        ]);

        $attributes = [
            'name' => $validated['facilityName'],
            'address' => $validated['facilityAddress'] !== '' ? $validated['facilityAddress'] : null,
            'postal_code' => $validated['facilityPostalCode'] !== '' ? $validated['facilityPostalCode'] : null,
            'city' => $validated['facilityCity'] !== '' ? $validated['facilityCity'] : null,
            'notes' => $validated['facilityNotes'] !== '' ? $validated['facilityNotes'] : null,
        ];

        if ($this->editingFacilityId !== null) {
            Facility::query()->findOrFail($this->editingFacilityId)->update($attributes);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Facility::query()->create($attributes);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetFacilityForm();
        unset($this->facilities);

        Flux::modal('facility-form')->close();
    }

    public function deleteFacility(int $facilityId): void
    {
        abort_unless($this->isManager(), 403);

        $facility = Facility::query()->findOrFail($facilityId);

        // Wing (and, transitively, Cage) has no shelter scope of its own and
        // the DB's cascadeOnDelete only fires on a hard delete, so soft-delete
        // each one individually (rather than a bulk ->delete()) so Blameable
        // still stamps deleted_by.
        $facility->wings()->get()->each(function (Wing $wing): void {
            $wing->cages()->get()->each->delete();
            $wing->delete();
        });

        $facility->delete();

        if ($this->editingFacilityId === $facilityId) {
            $this->resetFacilityForm();
        }

        unset($this->facilities);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function createWing(int $facilityId): void
    {
        abort_unless($this->isManager(), 403);

        // findOrFail enforces MultiShelterTrait's shelter scope, so a tampered
        // facility id from another shelter 404s before the modal ever opens.
        Facility::query()->findOrFail($facilityId);

        $this->resetWingForm();
        $this->wingFacilityId = $facilityId;
    }

    public function editWing(int $wingId): void
    {
        abort_unless($this->isManager(), 403);

        $wing = $this->scopedWingQuery()->findOrFail($wingId);

        $this->editingWingId = $wing->id;
        $this->wingFacilityId = $wing->facility_id;
        $this->wingName = $wing->name;
        $this->wingDescription = (string) $wing->description;
    }

    public function saveWing(): void
    {
        abort_unless($this->isManager(), 403);

        $validated = $this->validate([
            'wingFacilityId' => ['required', 'integer', 'exists:facilities,id'],
            'wingName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wings', 'name')
                    ->where(fn ($query) => $query->where('facility_id', $this->wingFacilityId))
                    ->ignore($this->editingWingId),
            ],
            'wingDescription' => ['nullable', 'string'],
        ], [], [
            'wingFacilityId' => __('Facility'),
            'wingName' => __('Name'),
            'wingDescription' => __('Notes'),
        ]);

        // Re-fetch through the scoped query (not just the exists rule above)
        // so a facility belonging to another shelter 404s instead of succeeding.
        $facility = Facility::query()->findOrFail($validated['wingFacilityId']);

        $description = $validated['wingDescription'] !== '' ? $validated['wingDescription'] : null;

        if ($this->editingWingId !== null) {
            $this->scopedWingQuery()->findOrFail($this->editingWingId)->update([
                'facility_id' => $facility->id,
                'name' => $validated['wingName'],
                'description' => $description,
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Wing::query()->create([
                'facility_id' => $facility->id,
                'name' => $validated['wingName'],
                'description' => $description,
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetWingForm();
        unset($this->facilities);

        Flux::modal('wing-form')->close();
    }

    public function deleteWing(int $wingId): void
    {
        abort_unless($this->isManager(), 403);

        $wing = $this->scopedWingQuery()->findOrFail($wingId);

        // Cage has no shelter scope of its own and the DB's cascadeOnDelete
        // only fires on a hard delete, so soft-delete each cage individually
        // (rather than a bulk ->delete()) so Blameable still stamps deleted_by.
        $wing->cages()->get()->each->delete();

        $wing->delete();

        if ($this->editingWingId === $wingId) {
            $this->resetWingForm();
        }

        unset($this->facilities);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    public function createCage(int $wingId): void
    {
        abort_unless($this->isManager(), 403);

        // findOrFail enforces tenancy transitively through the wing's
        // facility, so a tampered wing id from another shelter 404s before
        // the modal ever opens.
        $this->scopedWingQuery()->findOrFail($wingId);

        $this->resetCageForm();
        $this->cageWingId = $wingId;
    }

    public function editCage(int $cageId): void
    {
        abort_unless($this->isManager(), 403);

        $cage = $this->scopedCageQuery()->findOrFail($cageId);

        $this->editingCageId = $cage->id;
        $this->cageWingId = $cage->wing_id;
        $this->cageCode = $cage->code;
        $this->cageCapacity = $cage->capacity;
    }

    public function saveCage(): void
    {
        abort_unless($this->isManager(), 403);

        $validated = $this->validate([
            'cageWingId' => ['required', 'integer', 'exists:wings,id'],
            'cageCode' => ['required', 'string', 'max:255'],
            'cageCapacity' => ['required', 'integer', 'min:1'],
        ], [], [
            'cageWingId' => __('Wing'),
            'cageCode' => __('Code'),
            'cageCapacity' => __('Capacity'),
        ]);

        // Re-fetch through the scoped query (not just the exists rule above)
        // so a wing belonging to another shelter 404s instead of succeeding.
        $wing = $this->scopedWingQuery()->findOrFail($validated['cageWingId']);

        if ($this->editingCageId !== null) {
            $this->scopedCageQuery()->findOrFail($this->editingCageId)->update([
                'wing_id' => $wing->id,
                'code' => $validated['cageCode'],
                'capacity' => $validated['cageCapacity'],
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Cage::query()->create([
                'wing_id' => $wing->id,
                'code' => $validated['cageCode'],
                'capacity' => $validated['cageCapacity'],
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetCageForm();
        unset($this->facilities);

        Flux::modal('cage-form')->close();
    }

    public function deleteCage(int $cageId): void
    {
        abort_unless($this->isManager(), 403);

        $this->scopedCageQuery()->findOrFail($cageId)->delete();

        if ($this->editingCageId === $cageId) {
            $this->resetCageForm();
        }

        unset($this->facilities);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetFacilityForm(): void
    {
        $this->reset(['editingFacilityId', 'facilityName', 'facilityAddress', 'facilityPostalCode', 'facilityCity', 'facilityNotes']);
        $this->resetErrorBag();
    }

    protected function resetWingForm(): void
    {
        $this->reset(['editingWingId', 'wingFacilityId', 'wingName', 'wingDescription']);
        $this->resetErrorBag();
    }

    protected function resetCageForm(): void
    {
        $this->reset(['editingCageId', 'cageWingId', 'cageCode', 'cageCapacity']);
        $this->resetErrorBag();
    }

    /**
     * Wing has no shelter_id of its own, so scope it transitively through
     * its facility (see [[models]] note on MultiShelterTrait).
     */
    protected function scopedWingQuery(): Builder
    {
        return Wing::query()->whereHas(
            'facility',
            fn ($query) => $query->where('shelter_id', Auth::user()->current_shelter_id),
        );
    }

    /**
     * Cage has no shelter_id of its own either, so scope it transitively
     * through its wing's facility.
     */
    protected function scopedCageQuery(): Builder
    {
        return Cage::query()->whereHas(
            'wing.facility',
            fn ($query) => $query->where('shelter_id', Auth::user()->current_shelter_id),
        );
    }

    public function render(): View
    {
        return view('livewire.facilities.manage-spaces');
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Activity;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Activities')]
class ManageActivities extends Component
{
    public ?int $editingActivityId = null;

    public string $activityName = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->is_admin, 403);
    }

    /**
     * @return Collection<int, Activity>
     */
    #[Computed]
    public function activities(): Collection
    {
        return Activity::query()
            ->orderBy('name')
            ->get();
    }

    public function createActivity(): void
    {
        $this->resetActivityForm();
    }

    public function editActivity(int $activityId): void
    {
        $activity = Activity::query()->findOrFail($activityId);

        $this->editingActivityId = $activity->id;
        $this->activityName = $activity->name;
    }

    public function saveActivity(): void
    {
        $validated = $this->validate([
            'activityName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('activities', 'name')->ignore($this->editingActivityId),
            ],
        ], [], [
            'activityName' => __('Name'),
        ]);

        if ($this->editingActivityId !== null) {
            Activity::query()->findOrFail($this->editingActivityId)->update([
                'name' => $validated['activityName'],
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            Activity::query()->create([
                'name' => $validated['activityName'],
            ]);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetActivityForm();
        unset($this->activities);

        Flux::modal('activity-form')->close();
    }

    public function deleteActivity(int $activityId): void
    {
        Activity::query()->findOrFail($activityId)->delete();

        if ($this->editingActivityId === $activityId) {
            $this->resetActivityForm();
        }

        unset($this->activities);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetActivityForm(): void
    {
        $this->reset(['editingActivityId', 'activityName']);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-activities');
    }
}

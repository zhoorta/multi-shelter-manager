<?php

declare(strict_types=1);

namespace App\Livewire\Volunteers;

use App\Models\Volunteer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VolunteerShow extends Component
{
    public Volunteer $volunteer;

    public function mount(Volunteer $volunteer): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if(Auth::user()->isViewerOfCurrentShelter(), 403);

        $this->volunteer = $volunteer->load([
            'activities', 'availabilities', 'species',
            'fosterCages.pets' => fn ($query) => $query->where('status', '!=', 'adopted')->whereNull('date_of_death')->with('species')->orderBy('name'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.volunteers.volunteer-show')->title(__('Volunteer').' — '.$this->volunteer->name);
    }
}

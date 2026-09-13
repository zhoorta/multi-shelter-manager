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
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);

        $this->volunteer = $volunteer;
    }

    public function render(): View
    {
        return view('livewire.volunteers.volunteer-show')->title(__('Volunteer').' — '.$this->volunteer->name);
    }
}

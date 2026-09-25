<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Livewire\Reports\Concerns\BuildsShelterReport;
use App\Models\Shelter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ShelterReportPrint extends Component
{
    use BuildsShelterReport;

    public Shelter $shelter;

    public function mount(): void
    {
        abort_unless(Auth::user()->isManagerOfCurrentShelter(), 403);

        $this->shelter = Auth::user()->currentShelter;
    }

    /**
     * "Activity report 2025" for a calendar year, "Activity report" otherwise
     * (the dates are printed underneath).
     */
    public function reportTitle(): string
    {
        return preg_match('/^\d{4}$/', $this->period) === 1
            ? __('Activity report :year', ['year' => $this->period])
            : __('Activity report');
    }

    #[Layout('layouts.print')]
    public function render(): View
    {
        return view('livewire.reports.shelter-report-print')
            ->title($this->reportTitle().' - '.$this->shelter->name);
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Livewire\Reports\Concerns\BuildsFinanceReport;
use App\Livewire\Reports\Concerns\BuildsHealthReport;
use App\Livewire\Reports\Concerns\BuildsOccupancyReport;
use App\Livewire\Reports\Concerns\BuildsShelterReport;
use App\Models\Shelter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ShelterReportPrint extends Component
{
    use BuildsFinanceReport, BuildsHealthReport, BuildsOccupancyReport, BuildsShelterReport;

    public Shelter $shelter;

    public function mount(): void
    {
        abort_unless(Auth::user()->isManagerOfCurrentShelter(), 403);

        $this->shelter = Auth::user()->currentShelter;
    }

    /**
     * The printed section's title, followed by the year for a calendar-year
     * period ("Activity report 2025"); other periods print their dates below.
     */
    public function reportTitle(): string
    {
        $title = match ($this->activeTab()) {
            'finances' => __('Financial report'),
            'occupancy' => __('Occupancy report'),
            'health' => __('Health report'),
            default => __('Activity report'),
        };

        return preg_match('/^\d{4}$/', $this->period) === 1 ? $title.' '.$this->period : $title;
    }

    #[Layout('layouts.print')]
    public function render(): View
    {
        return view('livewire.reports.shelter-report-print')
            ->title($this->reportTitle().' - '.$this->shelter->name);
    }
}

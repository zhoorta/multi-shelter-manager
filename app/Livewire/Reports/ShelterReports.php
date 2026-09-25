<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Livewire\Reports\Concerns\BuildsFinanceReport;
use App\Livewire\Reports\Concerns\BuildsHealthReport;
use App\Livewire\Reports\Concerns\BuildsOccupancyReport;
use App\Livewire\Reports\Concerns\BuildsShelterReport;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Reports')]
class ShelterReports extends Component
{
    use BuildsFinanceReport, BuildsHealthReport, BuildsOccupancyReport, BuildsShelterReport;

    public function mount(): void
    {
        abort_unless(Auth::user()->isManagerOfCurrentShelter(), 403);

        $this->updatingPeriod($this->period);
    }

    /**
     * Start a custom period from the range that was on screen, so the date
     * inputs are never empty.
     */
    public function updatingPeriod(string $value): void
    {
        if ($value === 'custom' && ($this->parseDate($this->from) === null || $this->parseDate($this->to) === null)) {
            [$start, $end] = $this->dateRange();
            $this->from = $start->format('Y-m-d');
            $this->to = $end->format('Y-m-d');
        }
    }

    public function render(): View
    {
        return view('livewire.reports.shelter-reports');
    }
}

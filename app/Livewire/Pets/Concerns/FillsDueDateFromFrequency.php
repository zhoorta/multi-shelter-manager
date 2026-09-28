<?php

declare(strict_types=1);

namespace App\Livewire\Pets\Concerns;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Locked;

/**
 * Shared "next due date" handling for vaccination and treatment forms:
 * the due date is filled in from the dose date plus the catalogue
 * frequency (e.g. rabies every 36 months, deworming every 3), without ever
 * overwriting a date the user typed.
 */
trait FillsDueDateFromFrequency
{
    public string $administeredDate = '';

    public string $dueDate = '';

    /**
     * The due date last filled in from the frequency, so it can be
     * recalculated while the user hasn't typed a date of their own.
     */
    #[Locked]
    public string $autoFilledDueDate = '';

    /**
     * Frequency in months of the vaccine or treatment currently selected.
     */
    abstract protected function selectedFrequencyMonths(): ?int;

    public function updatedAdministeredDate(): void
    {
        $this->fillDueDateFromFrequency();
    }

    /**
     * Fill the next due date from the administered date plus the frequency.
     * A date the user typed themselves is never overwritten; an auto-filled
     * one is recalculated, or cleared when there's no longer a frequency or
     * dose date to use.
     */
    protected function fillDueDateFromFrequency(): void
    {
        if ($this->dueDate !== '' && $this->dueDate !== $this->autoFilledDueDate) {
            return;
        }

        $frequencyMonths = $this->selectedFrequencyMonths();

        $this->dueDate = $frequencyMonths !== null && Carbon::hasFormat($this->administeredDate, 'Y-m-d')
            ? Carbon::createFromFormat('Y-m-d', $this->administeredDate)->addMonthsNoOverflow($frequencyMonths)->toDateString()
            : '';

        $this->autoFilledDueDate = $this->dueDate;
    }
}

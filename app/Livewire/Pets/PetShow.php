<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use App\Models\Sickness;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PetShow extends Component
{
    public Pet $pet;

    public ?int $editingPaymentId = null;

    public ?int $paymentSponsorshipId = null;

    public string $paymentStartDate = '';

    public string $paymentEndDate = '';

    public string $paymentDate = '';

    public string $paymentValue = '0.00';

    public string $paymentNotes = '';

    public function mount(Pet $pet): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);

        $this->pet = $pet->load([
            'species', 'breed', 'cage.wing.facility', 'images', 'primaryColor', 'secondaryColor', 'furType', 'sicknesses',
            'sponsorships' => fn ($query) => $query->latest()->with(['payments' => fn ($paymentsQuery) => $paymentsQuery->orderByDesc('payment_date')]),
        ]);
    }

    /**
     * Sicknesses that can affect the pet's species (see sickness_species
     * pivot), each to be shown alongside whether the pet has it.
     *
     * @return Collection<int, Sickness>
     */
    #[Computed]
    public function sicknesses(): Collection
    {
        return Sickness::query()
            ->whereHas('species', fn (Builder $query) => $query->whereKey($this->pet->species_id))
            ->orderBy('name')
            ->get();
    }

    public function createPayment(int $sponsorshipId): void
    {
        $this->scopedSponsorshipQuery()->findOrFail($sponsorshipId);

        $this->resetPaymentForm();
        $this->paymentSponsorshipId = $sponsorshipId;

        // Default to today so the date fields start valid: Safari's empty
        // native date input visually looks pre-filled with today's date
        // (see pet-show.blade.php), so leaving the value actually empty
        // makes an unmodified submit fail "required" against a field that
        // looked already filled in.
        $today = now();
        $this->paymentStartDate = $today->format('Y-m-d');
        $this->paymentEndDate = $today->clone()->addYear()->format('Y-m-d');
        $this->paymentDate = $today->format('Y-m-d');
    }

    public function editPayment(int $paymentId): void
    {
        $payment = $this->scopedSponsorshipPaymentQuery()->findOrFail($paymentId);

        $this->editingPaymentId = $payment->id;
        $this->paymentSponsorshipId = $payment->sponsorship_id;
        $this->paymentStartDate = $payment->start_date->format('Y-m-d');
        $this->paymentEndDate = $payment->end_date->format('Y-m-d');
        $this->paymentDate = $payment->payment_date->format('Y-m-d');
        $this->paymentValue = (string) $payment->payment_value;
        $this->paymentNotes = (string) $payment->notes;
    }

    public function savePayment(): void
    {
        $validated = $this->validate([
            'paymentStartDate' => ['required', 'date'],
            'paymentEndDate' => ['required', 'date', 'after_or_equal:paymentStartDate'],
            'paymentDate' => ['required', 'date'],
            'paymentValue' => ['required', 'numeric', 'min:0'],
            'paymentNotes' => ['nullable', 'string'],
        ], [], [
            'paymentStartDate' => __('Start Date'),
            'paymentEndDate' => __('End Date'),
            'paymentDate' => __('Payment Date'),
            'paymentValue' => __('Payment Value'),
            'paymentNotes' => __('Notes'),
        ]);

        $sponsorship = $this->scopedSponsorshipQuery()->findOrFail($this->paymentSponsorshipId);

        $paymentAttributes = [
            'start_date' => $validated['paymentStartDate'],
            'end_date' => $validated['paymentEndDate'],
            'payment_date' => $validated['paymentDate'],
            'payment_value' => $validated['paymentValue'],
            'notes' => $validated['paymentNotes'] !== '' ? $validated['paymentNotes'] : null,
        ];

        if ($this->editingPaymentId !== null) {
            $this->scopedSponsorshipPaymentQuery()->findOrFail($this->editingPaymentId)->update($paymentAttributes);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            $sponsorship->payments()->create($paymentAttributes);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetPaymentForm();
        $this->refreshSponsorships();

        Flux::modal('sponsorship-payment-form')->close();
    }

    public function deletePayment(int $paymentId): void
    {
        $this->scopedSponsorshipPaymentQuery()->findOrFail($paymentId)->delete();

        $this->refreshSponsorships();

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetPaymentForm(): void
    {
        $this->reset([
            'editingPaymentId', 'paymentSponsorshipId', 'paymentStartDate', 'paymentEndDate', 'paymentDate', 'paymentValue', 'paymentNotes',
        ]);
        $this->resetErrorBag();
    }

    /**
     * Reload the pet's sponsorships and their payments after a payment is
     * created, edited or deleted, so the sponsorship box reflects the
     * change without a full page reload.
     */
    protected function refreshSponsorships(): void
    {
        $this->pet->load([
            'sponsorships' => fn ($query) => $query->latest()->with(['payments' => fn ($paymentsQuery) => $paymentsQuery->orderByDesc('payment_date')]),
        ]);
    }

    /**
     * Sponsorship has no shelter_id of its own, so scope it transitively
     * through pet (see .ai/rules/livewire-pets.md).
     */
    protected function scopedSponsorshipQuery(): Builder
    {
        return Sponsorship::query()->where('pet_id', $this->pet->id);
    }

    /**
     * SponsorshipPayment has no shelter_id of its own, so scope it
     * transitively through sponsorship.pet (see .ai/rules/livewire-pets.md).
     */
    protected function scopedSponsorshipPaymentQuery(): Builder
    {
        return SponsorshipPayment::query()->whereHas(
            'sponsorship',
            fn (Builder $query) => $query->where('pet_id', $this->pet->id),
        );
    }

    public function render(): View
    {
        return view('livewire.pets.pet-show')->title($this->pet->name);
    }
}

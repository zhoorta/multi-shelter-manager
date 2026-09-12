<?php

declare(strict_types=1);

namespace App\Livewire\Pets\Concerns;

use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;

/**
 * Shared SponsorshipPayment CRUD for the two pages that render the
 * sponsorship box + payments table (PetShow, which lists every sponsorship
 * for a pet, and SponsorshipShow, which shows a single one). Consumers must
 * implement the scoped query/refresh methods below, since the scope differs
 * (all of a pet's sponsorships vs. a single one).
 */
trait ManagesSponsorshipPayments
{
    public ?int $editingPaymentId = null;

    public ?int $paymentSponsorshipId = null;

    public string $paymentStartDate = '';

    public string $paymentEndDate = '';

    public string $paymentDate = '';

    public string $paymentValue = '0.00';

    public string $paymentNotes = '';

    abstract protected function scopedSponsorshipQuery(): Builder;

    abstract protected function scopedSponsorshipPaymentQuery(): Builder;

    abstract protected function refreshSponsorships(): void;

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
}

<?php

declare(strict_types=1);

namespace App\Livewire\Members;

use App\Models\Member;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MemberShow extends Component
{
    public Member $member;

    public ?int $editingPaymentId = null;

    public string $paymentType = 'membership_fee';

    public string $paymentStartDate = '';

    public string $paymentEndDate = '';

    public string $paymentDate = '';

    public string $paymentValue = '0.00';

    public string $paymentMethod = '';

    public string $paymentNotes = '';

    public function mount(Member $member): void
    {
        abort_if(Auth::user()->is_admin, 403);
        abort_if(Auth::user()->isViewerOfCurrentShelter(), 403);

        $this->member = $member;
        $this->loadPayments();
    }

    protected function loadPayments(): void
    {
        $this->member->load([
            'volunteer',
            'payments' => fn ($query) => $query->orderByDesc('payment_date')->orderByDesc('id'),
        ]);
    }

    /**
     * Pre-fill the payment form: the joining fee, or the member's next
     * membership fee period and amount.
     */
    public function createPayment(string $type): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        $this->resetPaymentForm();
        $this->paymentType = $type === 'joining_fee' ? 'joining_fee' : 'membership_fee';
        $this->paymentDate = now()->format('Y-m-d');

        if ($this->paymentType === 'joining_fee') {
            $this->paymentValue = (string) $this->member->joining_fee;

            return;
        }

        [$start, $end] = $this->member->nextFeePeriod();
        $this->paymentStartDate = $start->format('Y-m-d');
        $this->paymentEndDate = $end->format('Y-m-d');
        $this->paymentValue = (string) $this->member->membership_fee;
    }

    public function editPayment(int $paymentId): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);
        $payment = $this->member->payments()->findOrFail($paymentId);

        $this->resetPaymentForm();
        $this->editingPaymentId = $payment->id;
        $this->paymentType = $payment->type;
        $this->paymentStartDate = (string) $payment->start_date?->format('Y-m-d');
        $this->paymentEndDate = (string) $payment->end_date?->format('Y-m-d');
        $this->paymentDate = $payment->payment_date->format('Y-m-d');
        $this->paymentValue = (string) $payment->payment_value;
        $this->paymentMethod = (string) $payment->payment_method;
        $this->paymentNotes = (string) $payment->notes;
    }

    public function savePayment(): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        $isMembershipFee = $this->paymentType === 'membership_fee';

        $validated = $this->validate([
            'paymentType' => ['required', 'in:joining_fee,membership_fee'],
            'paymentStartDate' => $isMembershipFee ? ['required', 'date'] : ['nullable'],
            'paymentEndDate' => $isMembershipFee ? ['required', 'date', 'after_or_equal:paymentStartDate'] : ['nullable'],
            'paymentDate' => ['required', 'date'],
            'paymentValue' => ['required', 'numeric', 'min:0', 'max:999999'],
            'paymentMethod' => ['nullable', 'in:cash,bank_transfer,mobile,other'],
            'paymentNotes' => ['nullable', 'string'],
        ], [], [
            'paymentType' => __('Type'),
            'paymentStartDate' => __('Start Date'),
            'paymentEndDate' => __('End Date'),
            'paymentDate' => __('Payment Date'),
            'paymentValue' => __('Payment Value'),
            'paymentMethod' => __('Payment Method'),
            'paymentNotes' => __('Notes'),
        ]);

        $attributes = [
            'type' => $validated['paymentType'],
            'start_date' => $isMembershipFee ? $validated['paymentStartDate'] : null,
            'end_date' => $isMembershipFee ? $validated['paymentEndDate'] : null,
            'payment_date' => $validated['paymentDate'],
            'payment_value' => $validated['paymentValue'],
            'payment_method' => $validated['paymentMethod'] !== '' ? $validated['paymentMethod'] : null,
            'notes' => $validated['paymentNotes'] !== '' ? $validated['paymentNotes'] : null,
        ];

        if ($this->editingPaymentId !== null) {
            $this->member->payments()->findOrFail($this->editingPaymentId)->update($attributes);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            $this->member->payments()->create($attributes);

            Flux::toast(variant: 'success', text: __('Record created successfully'));
        }

        $this->resetPaymentForm();
        $this->loadPayments();

        Flux::modal('member-payment-form')->close();
    }

    public function deletePayment(int $paymentId): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        $this->member->payments()->findOrFail($paymentId)->delete();
        $this->loadPayments();

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetPaymentForm(): void
    {
        $this->reset([
            'editingPaymentId', 'paymentType', 'paymentStartDate', 'paymentEndDate', 'paymentDate',
            'paymentValue', 'paymentMethod', 'paymentNotes',
        ]);
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.members.member-show', [
            'owesJoiningFee' => $this->member->owesJoiningFee(),
            'feesPaidUntil' => $this->member->feesPaidUntil(),
            'isInArrears' => $this->member->isInArrears(),
        ])->title(__('Member').' — '.$this->member->name);
    }
}

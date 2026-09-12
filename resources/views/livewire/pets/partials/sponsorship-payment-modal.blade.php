{{--
    Shared "sponsorship-payment-form" modal used by every sponsorship-box
    partial on the including page. Relies on the including Livewire
    component's ManagesSponsorshipPayments-bound properties
    ($editingPaymentId, $paymentStartDate, etc.).
--}}
<flux:modal name="sponsorship-payment-form" class="max-w-lg">
    <form wire:submit="savePayment" class="flex flex-col gap-6">
        <flux:heading size="lg">
            {{ $editingPaymentId ? __('Edit') : __('Create') }} &mdash; {{ __('Sponsorship Payments') }}
        </flux:heading>

        {{-- Safari renders an empty native date input showing today's date instead of a
             blank placeholder, so the field starts as plain text and only switches to the
             native date picker on focus (reverting to text on blur if still empty). --}}
        <flux:input
            type="text"
            wire:model="paymentStartDate"
            :label="__('Start Date')"
            :placeholder="__('Select a date')"
            autocomplete="off"
            clearable
            x-data="{ dateFieldType: 'text' }"
            x-bind:type="dateFieldType"
            x-on:focus="dateFieldType = 'date'"
            x-on:blur="if (! $el.value) dateFieldType = 'text'"
        />

        <flux:input
            type="text"
            wire:model="paymentEndDate"
            :label="__('End Date')"
            :placeholder="__('Select a date')"
            autocomplete="off"
            clearable
            x-data="{ dateFieldType: 'text' }"
            x-bind:type="dateFieldType"
            x-on:focus="dateFieldType = 'date'"
            x-on:blur="if (! $el.value) dateFieldType = 'text'"
        />

        <flux:input
            type="text"
            wire:model="paymentDate"
            :label="__('Payment Date')"
            :placeholder="__('Select a date')"
            autocomplete="off"
            clearable
            x-data="{ dateFieldType: 'text' }"
            x-bind:type="dateFieldType"
            x-on:focus="dateFieldType = 'date'"
            x-on:blur="if (! $el.value) dateFieldType = 'text'"
        />

        <flux:input wire:model="paymentValue" type="number" step="0.01" min="0" :label="__('Payment Value')" />

        <flux:textarea wire:model="paymentNotes" :label="__('Notes')" />

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary">
                {{ $editingPaymentId ? __('Save') : __('Create') }}
            </flux:button>
        </div>
    </form>
</flux:modal>

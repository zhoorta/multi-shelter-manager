{{--
    Renders one sponsorship's details plus its payments management table.
    Expects $pet and $sponsorship. Included by both pet-show.blade.php
    (looping over every sponsorship for a pet) and sponsorship-show.blade.php
    (a single sponsorship) — the wire:click calls below (createPayment,
    editPayment, deletePayment) rely on the including Livewire component
    using the ManagesSponsorshipPayments trait.
--}}
<div wire:key="sponsorship-{{ $sponsorship->id }}" class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
    <div class="flex w-full items-center justify-between">
        <flux:label>{{ __('Sponsorship') }}</flux:label>

        <flux:button
            :href="route('pets.sponsor.edit', [$pet, $sponsorship])"
            variant="filled"
            size="sm"
            icon="pencil"
            wire:navigate
        >
            {{ __('Edit') }}
        </flux:button>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Name') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $sponsorship->name ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $sponsorship->email ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Phone') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $sponsorship->phone ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Address') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $sponsorship->address ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Postal Code') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $sponsorship->postal_code ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('City') }}</flux:text>
            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $sponsorship->city ?? '—' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Send Feedback') }}</flux:text>
            <flux:badge size="sm" :color="$sponsorship->send_feedback ? 'lime' : 'zinc'">{{ $sponsorship->send_feedback ? __('Yes') : __('No') }}</flux:badge>
        </div>

        <div>
            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Send Newsletter') }}</flux:text>
            <flux:badge size="sm" :color="$sponsorship->send_newsletter ? 'lime' : 'zinc'">{{ $sponsorship->send_newsletter ? __('Yes') : __('No') }}</flux:badge>
        </div>
    </div>

    <div>
        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Notes') }}</flux:text>
        <flux:text class="text-neutral-700 dark:text-neutral-300 whitespace-pre-line">{{ $sponsorship->notes ?? '—' }}</flux:text>
    </div>

    <div class="flex w-full items-center justify-between pt-2">
        <flux:label>{{ __('Sponsorship Payments') }}</flux:label>

        <flux:modal.trigger name="sponsorship-payment-form">
            <flux:button
                variant="filled"
                size="sm"
                icon="plus"
                wire:click="createPayment({{ $sponsorship->id }})"
            >
                {{ __('Add Payment') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div class="w-full overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                    <tr>
                        <th scope="col" class="px-4 py-2 font-medium">{{ __('Start Date') }}</th>
                        <th scope="col" class="px-4 py-2 font-medium">{{ __('End Date') }}</th>
                        <th scope="col" class="px-4 py-2 font-medium">{{ __('Payment Date') }}</th>
                        <th scope="col" class="px-4 py-2 font-medium">{{ __('Payment Value') }}</th>
                        <th scope="col" class="px-4 py-2 font-medium">{{ __('Notes') }}</th>
                        <th scope="col" class="px-4 py-2 font-medium">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($sponsorship->payments as $payment)
                        <tr wire:key="sponsorship-payment-{{ $payment->id }}">
                            <td class="px-4 py-2">{{ $payment->start_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $payment->end_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ number_format((float) $payment->payment_value, 2, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $payment->notes ?? '—' }}</td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <flux:modal.trigger name="sponsorship-payment-form">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="pencil"
                                            wire:click="editPayment({{ $payment->id }})"
                                            :aria-label="__('Edit')"
                                        />
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="confirm-payment-deletion-{{ $payment->id }}">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="trash"
                                            :aria-label="__('Delete')"
                                        />
                                    </flux:modal.trigger>

                                    <flux:modal name="confirm-payment-deletion-{{ $payment->id }}" class="max-w-lg">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                            </div>

                                            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                <flux:modal.close>
                                                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                </flux:modal.close>

                                                <flux:button variant="danger" wire:click="deletePayment({{ $payment->id }})">
                                                    {{ __('Delete') }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-neutral-500 dark:text-neutral-400">
                                {{ __('No sponsorship payments registered') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

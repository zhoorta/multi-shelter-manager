<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $member->name }}</flux:heading>
            <flux:subheading>{{ __('Member') }} {{ __('No.') }} {{ $member->member_number }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            <flux:button :href="route('members.index')" variant="filled" icon="arrow-left" wire:navigate>
                {{ __('Members') }}
            </flux:button>

            <flux:button :href="route('members.edit', $member)" variant="primary" icon="pencil" wire:navigate>
                {{ __('Edit') }}
            </flux:button>
        </div>
    </div>

    <div class="flex flex-col gap-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div>
            <flux:heading>{{ __('Identification') }}</flux:heading>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Member Number') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $member->member_number }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('TIN') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $member->tin ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Volunteer') }}</flux:text>
                    @if ($member->volunteer)
                        <flux:link :href="route('volunteers.show', $member->volunteer)" wire:navigate>{{ $member->volunteer->name }}</flux:link>
                    @else
                        <flux:text class="text-neutral-700 dark:text-neutral-300">—</flux:text>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <flux:heading>{{ __('Contacts') }}</flux:heading>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $member->email ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Phone') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $member->phone ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Address') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $member->address ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Postal Code') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $member->postal_code ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('City') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $member->city ?? '—' }}</flux:text>
                </div>
            </div>
        </div>

        <div>
            <flux:heading>{{ __('Membership') }}</flux:heading>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Join Date') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $member->join_date->format('d/m/Y') }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</flux:text>
                    <div class="flex flex-wrap gap-1">
                        <flux:badge size="sm" :color="match ($member->status) { 'active' => 'lime', 'suspended' => 'amber', default => 'zinc' }">
                            {{ __('member_'.$member->status) }}
                        </flux:badge>

                        @if ($isInArrears)
                            <flux:badge size="sm" color="red">{{ __('Fees overdue') }}</flux:badge>
                        @endif
                    </div>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Joining Fee') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">
                        {{ number_format((float) $member->joining_fee, 2, ',', '.') }} €
                        @if ((float) $member->joining_fee > 0)
                            &middot; {{ $owesJoiningFee ? __('Not paid') : __('Paid') }}
                        @endif
                    </flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Membership Fee') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ number_format((float) $member->membership_fee, 2, ',', '.') }} € ({{ __($member->membership_fee_frequency) }})</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Paid until') }}</flux:text>
                    <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $feesPaidUntil?->format('d/m/Y') ?? '—' }}</flux:text>
                </div>
            </div>
        </div>

        @if ($member->notes)
            <div>
                <flux:heading>{{ __('Notes') }}</flux:heading>
                <flux:text class="text-neutral-700 dark:text-neutral-300 mt-2 whitespace-pre-line">{{ $member->notes }}</flux:text>
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex w-full flex-wrap items-center justify-between gap-2">
            <flux:label>{{ __('Payments') }}</flux:label>

            <div class="flex items-center gap-2">
                @if ($owesJoiningFee)
                    <flux:modal.trigger name="member-payment-form">
                        <flux:button variant="filled" size="sm" icon="plus" wire:click="createPayment('joining_fee')">
                            {{ __('Joining Fee') }}
                        </flux:button>
                    </flux:modal.trigger>
                @endif

                <flux:modal.trigger name="member-payment-form">
                    <flux:button variant="filled" size="sm" icon="plus" wire:click="createPayment('membership_fee')">
                        {{ __('Membership Fee') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        <div class="w-full overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-4 py-2 font-medium">{{ __('Type') }}</th>
                            <th scope="col" class="px-4 py-2 font-medium">{{ __('Period') }}</th>
                            <th scope="col" class="px-4 py-2 font-medium">{{ __('Payment Date') }}</th>
                            <th scope="col" class="px-4 py-2 font-medium">{{ __('Payment Value') }}</th>
                            <th scope="col" class="px-4 py-2 font-medium">{{ __('Payment Method') }}</th>
                            <th scope="col" class="px-4 py-2 font-medium">{{ __('Notes') }}</th>
                            <th scope="col" class="px-4 py-2 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($member->payments as $payment)
                            <tr wire:key="member-payment-{{ $payment->id }}">
                                <td class="px-4 py-2">{{ $payment->type === 'joining_fee' ? __('Joining Fee') : __('Membership Fee') }}</td>
                                <td class="px-4 py-2">
                                    {{ $payment->start_date ? $payment->start_date->format('d/m/Y').' – '.$payment->end_date?->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-4 py-2">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2">{{ number_format((float) $payment->payment_value, 2, ',', '.') }} €</td>
                                <td class="px-4 py-2">{{ $payment->payment_method ? __('payment_'.$payment->payment_method) : '—' }}</td>
                                <td class="px-4 py-2">{{ $payment->notes ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="member-payment-form">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="pencil"
                                                wire:click="editPayment({{ $payment->id }})"
                                                :aria-label="__('Edit')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal.trigger name="confirm-member-payment-deletion-{{ $payment->id }}">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="trash"
                                                :aria-label="__('Delete')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal name="confirm-member-payment-deletion-{{ $payment->id }}" class="max-w-lg">
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
                                <td colspan="7" class="px-4 py-4 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No payments registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <flux:modal name="member-payment-form" class="max-w-lg">
        <form wire:submit="savePayment" class="flex flex-col gap-6">
            <flux:heading size="lg">
                {{ $editingPaymentId ? __('Edit') : __('Create') }} &mdash; {{ $paymentType === 'joining_fee' ? __('Joining Fee') : __('Membership Fee') }}
            </flux:heading>

            @if ($paymentType === 'membership_fee')
                {{-- Safari renders an empty native date input showing today's date instead of a
                     blank placeholder, so the field starts as plain text and only switches to the
                     native date picker on focus (reverting to text on blur if still empty). --}}
                <div class="grid grid-cols-2 gap-4">
                    <flux:input
                        type="text"
                        wire:model="paymentStartDate"
                        :label="__('Start Date')"
                        :placeholder="__('Select a date')"
                        autocomplete="off"
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
                        x-data="{ dateFieldType: 'text' }"
                        x-bind:type="dateFieldType"
                        x-on:focus="dateFieldType = 'date'"
                        x-on:blur="if (! $el.value) dateFieldType = 'text'"
                    />
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <flux:input
                    type="text"
                    wire:model="paymentDate"
                    :label="__('Payment Date')"
                    :placeholder="__('Select a date')"
                    autocomplete="off"
                    x-data="{ dateFieldType: 'text' }"
                    x-bind:type="dateFieldType"
                    x-on:focus="dateFieldType = 'date'"
                    x-on:blur="if (! $el.value) dateFieldType = 'text'"
                />

                <flux:input wire:model="paymentValue" type="number" step="0.01" min="0" :label="__('Payment Value')" />
            </div>

            <flux:select wire:model="paymentMethod" :label="__('Payment Method')">
                <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                <flux:select.option value="cash">{{ __('payment_cash') }}</flux:select.option>
                <flux:select.option value="bank_transfer">{{ __('payment_bank_transfer') }}</flux:select.option>
                <flux:select.option value="mobile">{{ __('payment_mobile') }}</flux:select.option>
                <flux:select.option value="other">{{ __('payment_other') }}</flux:select.option>
            </flux:select>

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
</div>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Members') }}</flux:heading>

        <div class="flex items-center gap-2">
            @if (auth()->user()->isManagerOfCurrentShelter())
                <flux:modal.trigger name="member-fee-defaults">
                    <flux:button variant="filled" icon="cog-6-tooth" wire:click="editFeeDefaults">
                        {{ __('Fees') }}
                    </flux:button>
                </flux:modal.trigger>
            @endif

            <flux:button variant="primary" icon="plus" :href="route('members.create')" wire:navigate>
                {{ __('Create') }}
            </flux:button>
        </div>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :label="__('Search')"
            :placeholder="__('Search by number, name, phone, email, TIN or notes')"
            class="sm:max-w-xs"
        />

        <flux:select wire:model.live="statusFilter" :label="__('Status')" class="sm:max-w-xs">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            <flux:select.option value="active">{{ __('member_active') }}</flux:select.option>
            <flux:select.option value="suspended">{{ __('member_suspended') }}</flux:select.option>
            <flux:select.option value="left">{{ __('member_left') }}</flux:select.option>
        </flux:select>

        <div class="flex items-center sm:h-10">
            <flux:switch wire:model.live="inArrearsOnly" :label="__('Fees overdue only')" align="left" />
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('No.') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Identification and Contacts') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Membership Fee') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->members as $item)
                            <tr wire:key="member-{{ $item->id }}">
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">{{ $item->member_number }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('members.show', $item) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $item->name }}</a>
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ $item->phone ?? '—' }}</span>
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ $item->email ?? '—' }}</span>
                                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('Member since') }} {{ $item->join_date->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-col gap-1 text-neutral-500 dark:text-neutral-400">
                                        <span>{{ number_format((float) $item->membership_fee, 2, ',', '.') }} € ({{ __($item->membership_fee_frequency) }})</span>
                                        <span>{{ __('Paid until') }} {{ $item->fees_paid_until ? \Illuminate\Support\Carbon::parse($item->fees_paid_until)->format('d/m/Y') : '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-col items-start gap-1">
                                        <flux:badge size="sm" :color="match ($item->status) { 'active' => 'lime', 'suspended' => 'amber', default => 'zinc' }">
                                            {{ __('member_'.$item->status) }}
                                        </flux:badge>

                                        @if (in_array($item->id, $this->inArrearsIds, true))
                                            <flux:badge size="sm" color="red">{{ __('Fees overdue') }}</flux:badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="eye"
                                            :href="route('members.show', $item)"
                                            :aria-label="__('View')"
                                            wire:navigate
                                        />

                                        @if (auth()->user()->isManagerOfCurrentShelter())
                                            <flux:modal.trigger name="confirm-member-deletion-{{ $item->id }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="subtle"
                                                    icon="trash"
                                                    :aria-label="__('Delete')"
                                                />
                                            </flux:modal.trigger>

                                            <flux:modal name="confirm-member-deletion-{{ $item->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <div>
                                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                    </div>

                                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                        </flux:modal.close>

                                                        <flux:button variant="danger" wire:click="deleteMember({{ $item->id }})">
                                                            {{ __('Delete') }}
                                                        </flux:button>
                                                    </div>
                                                </div>
                                            </flux:modal>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No members registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="px-6 py-3">
        <flux:pagination :paginator="$this->members" class="!border-t-0 !pt-0" />
    </div>

    @if (auth()->user()->isManagerOfCurrentShelter())
        <flux:modal name="member-fee-defaults" class="max-w-lg">
            <form wire:submit="saveFeeDefaults" class="flex flex-col gap-6">
                <div>
                    <flux:heading size="lg">{{ __('Fees') }}</flux:heading>
                    <flux:subheading>{{ __('Default values for new members. They can be changed for each member.') }}</flux:subheading>
                </div>

                <flux:input wire:model="defaultJoiningFee" type="number" step="0.01" min="0" :label="__('Joining Fee')" :description="__('One-time fee paid when joining. Use 0 if there is none.')" />

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="defaultMembershipFee" type="number" step="0.01" min="0" :label="__('Membership Fee')" />

                    <flux:select wire:model="defaultMembershipFeeFrequency" :label="__('Frequency')">
                        <flux:select.option value="monthly">{{ __('monthly') }}</flux:select.option>
                        <flux:select.option value="quarterly">{{ __('quarterly') }}</flux:select.option>
                        <flux:select.option value="semiannual">{{ __('semiannual') }}</flux:select.option>
                        <flux:select.option value="yearly">{{ __('yearly') }}</flux:select.option>
                    </flux:select>
                </div>

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>

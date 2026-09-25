<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">
                {{ $member ? __('Edit') : __('Create') }} &mdash; {{ __('Members') }}
            </flux:heading>

            @if ($member)
                <flux:subheading>{{ $member->name }}</flux:subheading>
            @endif
        </div>

        <flux:button :href="$member ? route('members.show', $member) : route('members.index')" variant="filled" icon="arrow-left" wire:navigate>
            {{ $member ? $member->name : __('Members') }}
        </flux:button>
    </div>

    <form wire:submit="saveMember" autocomplete="off" class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-3 gap-4">
                <flux:input
                    wire:model="memberNumber"
                    type="number"
                    min="1"
                    :label="__('Member Number')"
                    :placeholder="$member ? null : __('Automatic')"
                />
                <flux:input wire:model="memberName" :label="__('Name')" field:class="col-span-2" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="memberTin" :label="__('TIN')" />

                <flux:select wire:model="memberVolunteerId" :label="__('Volunteer')">
                    <flux:select.option value="">{{ __('Not a volunteer') }}</flux:select.option>
                    @foreach ($this->volunteers as $volunteer)
                        <flux:select.option value="{{ $volunteer->id }}">{{ $volunteer->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="memberEmail" type="email" :label="__('Email')" />
                <flux:input wire:model="memberPhone" :label="__('Phone')" />
            </div>

            <flux:input wire:model="memberAddress" :label="__('Address')" />

            <div class="grid grid-cols-3 gap-4">
                <flux:input wire:model="memberPostalCode" :label="__('Postal Code')" />
                <flux:input wire:model="memberCity" :label="__('City')" field:class="col-span-2" />
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-2 gap-4">
                {{-- Safari renders an empty native date input showing today's date instead of a
                     blank placeholder, so the field starts as plain text and only switches to the
                     native date picker on focus (reverting to text on blur if still empty). --}}
                <flux:input
                    type="text"
                    wire:model="memberJoinDate"
                    :label="__('Join Date')"
                    :placeholder="__('Select a date')"
                    autocomplete="off"
                    x-data="{ dateFieldType: 'text' }"
                    x-bind:type="dateFieldType"
                    x-on:focus="dateFieldType = 'date'"
                    x-on:blur="if (! $el.value) dateFieldType = 'text'"
                />

                <flux:select wire:model="memberStatus" :label="__('Status')">
                    <flux:select.option value="active">{{ __('member_active') }}</flux:select.option>
                    <flux:select.option value="suspended">{{ __('member_suspended') }}</flux:select.option>
                    <flux:select.option value="left">{{ __('member_left') }}</flux:select.option>
                </flux:select>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <flux:input wire:model="memberJoiningFee" type="number" step="0.01" min="0" :label="__('Joining Fee')" />
                <flux:input wire:model="memberMembershipFee" type="number" step="0.01" min="0" :label="__('Membership Fee')" />

                <flux:select wire:model="memberMembershipFeeFrequency" :label="__('Frequency')">
                    <flux:select.option value="monthly">{{ __('monthly') }}</flux:select.option>
                    <flux:select.option value="quarterly">{{ __('quarterly') }}</flux:select.option>
                    <flux:select.option value="yearly">{{ __('yearly') }}</flux:select.option>
                </flux:select>
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:textarea wire:model="memberNotes" :label="__('Notes')" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="$member ? route('members.show', $member) : route('members.index')" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ $member ? __('Save') : __('Create') }}
            </flux:button>
        </div>
    </form>
</div>

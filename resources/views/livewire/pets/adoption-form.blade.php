<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $adoption !== null ? __('Edit Adoption') : __('Adoption Registration') }}</flux:heading>
            <flux:subheading>{{ $pet->name }}</flux:subheading>
        </div>

        <flux:button :href="$backRoute" variant="filled" icon="arrow-left" wire:navigate>
            {{ $backLabel }}
        </flux:button>
    </div>

    <form wire:submit="saveAdoption" autocomplete="off" class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:input wire:model="adopterName" :label="__('Owner Name')" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="adopterEmail" type="email" :label="__('Email')" />
                <flux:input wire:model="adopterPhone" :label="__('Phone')" />
            </div>

            <flux:input wire:model="adopterAddress" :label="__('Address')" />

            <div class="grid grid-cols-3 gap-4">
                <flux:input wire:model="adopterPostalCode" :label="__('Postal Code')" />
                <flux:input wire:model="adopterCity" :label="__('City')" field:class="col-span-2" />
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            {{-- Safari renders an empty native date input showing today's date instead of a
                 blank placeholder, so the field starts as plain text and only switches to the
                 native date picker on focus (reverting to text on blur if still empty). --}}
            <flux:input
                type="text"
                wire:model="adoptionDate"
                :label="__('Adoption Date')"
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
                wire:model="returnDate"
                :label="__('Return Date')"
                :placeholder="__('Select a date')"
                autocomplete="off"
                clearable
                x-data="{ dateFieldType: 'text' }"
                x-bind:type="dateFieldType"
                x-on:focus="dateFieldType = 'date'"
                x-on:blur="if (! $el.value) dateFieldType = 'text'"
            />

            <flux:input wire:model="adoptionFee" type="number" step="0.01" min="0" :label="__('Adoption Fee')" />

            <flux:select wire:model="applicationStatus" :label="__('Application Status')">
                <flux:select.option value="Pending">{{ __('Pending') }}</flux:select.option>
                <flux:select.option value="Approved">{{ __('Approved') }}</flux:select.option>
                <flux:select.option value="Rejected">{{ __('Rejected') }}</flux:select.option>
            </flux:select>

            <flux:textarea wire:model="adoptionNotes" :label="__('Notes')" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="$backRoute" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
</div>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $petVaccine !== null ? __('Edit Vaccination') : __('New Vaccination') }}</flux:heading>
            <flux:subheading>{{ $pet->name }}</flux:subheading>
        </div>

        <flux:button :href="route('pets.show', $pet)" variant="filled" icon="arrow-left" wire:navigate>
            {{ $pet->name }}
        </flux:button>
    </div>

    <form wire:submit="saveVaccination" autocomplete="off" class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:select wire:model="vaccineId" :label="__('Vaccine')" :placeholder="__('Select a vaccine')">
                @foreach ($this->vaccines as $vaccine)
                    <flux:select.option value="{{ $vaccine->id }}">{{ $vaccine->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                {{-- Safari renders an empty native date input showing today's date instead of a
                     blank placeholder, so the field starts as plain text and only switches to the
                     native date picker on focus (reverting to text on blur if still empty). --}}
                <flux:input
                    type="text"
                    wire:model="administeredDate"
                    :label="__('Administered Date')"
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
                    wire:model="dueDate"
                    :label="__('Due Date')"
                    :placeholder="__('Select a date')"
                    autocomplete="off"
                    clearable
                    x-data="{ dateFieldType: 'text' }"
                    x-bind:type="dateFieldType"
                    x-on:focus="dateFieldType = 'date'"
                    x-on:blur="if (! $el.value) dateFieldType = 'text'"
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="lotNumber" :label="__('Lot Number')" />
                <flux:input wire:model="veterinarianName" :label="__('Veterinarian')" />
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:textarea wire:model="vaccinationNotes" :label="__('Notes')" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="route('pets.show', $pet)" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
</div>

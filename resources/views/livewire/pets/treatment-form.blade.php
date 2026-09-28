<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $petTreatment !== null ? __('Edit Treatment') : __('New Treatment') }}</flux:heading>
            <flux:subheading>{{ $pet->name }}</flux:subheading>
        </div>

        <flux:button :href="route('pets.show', $pet)" variant="filled" icon="arrow-left" wire:navigate>
            {{ $pet->name }}
        </flux:button>
    </div>

    <form wire:submit="saveTreatment" autocomplete="off" class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:select wire:model.live="treatmentId" :label="__('Treatment')" :placeholder="__('Select a treatment')">
                @foreach ($this->treatments as $treatment)
                    <flux:select.option value="{{ $treatment->id }}">{{ $treatment->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                {{-- Safari renders an empty native date input showing today's date instead of a
                     blank placeholder, so the field starts as plain text and only switches to the
                     native date picker on focus (reverting to text on blur if still empty). --}}
                <flux:input
                    type="text"
                    wire:model.live="administeredDate"
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
                    :label="__('Next Due Date')"
                    :placeholder="__('Select a date')"
                    autocomplete="off"
                    clearable
                    x-data="{ dateFieldType: 'text' }"
                    x-bind:type="dateFieldType"
                    x-on:focus="dateFieldType = 'date'"
                    x-on:blur="if (! $el.value) dateFieldType = 'text'"
                />

                {{-- Its own grid row under Next Due Date, rather than a field description,
                     so both date inputs keep the same height and stay aligned. --}}
                @if ($this->selectedTreatment?->frequency_months !== null)
                    <div></div>
                    <flux:text class="-mt-2.5">
                        {{ __('Filled in from the treatment frequency (every :months months). You can change it.', ['months' => $this->selectedTreatment->frequency_months]) }}
                    </flux:text>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="product" :label="__('Product')" :placeholder="__('e.g. Drontal, Bravecto')" />
                <flux:input wire:model="veterinarianName" :label="__('Veterinarian')" />
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:textarea wire:model="treatmentNotes" :label="__('Notes')" />
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

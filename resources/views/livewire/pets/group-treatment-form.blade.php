<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ __('Group Treatment') }}</flux:heading>
            <flux:subheading>{{ __('Record the same treatment for several animals at once.') }}</flux:subheading>
        </div>

        <flux:button :href="route('pets.treatments.index')" variant="filled" icon="arrow-left" wire:navigate>
            {{ __('Treatments') }}
        </flux:button>
    </div>

    <form wire:submit="saveGroupTreatment" autocomplete="off" class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:select wire:model.live="treatmentId" :label="__('Treatment')" :placeholder="__('Select a treatment')">
                @foreach ($this->treatments as $treatment)
                    <flux:select.option value="{{ $treatment->id }}">{{ $treatment->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input type="date" wire:model.live="administeredDate" :label="__('Administered Date')" />

                {{-- Safari renders an empty native date input showing today's date instead of a
                     blank placeholder, so the field starts as plain text and only switches to the
                     native date picker on focus (reverting to text on blur if still empty). --}}
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

            <flux:textarea wire:model="treatmentNotes" :label="__('Notes')" rows="2" />
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <flux:label>{{ __('Animals') }}</flux:label>

                @if ($this->candidatePets->isNotEmpty())
                    <div class="flex items-center gap-2">
                        <flux:text>{{ __(':selected of :total selected', ['selected' => count($selectedPetIds), 'total' => $this->candidatePets->count()]) }}</flux:text>
                        <flux:button size="sm" variant="subtle" wire:click="selectAllCandidates">{{ __('Select all') }}</flux:button>
                        <flux:button size="sm" variant="subtle" wire:click="deselectAllCandidates">{{ __('Select none') }}</flux:button>
                    </div>
                @endif
            </div>

            @if ($this->selectedTreatment === null)
                <flux:text>{{ __('Choose a treatment to list the resident animals it applies to.') }}</flux:text>
            @else
                <div class="flex flex-col gap-4 sm:flex-row">
                    @if ($this->selectedTreatment->species->count() > 1)
                        <flux:select wire:model.live="speciesFilter" class="sm:max-w-xs">
                            <flux:select.option value="">{{ __('All') }}</flux:select.option>
                            @foreach ($this->selectedTreatment->species as $species)
                                <flux:select.option value="{{ $species->id }}">{{ $species->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif

                    <flux:select wire:model.live="locationFilter" class="sm:max-w-xs">
                        <flux:select.option value="">{{ __('All Locations') }}</flux:select.option>
                        @foreach ($this->facilities as $facility)
                            <flux:select.option value="facility:{{ $facility->id }}">{{ $facility->name }}</flux:select.option>
                            @foreach ($facility->wings as $wing)
                                <flux:select.option value="wing:{{ $wing->id }}">{{ str_repeat("\u{00A0}", 4) }}{{ $wing->name }}</flux:select.option>
                            @endforeach
                        @endforeach
                    </flux:select>
                </div>

                @if ($this->candidatePets->isEmpty())
                    <flux:text>{{ __('No resident animals match these filters.') }}</flux:text>
                @else
                    <flux:checkbox.group wire:model="selectedPetIds" class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($this->candidatePets as $candidatePet)
                            <flux:checkbox
                                wire:key="candidate-pet-{{ $candidatePet->id }}"
                                value="{{ $candidatePet->id }}"
                                :label="$candidatePet->name.' ('.$candidatePet->ref.')'"
                                :description="collect([$candidatePet->species?->name, $candidatePet->cage?->wing?->name, $candidatePet->cage?->code])->filter()->implode(' · ')"
                            />
                        @endforeach
                    </flux:checkbox.group>
                @endif
            @endif

            <flux:error name="selectedPetIds" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="route('pets.treatments.index')" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
</div>

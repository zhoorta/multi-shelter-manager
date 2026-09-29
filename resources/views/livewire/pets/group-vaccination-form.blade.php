<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ __('Group Vaccination') }}</flux:heading>
            <flux:subheading>{{ __('Record the same vaccine for several animals at once, e.g. on the day the vet vaccinates a group.') }}</flux:subheading>
        </div>

        <flux:button :href="route('pets.vaccinations.index')" variant="filled" icon="arrow-left" wire:navigate>
            {{ __('Vaccinations') }}
        </flux:button>
    </div>

    <form wire:submit="saveGroupVaccination" autocomplete="off" class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:select wire:model.live="vaccineId" :label="__('Vaccine')" :placeholder="__('Select a vaccine')">
                @foreach ($this->vaccines as $vaccine)
                    <flux:select.option value="{{ $vaccine->id }}">{{ $vaccine->name }} ({{ $vaccine->species->pluck('name')->implode(', ') }})</flux:select.option>
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

                @if ($this->selectedVaccine?->frequency_months !== null)
                    <div></div>
                    <flux:text class="-mt-2.5">
                        {{ __('Filled in from the vaccine frequency (every :months months). You can change it.', ['months' => $this->selectedVaccine->frequency_months]) }}
                    </flux:text>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="lotNumber" :label="__('Lot Number')" />
                <flux:input wire:model="veterinarianName" :label="__('Veterinarian')" />
            </div>

            <flux:textarea wire:model="vaccinationNotes" :label="__('Notes')" rows="2" />
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

            @if ($this->selectedVaccine === null)
                <flux:text>{{ __('Choose a vaccine to list the resident animals it applies to.') }}</flux:text>
            @else
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <flux:select wire:model.live="dueMonth" class="sm:max-w-xs">
                        @foreach ($this->dueMonthOptions as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    @if ($this->selectedVaccine->species->count() > 1)
                        <flux:select wire:model.live="speciesFilter" class="sm:max-w-xs">
                            <flux:select.option value="">{{ __('All') }}</flux:select.option>
                            @foreach ($this->selectedVaccine->species as $species)
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
                                :description="collect([$candidatePet->species?->name, $candidatePet->cage?->wing?->name, $candidatePet->cage?->code, isset($this->pendingDueDates[$candidatePet->id]) ? __('due :date', ['date' => $this->pendingDueDates[$candidatePet->id]]) : null])->filter()->implode(' · ')"
                            />
                        @endforeach
                    </flux:checkbox.group>
                @endif
            @endif

            <flux:error name="selectedPetIds" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="route('pets.vaccinations.index')" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
</div>

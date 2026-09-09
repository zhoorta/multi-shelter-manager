<div class="flex h-full w-full flex-1 flex-col gap-6">
    @php
        $petsIndexRoute = route('pets.index', $this->currentSpecies ? ['speciesFilter' => $this->currentSpecies->id] : []);
    @endphp

    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">
                {{ $pet ? __('Edit') : __('Create') }} &mdash; {{ $this->currentSpecies?->name_plural ?? __('Pets') }}
            </flux:heading>

            @if ($pet)
                <flux:subheading>{{ $pet->name }}</flux:subheading>
            @endif
        </div>

        <flux:button :href="$pet ? route('pets.show', $pet) : $petsIndexRoute" variant="filled" icon="arrow-left" wire:navigate>
            {{ $pet ? $pet->name : ($this->currentSpecies?->name_plural ?? __('Pets')) }}
        </flux:button>
    </div>

    <form wire:submit="savePet" autocomplete="off" class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:label>{{ __('Photos') }}</flux:label>

            @if ($pet && $this->petImages->isNotEmpty())
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                    @foreach ($this->petImages as $image)
                        <div wire:key="pet-image-{{ $image->id }}" class="group relative">
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}"
                                alt="{{ __('Photo') }}"
                                @class([
                                    'h-24 w-full rounded-lg object-cover ring-1',
                                    'ring-2 ring-neutral-900 dark:ring-white' => $image->is_main,
                                    'ring-neutral-200 dark:ring-neutral-700' => ! $image->is_main,
                                ])
                            >

                            @if ($image->is_main)
                                <flux:badge size="sm" class="absolute left-1 top-1">{{ __('Main') }}</flux:badge>
                            @endif

                            <div class="absolute inset-x-0 bottom-1 flex items-center justify-center gap-1 opacity-0 transition group-hover:opacity-100">
                                @unless ($image->is_main)
                                    <flux:button
                                        size="sm"
                                        variant="subtle"
                                        wire:click="setMainPetImage({{ $image->id }})"
                                        :aria-label="__('Set as Main')"
                                        icon="star"
                                    />
                                @endunless

                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    icon="trash"
                                    wire:click="deletePetImage({{ $image->id }})"
                                    :aria-label="__('Delete')"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($petPhotos)
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                    @foreach ($petPhotos as $photo)
                        <img
                            src="{{ $photo->temporaryUrl() }}"
                            alt="{{ __('Photo') }}"
                            class="h-24 w-full rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                        >
                    @endforeach
                </div>
            @endif

            <flux:field>
                <input
                    type="file"
                    wire:model="petPhotos"
                    multiple
                    accept="image/*"
                    class="block w-full text-sm text-neutral-700 file:mr-4 file:rounded-lg file:border-0 file:bg-neutral-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-neutral-700 dark:text-neutral-300 dark:file:bg-white dark:file:text-neutral-900 dark:hover:file:bg-neutral-200"
                >

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">
                    {{ __('PNG or JPG up to 2MB each. The first photo becomes the main photo.') }}
                </flux:text>

                <div wire:loading wire:target="petPhotos">
                    <flux:text size="sm">{{ __('Uploading') }}&hellip;</flux:text>
                </div>

                <flux:error name="petPhotos.*" />
            </flux:field>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:input wire:model="petName" :label="__('Name')" />

            <flux:input wire:model="petChip" :label="__('Microchip / Chip')" />

            <flux:select wire:model="petBreedId" :label="__('Breed')">
                <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                @foreach ($this->breeds as $item)
                    <flux:select.option value="{{ $item->id }}">{{ $item->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="petGender" :label="__('Gender')">
                <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                <flux:select.option value="male">{{ __('Male') }}</flux:select.option>
                <flux:select.option value="female">{{ __('Female') }}</flux:select.option>
            </flux:select>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:select wire:model="petPrimaryColorId" :label="__('Primary Color')">
                <flux:select.option value="">{{ __('No Color Assigned') }}</flux:select.option>
                @foreach ($this->colors as $item)
                    <flux:select.option value="{{ $item->id }}">{{ $item->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="petSecondaryColorId" :label="__('Secondary Color')">
                <flux:select.option value="">{{ __('No Color Assigned') }}</flux:select.option>
                @foreach ($this->colors as $item)
                    <flux:select.option value="{{ $item->id }}">{{ $item->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="petFurTypeId" :label="__('Fur Type')">
                <flux:select.option value="">{{ __('No Fur Type Assigned') }}</flux:select.option>
                @foreach ($this->furTypes as $item)
                    <flux:select.option value="{{ $item->id }}">{{ $item->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            {{-- Safari renders an empty native date input showing today's date instead of a
                 blank placeholder, so the field starts as plain text and only switches to the
                 native date picker on focus (reverting to text on blur if still empty). --}}
            <flux:input
                type="text"
                wire:model="petBirthDate"
                :label="__('Birth Date')"
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
                wire:model="petDeathDate"
                :label="__('Death Date')"
                :placeholder="__('Select a date')"
                autocomplete="off"
                clearable
                x-data="{ dateFieldType: 'text' }"
                x-bind:type="dateFieldType"
                x-on:focus="dateFieldType = 'date'"
                x-on:blur="if (! $el.value) dateFieldType = 'text'"
            />
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:switch wire:model="petIsNeutered" :label="__('Is Neutered')" align="left" />

            @foreach ($this->sicknesses as $item)
                <flux:switch
                    :checked="in_array($item->id, $petSicknessIds, true)"
                    wire:click="toggleSickness({{ $item->id }})"
                    :label="$item->name"
                    align="left"
                />
            @endforeach
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:switch wire:model="petIsAdoptable" :label="__('Is Adoptable')" align="left" />
            <flux:switch wire:model="petIsSponsorable" :label="__('Is Sponsorable')" align="left" />
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:select wire:model="petStatus" :label="__('Status')">
                <flux:select.option value="available">{{ __('Available') }}</flux:select.option>
                <flux:select.option value="quarantine">{{ __('Quarantine') }}</flux:select.option>
                <flux:select.option value="adopted">{{ __('Adopted') }}</flux:select.option>
                <flux:select.option value="medical">{{ __('Medical') }}</flux:select.option>
            </flux:select>

            <flux:select wire:model="petCageId" :label="__('Cage')">
                <flux:select.option value="">{{ __('No Cage Assigned') }}</flux:select.option>
                @foreach ($this->cages as $item)
                    <flux:select.option value="{{ $item->id }}">{{ $item->wing->name }} &middot; {{ $item->code }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Safari renders an empty native date input showing today's date instead of a
                 blank placeholder, so the field starts as plain text and only switches to the
                 native date picker on focus (reverting to text on blur if still empty). --}}
            <flux:input
                type="text"
                wire:model="petCheckinDate"
                :label="__('Checkin Date')"
                :placeholder="__('Select a date')"
                autocomplete="off"
                clearable
                x-data="{ dateFieldType: 'text' }"
                x-bind:type="dateFieldType"
                x-on:focus="dateFieldType = 'date'"
                x-on:blur="if (! $el.value) dateFieldType = 'text'"
            />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="$pet ? route('pets.show', $pet) : $petsIndexRoute" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ $pet ? __('Save') : __('Create') }}
            </flux:button>
        </div>
    </form>
</div>

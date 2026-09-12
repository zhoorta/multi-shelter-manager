<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ $this->selectedSpecies?->name_plural ?? __('Pets') }}</flux:heading>

        {{-- A full page load (no wire:navigate) here is deliberate: navigate morphs
             the previous page's DOM into the new one, and a native <select> can keep
             its browser-side selected option across that morph even though the fresh
             component's bound property is null — a fresh request guarantees a clean
             form every time. --}}
        <flux:button
            :href="route('pets.create', $this->selectedSpecies ? ['species' => $this->selectedSpecies->id] : [])"
            variant="primary"
            icon="plus"
        >
            {{ __('Create') }}
        </flux:button>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :placeholder="__('Search by name or microchip')"
            class="sm:max-w-xs"
        />

        <flux:select wire:model.live="statusFilter" :placeholder="__('All')" class="sm:max-w-xs">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            <flux:select.option value="available">{{ __('Available') }}</flux:select.option>
            <flux:select.option value="quarantine">{{ __('Quarantine') }}</flux:select.option>
            <flux:select.option value="adopted">{{ __('Adopted') }}</flux:select.option>
            <flux:select.option value="medical">{{ __('Medical') }}</flux:select.option>
        </flux:select>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Photo') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Identification') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Characteristics') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Accommodation') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($this->pets as $pet)
                        @php
                            $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();

                            $colors = null;
                            if ($pet->primaryColor && $pet->secondaryColor) {
                                $colors = $pet->primaryColor->name.' '.__('and').' '.$pet->secondaryColor->name;
                            } elseif ($pet->primaryColor) {
                                $colors = $pet->primaryColor->name;
                            } elseif ($pet->secondaryColor) {
                                $colors = $pet->secondaryColor->name;
                            }
                        @endphp
                        <tr wire:key="pet-{{ $pet->id }}">
                            <td class="px-6 py-3">
                                <flux:avatar
                                    size="xl"
                                    class="size-24"
                                    :src="$mainImage ? \Illuminate\Support\Facades\Storage::url($mainImage->image_path) : null"
                                    :name="$pet->name"
                                    :href="route('pets.show', $pet)"
                                    wire:navigate
                                />
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $pet->ref }}</span>
                                    <a href="{{ route('pets.show', $pet) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</a>
                                    <span class="text-neutral-500 dark:text-neutral-400">{{ __(ucfirst($pet->gender)) }}</span>
                                    <span class="text-neutral-500 dark:text-neutral-400">{{ $pet->chip }}</span>

                                    @if ($pet->date_of_death)
                                        <span>&nbsp;</span>
                                        <span class="text-neutral-500 dark:text-neutral-400">
                                            <strong>{{ __('Deceased') }}</strong> {{ __('at').' '.$pet->date_of_death->format('d/m/Y') }}
                                        </span>
                                    @elseif ($pet->status === 'adopted' && $pet->latestAdoption)
                                        <span>&nbsp;</span>
                                        <span class="text-neutral-500 dark:text-neutral-400">
                                            <strong>{{ __('Adopted') }}</strong> {{ __('at').' '.$pet->latestAdoption->adoption_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-col gap-1 text-neutral-500 dark:text-neutral-400">
                                    <span>{{ $pet->breed->name }}</span>
                                    <span>{{ $pet->furType?->name }}</span>
                                    <span>{{ $colors }}</span>
                                    <span>{{ $pet->age_in_words !== null ? __('Age').' '.$pet->age_in_words : '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-col gap-1 text-neutral-500 dark:text-neutral-400">
                                    <span>{{ $pet->cage?->wing->facility->name ?? __('No Facility Assigned') }}</span>
                                    <span>{{ $pet->cage?->wing->name ?? __('No Wing Assigned') }}</span>
                                    <span>{{ $pet->cage->code ?? __('No Cage Assigned') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <flux:button
                                        :href="route('pets.show', $pet)"
                                        size="sm"
                                        variant="subtle"
                                        icon="eye"
                                        :aria-label="__('View')"
                                        wire:navigate
                                    />

                                    <flux:modal.trigger name="confirm-pet-deletion-{{ $pet->id }}">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="trash"
                                            :aria-label="__('Delete')"
                                        />
                                    </flux:modal.trigger>

                                    <flux:modal name="confirm-pet-deletion-{{ $pet->id }}" class="max-w-lg">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                            </div>

                                            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                <flux:modal.close>
                                                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                </flux:modal.close>

                                                <flux:button variant="danger" wire:click="deletePet({{ $pet->id }})">
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
                            <td colspan="5" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                {{ __('No pets registered') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-3">
            <flux:pagination :paginator="$this->pets" />
        </div>
    </div>
</div>

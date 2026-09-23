<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">
                {{ $pet->name }}
                @if ($pet->date_of_death)
                    <span class="text-base font-normal text-neutral-500 dark:text-neutral-400">({{ __('Deceased') }} {{ __('at') }} {{ $pet->date_of_death->format('d/m/Y') }})</span>
                @elseif ($pet->status === 'adopted' && $pet->adoptions->isNotEmpty())
                    <span class="text-base font-normal text-neutral-500 dark:text-neutral-400">({{ __('Adopted') }} {{ __('at') }} {{ $pet->adoptions->first()->adoption_date->format('d/m/Y') }})</span>
                @endif
            </flux:heading>
            <flux:subheading>{{ __('Pets') }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            <flux:button :href="route('pets.index', ['speciesFilter' => $pet->species_id])" variant="filled" icon="arrow-left" wire:navigate>
                {{ $pet->species->name_plural }}
            </flux:button>

            <flux:button :href="route('pets.edit', $pet)" variant="primary" icon="pencil" wire:navigate>
                {{ __('Edit') }}
            </flux:button>

            @if ($pet->status !== 'adopted' || $pet->is_sponsorable)
                <flux:dropdown position="bottom" align="end">
                    <flux:button
                        icon="heart"
                        :aria-label="__('Adoption Registration')"
                    />

                    <flux:menu>
                        @unless ($pet->status === 'adopted')
                            <flux:menu.item :href="route('pets.adopt', $pet)" icon="heart" wire:navigate>
                                {{ __('Adoption Registration') }}
                            </flux:menu.item>
                        @endunless

                        @if ($pet->is_sponsorable)
                            <flux:menu.item :href="route('pets.sponsor', $pet)" icon="gift" wire:navigate>
                                {{ __('Sponsorship Registration') }}
                            </flux:menu.item>
                        @endif
                    </flux:menu>
                </flux:dropdown>
            @endif

            <flux:button
                :href="route('pets.print', $pet)"
                icon="printer"
                target="_blank"
                :aria-label="__('Print')"
            />
        </div>
    </div>

    @php
        $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
        $otherImages = $pet->images->reject(fn ($image) => $image->is($mainImage));
        $orderedImages = $pet->images->isNotEmpty() ? collect([$mainImage])->merge($otherImages) : collect();
        $galleryUrls = $orderedImages->map(fn ($image) => \Illuminate\Support\Facades\Storage::url($image->image_path))->values();
    @endphp

    <div class="flex flex-col gap-8">
        <div
            class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900"
            x-data="{ lightboxIndex: 0, images: @js($galleryUrls), open: false }"
            x-on:modal-show.window="$event.detail.name === 'pet-gallery' && (open = true)"
            x-on:modal-close.window="(!$event.detail.name || $event.detail.name === 'pet-gallery') && (open = false)"
            x-on:keydown.left.window="open && images.length > 1 && (lightboxIndex = (lightboxIndex - 1 + images.length) % images.length)"
            x-on:keydown.right.window="open && images.length > 1 && (lightboxIndex = (lightboxIndex + 1) % images.length)"
        >
            <flux:label>{{ __('Photos') }}</flux:label>

            @if ($orderedImages->isNotEmpty())
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                    @foreach ($orderedImages as $image)
                        <button
                            type="button"
                            wire:key="pet-image-{{ $image->id }}"
                            x-on:click="lightboxIndex = {{ $loop->index }}; $dispatch('modal-show', { name: 'pet-gallery' })"
                            class="cursor-zoom-in overflow-hidden rounded-lg {{ $loop->first ? 'ring-2 ring-neutral-900 dark:ring-white' : 'ring-1 ring-neutral-200 dark:ring-neutral-700' }}"
                        >
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}"
                                alt="{{ $pet->name }}"
                                class="h-24 w-full object-cover"
                            >
                        </button>
                    @endforeach
                </div>
            @else
                <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('No photos uploaded') }}</flux:text>
            @endif

            <flux:modal name="pet-gallery" variant="bare" class="h-dvh w-screen max-w-none p-0">
                <div class="relative flex h-full w-full items-center justify-center bg-black/70">
                    <div class="absolute top-4 end-4 z-20">
                        <flux:modal.close>
                            <flux:button variant="ghost" icon="x-mark" size="sm" :aria-label="__('Close')" class="text-white! hover:text-white/70!" />
                        </flux:modal.close>
                    </div>

                    @if ($galleryUrls->count() > 1)
                        <button
                            type="button"
                            x-on:click="lightboxIndex = (lightboxIndex - 1 + images.length) % images.length"
                            class="absolute top-1/2 start-4 z-10 -translate-y-1/2 text-white/80 hover:text-white"
                            aria-label="{{ __('Previous photo') }}"
                        >
                            <flux:icon name="chevron-left" class="size-10" />
                        </button>
                    @endif

                    <img :src="images[lightboxIndex]" alt="{{ $pet->name }}" class="max-h-full max-w-full object-contain">

                    @if ($galleryUrls->count() > 1)
                        <button
                            type="button"
                            x-on:click="lightboxIndex = (lightboxIndex + 1) % images.length"
                            class="absolute top-1/2 end-4 z-10 -translate-y-1/2 text-white/80 hover:text-white"
                            aria-label="{{ __('Next photo') }}"
                        >
                            <flux:icon name="chevron-right" class="size-10" />
                        </button>

                        <div class="absolute bottom-4 z-10 flex gap-1.5">
                            <template x-for="(url, i) in images" :key="i">
                                <button
                                    type="button"
                                    x-on:click="lightboxIndex = i"
                                    class="h-2 w-2 rounded-full"
                                    :class="i === lightboxIndex ? 'bg-white' : 'bg-white/40'"
                                ></button>
                            </template>
                        </div>
                    @endif
                </div>
            </flux:modal>
        </div>

        <div class="flex flex-col gap-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <flux:heading>{{ __('Identification') }}</flux:heading>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Name') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Microchip / Chip') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->chip ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Species') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->species->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Breed') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">
                            {{ $pet->breed->name }}
                            @if ($pet->species->has_pure_breed_field && $pet->is_pure_breed)
                                ({{ __('Pure') }})
                            @endif
                        </flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Gender') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ __(ucfirst($pet->gender)) }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Birth Date') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->birth_date?->format('d/m/Y') ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Death Date') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->date_of_death?->format('d/m/Y') ?? '—' }}</flux:text>
                    </div>
                </div>
            </div>

            <div>
                <flux:heading>{{ __('Characteristics') }}</flux:heading>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Primary Color') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->primaryColor?->name ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Secondary Color') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->secondaryColor?->name ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Fur Type') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->furType?->name ?? '—' }}</flux:text>
                    </div>
                    @if ($this->speciesHasSizes)
                        <div>
                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Size') }}</flux:text>
                            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->size?->name ?? '—' }}</flux:text>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <flux:heading>{{ __('Health') }}</flux:heading>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Is Neutered') }}</flux:text>
                        <flux:badge size="sm" :color="$pet->is_neutered ? 'lime' : 'zinc'">{{ $pet->is_neutered ? __('Yes') : __('No') }}</flux:badge>
                    </div>

                    @foreach ($this->sicknesses as $sickness)
                        @php
                            $petHasSickness = $pet->sicknesses->contains('id', $sickness->id);
                        @endphp

                        <div>
                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ $sickness->name }}</flux:text>
                            <flux:badge size="sm" :color="$petHasSickness ? 'lime' : 'zinc'">{{ $petHasSickness ? __('Yes') : __('No') }}</flux:badge>
                        </div>
                    @endforeach
                </div>

                @if ($pet->clinical_notes)
                    <div class="mt-4">
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Clinical Notes') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300 whitespace-pre-line">{{ $pet->clinical_notes }}</flux:text>
                    </div>
                @endif
            </div>

            <div>
                <flux:heading>{{ __('Adoption') }}</flux:heading>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Is Adoptable') }}</flux:text>
                        <flux:badge size="sm" :color="$pet->is_adoptable ? 'lime' : 'zinc'">{{ $pet->is_adoptable ? __('Yes') : __('No') }}</flux:badge>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Is Sponsorable') }}</flux:text>
                        <flux:badge size="sm" :color="$pet->is_sponsorable ? 'lime' : 'zinc'">{{ $pet->is_sponsorable ? __('Yes') : __('No') }}</flux:badge>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</flux:text>
                        <flux:badge size="sm">{{ __(match ($pet->status) {
                            'available' => 'Available',
                            'not_available' => 'Not Available',
                            'adopted' => 'Adopted',
                            'deceased' => 'Deceased',
                        }) }}</flux:badge>
                    </div>
                </div>
            </div>

            @if (config('app.public_portal_enabled'))
                <div>
                    <flux:heading>{{ __('Public Portal') }}</flux:heading>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Publish to Portal') }}</flux:text>
                            <flux:badge size="sm" :color="$pet->publish_to_portal ? 'lime' : 'zinc'">{{ $pet->publish_to_portal ? __('Yes') : __('No') }}</flux:badge>
                        </div>
                        <div>
                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Is Featured') }}</flux:text>
                            <flux:badge size="sm" :color="$pet->is_featured ? 'lime' : 'zinc'">{{ $pet->is_featured ? __('Yes') : __('No') }}</flux:badge>
                        </div>
                        <div>
                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('View Count') }}</flux:text>
                            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->view_count }}</flux:text>
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <flux:heading>{{ __('Accommodation') }}</flux:heading>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Cage') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->cage?->wing->facility->name ?? __('No Facility Assigned') }} &middot; {{ $pet->cage?->wing->name ?? __('No Wing Assigned') }} &middot; {{ $pet->cage->code ?? __('No Cage Assigned') }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Checkin Date') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->checkin_date?->format('d/m/Y') ?? '—' }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Checkout Date') }}</flux:text>
                        <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $pet->checkout_date?->format('d/m/Y') ?? '—' }}</flux:text>
                    </div>
                </div>
            </div>

            <div>
                <flux:heading>{{ __('Description') }}</flux:heading>
                <flux:text inline class="text-neutral-700 dark:text-neutral-300 mt-2 [&_ol]:list-decimal [&_ol]:ps-5 [&_ul]:list-disc [&_ul]:ps-5">
                    @if ($pet->description)
                        {!! $pet->description !!}
                    @else
                        —
                    @endif
                </flux:text>
            </div>

            @if ($pet->notes)
                <div>
                    <flux:heading>{{ __('Notes') }}</flux:heading>
                    <flux:text class="text-neutral-700 dark:text-neutral-300 mt-2 whitespace-pre-line">{{ $pet->notes }}</flux:text>
                </div>
            @endif
        </div>

        @foreach ($pet->adoptions as $adoption)
            @include('livewire.pets.partials.adoption-box', ['pet' => $pet, 'adoption' => $adoption])
        @endforeach

        @foreach ($pet->sponsorships as $sponsorship)
            @include('livewire.pets.partials.sponsorship-box', ['pet' => $pet, 'sponsorship' => $sponsorship])
        @endforeach

        @if ($pet->sponsorships->isNotEmpty())
            @include('livewire.pets.partials.sponsorship-payment-modal')
        @endif

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex w-full items-center justify-between">
                <flux:label>{{ __('Vaccinations') }}</flux:label>

                <flux:button :href="route('pets.vaccinate', $pet)" variant="filled" size="sm" icon="plus" wire:navigate>
                    {{ __('New Vaccination') }}
                </flux:button>
            </div>

            <div class="w-full overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                            <tr>
                                <th scope="col" class="px-4 py-2 font-medium">{{ __('Vaccine') }}</th>
                                <th scope="col" class="px-4 py-2 font-medium">{{ __('Administered Date') }}</th>
                                <th scope="col" class="px-4 py-2 font-medium">{{ __('Due Date') }}</th>
                                <th scope="col" class="px-4 py-2 font-medium">{{ __('Lot Number') }}</th>
                                <th scope="col" class="px-4 py-2 font-medium">{{ __('Veterinarian') }}</th>
                                <th scope="col" class="px-4 py-2 font-medium">{{ __('Notes') }}</th>
                                <th scope="col" class="px-4 py-2 font-medium">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($pet->vaccines as $vaccine)
                                @php
                                    $isVaccineScheduled = $vaccine->pivot->administered_date === null;
                                    $isVaccineOverdue = $isVaccineScheduled && $vaccine->pivot->due_date !== null && $vaccine->pivot->due_date->lt(now()->startOfDay());
                                    $isVaccineDueSoon = $isVaccineScheduled && $vaccine->pivot->due_date !== null && $vaccine->pivot->due_date->lte(now()->addWeek());
                                    $nextDueDateClass = match (true) {
                                        $isVaccineOverdue => 'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-200',
                                        $isVaccineDueSoon => 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200',
                                        default => '',
                                    };
                                @endphp
                                <tr wire:key="pet-vaccine-{{ $vaccine->pivot->id }}">
                                    <td class="px-4 py-2">{{ $vaccine->name }}</td>
                                    <td class="px-4 py-2">{{ $vaccine->pivot->administered_date?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-4 py-2 {{ $nextDueDateClass }}">{{ $vaccine->pivot->due_date?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $vaccine->pivot->lot_number ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $vaccine->pivot->veterinarian_name ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $vaccine->pivot->notes ?? '—' }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <flux:modal.trigger name="vaccination-show-{{ $vaccine->pivot->id }}">
                                                <flux:button size="sm" variant="subtle" icon="eye" :aria-label="__('Show')" />
                                            </flux:modal.trigger>

                                            <flux:button
                                                :href="route('pets.vaccinate.edit', [$pet, $vaccine->pivot])"
                                                size="sm"
                                                variant="subtle"
                                                icon="pencil"
                                                :aria-label="__('Edit')"
                                                wire:navigate
                                            />

                                            <flux:modal.trigger name="confirm-vaccination-deletion-{{ $vaccine->pivot->id }}">
                                                <flux:button size="sm" variant="subtle" icon="trash" :aria-label="__('Delete')" />
                                            </flux:modal.trigger>

                                            <flux:modal name="vaccination-show-{{ $vaccine->pivot->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <flux:heading size="lg">{{ $vaccine->name }}</flux:heading>

                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Administered Date') }}</flux:text>
                                                            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $vaccine->pivot->administered_date?->format('d/m/Y') ?? '—' }}</flux:text>
                                                        </div>
                                                        <div>
                                                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Due Date') }}</flux:text>
                                                            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $vaccine->pivot->due_date?->format('d/m/Y') ?? '—' }}</flux:text>
                                                        </div>
                                                        <div>
                                                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Lot Number') }}</flux:text>
                                                            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $vaccine->pivot->lot_number ?? '—' }}</flux:text>
                                                        </div>
                                                        <div>
                                                            <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Veterinarian') }}</flux:text>
                                                            <flux:text class="text-neutral-700 dark:text-neutral-300">{{ $vaccine->pivot->veterinarian_name ?? '—' }}</flux:text>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Notes') }}</flux:text>
                                                        <flux:text class="text-neutral-700 dark:text-neutral-300 whitespace-pre-line">{{ $vaccine->pivot->notes ?? '—' }}</flux:text>
                                                    </div>

                                                    <div class="flex justify-end">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Close') }}</flux:button>
                                                        </flux:modal.close>
                                                    </div>
                                                </div>
                                            </flux:modal>

                                            <flux:modal name="confirm-vaccination-deletion-{{ $vaccine->pivot->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <div>
                                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                    </div>

                                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                        </flux:modal.close>

                                                        <flux:button variant="danger" wire:click="deleteVaccination({{ $vaccine->pivot->id }})">
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
                                        {{ __('No vaccinations registered') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

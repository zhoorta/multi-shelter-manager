<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $pet->name }}</flux:heading>
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
                        class="bg-[#960532]! hover:bg-[#7a0429]! text-white! dark:bg-[#960532]! dark:hover:bg-[#7a0429]!"
                    />

                    <flux:menu>
                        @unless ($pet->status === 'adopted')
                            <flux:menu.item :href="route('pets.adopt', $pet)" icon="heart" wire:navigate>
                                {{ __('Adoption Registration') }}
                            </flux:menu.item>
                        @endunless

                        @if ($pet->is_sponsorable)
                            <flux:menu.item :href="route('pets.sponsor', $pet)" icon="currency-dollar" wire:navigate>
                                {{ __('Sponsorship Registration') }}
                            </flux:menu.item>
                        @endif
                    </flux:menu>
                </flux:dropdown>
            @endif
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

        <div class="grid grid-cols-[max-content_1fr] items-baseline gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Name') }}:</flux:text>
            <flux:text>{{ $pet->name }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Microchip / Chip') }}:</flux:text>
            <flux:text>{{ $pet->chip ?? '—' }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Species') }}:</flux:text>
            <flux:text>{{ $pet->species->name }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Breed') }}:</flux:text>
            <flux:text>{{ $pet->breed->name }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Gender') }}:</flux:text>
            <flux:text>{{ __(ucfirst($pet->gender)) }}</flux:text>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-baseline gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Primary Color') }}:</flux:text>
            <flux:text>{{ $pet->primaryColor?->name ?? '—' }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Secondary Color') }}:</flux:text>
            <flux:text>{{ $pet->secondaryColor?->name ?? '—' }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Fur Type') }}:</flux:text>
            <flux:text>{{ $pet->furType?->name ?? '—' }}</flux:text>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-baseline gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Birth Date') }}:</flux:text>
            <flux:text>{{ $pet->birth_date?->format('d/m/Y') ?? '—' }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Death Date') }}:</flux:text>
            <flux:text>{{ $pet->date_of_death?->format('d/m/Y') ?? '—' }}</flux:text>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-center justify-items-start gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Is Neutered') }}:</flux:text>
            <flux:badge size="sm" :color="$pet->is_neutered ? 'lime' : 'zinc'">{{ $pet->is_neutered ? __('Yes') : __('No') }}</flux:badge>

            @foreach ($this->sicknesses as $sickness)
                @php
                    $petHasSickness = $pet->sicknesses->contains('id', $sickness->id);
                @endphp

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ $sickness->name }}:</flux:text>
                <flux:badge size="sm" :color="$petHasSickness ? 'lime' : 'zinc'">{{ $petHasSickness ? __('Yes') : __('No') }}</flux:badge>
            @endforeach
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-center justify-items-start gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Is Adoptable') }}:</flux:text>
            <flux:badge size="sm" :color="$pet->is_adoptable ? 'lime' : 'zinc'">{{ $pet->is_adoptable ? __('Yes') : __('No') }}</flux:badge>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Is Sponsorable') }}:</flux:text>
            <flux:badge size="sm" :color="$pet->is_sponsorable ? 'lime' : 'zinc'">{{ $pet->is_sponsorable ? __('Yes') : __('No') }}</flux:badge>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-center justify-items-start gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Status') }}:</flux:text>
            <flux:badge size="sm">{{ __(ucfirst($pet->status)) }}</flux:badge>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Cage') }}:</flux:text>
            <flux:text>{{ $pet->cage?->wing->facility->name ?? __('No Facility Assigned') }} &middot; {{ $pet->cage?->wing->name ?? __('No Wing Assigned') }} &middot; {{ $pet->cage->code ?? __('No Cage Assigned') }}</flux:text>

            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Checkin Date') }}:</flux:text>
            <flux:text>{{ $pet->checkin_date?->format('d/m/Y') ?? '—' }}</flux:text>
        </div>

        <div class="grid grid-cols-[max-content_1fr] items-start justify-items-start gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Description') }}:</flux:text>
            <flux:text inline class="[&_ol]:list-decimal [&_ol]:ps-5 [&_ul]:list-disc [&_ul]:ps-5">
                @if ($pet->description)
                    {!! $pet->description !!}
                @else
                    —
                @endif
            </flux:text>
        </div>

        @foreach ($pet->sponsorships as $sponsorship)
            <div wire:key="sponsorship-{{ $sponsorship->id }}" class="grid grid-cols-[max-content_1fr] items-start justify-items-start gap-x-2 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="col-span-2 flex w-full items-center justify-between">
                    <flux:label>{{ __('Sponsorship') }}</flux:label>

                    <flux:button
                        :href="route('pets.sponsor.edit', [$pet, $sponsorship])"
                        variant="filled"
                        size="sm"
                        icon="pencil"
                        wire:navigate
                    >
                        {{ __('Edit') }}
                    </flux:button>
                </div>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Name') }}:</flux:text>
                <flux:text>{{ $sponsorship->name ?? '—' }}</flux:text>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Email') }}:</flux:text>
                <flux:text>{{ $sponsorship->email ?? '—' }}</flux:text>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Phone') }}:</flux:text>
                <flux:text>{{ $sponsorship->phone ?? '—' }}</flux:text>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Address') }}:</flux:text>
                <flux:text>{{ $sponsorship->address ?? '—' }}</flux:text>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Postal Code') }}:</flux:text>
                <flux:text>{{ $sponsorship->postal_code ?? '—' }}</flux:text>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('City') }}:</flux:text>
                <flux:text>{{ $sponsorship->city ?? '—' }}</flux:text>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Send Feedback') }}:</flux:text>
                <flux:badge size="sm" :color="$sponsorship->send_feedback ? 'lime' : 'zinc'">{{ $sponsorship->send_feedback ? __('Yes') : __('No') }}</flux:badge>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Send Newsletter') }}:</flux:text>
                <flux:badge size="sm" :color="$sponsorship->send_newsletter ? 'lime' : 'zinc'">{{ $sponsorship->send_newsletter ? __('Yes') : __('No') }}</flux:badge>

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">{{ __('Notes') }}:</flux:text>
                <flux:text>{{ $sponsorship->notes ?? '—' }}</flux:text>

                <div class="col-span-2 flex w-full items-center justify-between pt-2">
                    <flux:label>{{ __('Sponsorship Payments') }}</flux:label>

                    <flux:modal.trigger name="sponsorship-payment-form">
                        <flux:button
                            variant="filled"
                            size="sm"
                            icon="plus"
                            wire:click="createPayment({{ $sponsorship->id }})"
                        >
                            {{ __('Add Payment') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>

                <div class="col-span-2 w-full overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                                <tr>
                                    <th scope="col" class="px-4 py-2 font-medium">{{ __('Start Date') }}</th>
                                    <th scope="col" class="px-4 py-2 font-medium">{{ __('End Date') }}</th>
                                    <th scope="col" class="px-4 py-2 font-medium">{{ __('Payment Date') }}</th>
                                    <th scope="col" class="px-4 py-2 font-medium">{{ __('Payment Value') }}</th>
                                    <th scope="col" class="px-4 py-2 font-medium">{{ __('Notes') }}</th>
                                    <th scope="col" class="px-4 py-2 font-medium">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @forelse ($sponsorship->payments as $payment)
                                    <tr wire:key="sponsorship-payment-{{ $payment->id }}">
                                        <td class="px-4 py-2">{{ $payment->start_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">{{ $payment->end_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">{{ number_format((float) $payment->payment_value, 2, ',', '.') }}</td>
                                        <td class="px-4 py-2">{{ $payment->notes ?? '—' }}</td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-center gap-2">
                                                <flux:modal.trigger name="sponsorship-payment-form">
                                                    <flux:button
                                                        size="sm"
                                                        variant="subtle"
                                                        icon="pencil"
                                                        wire:click="editPayment({{ $payment->id }})"
                                                        :aria-label="__('Edit')"
                                                    />
                                                </flux:modal.trigger>

                                                <flux:modal.trigger name="confirm-payment-deletion-{{ $payment->id }}">
                                                    <flux:button
                                                        size="sm"
                                                        variant="subtle"
                                                        icon="trash"
                                                        :aria-label="__('Delete')"
                                                    />
                                                </flux:modal.trigger>

                                                <flux:modal name="confirm-payment-deletion-{{ $payment->id }}" class="max-w-lg">
                                                    <div class="space-y-6">
                                                        <div>
                                                            <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                            <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                        </div>

                                                        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                            <flux:modal.close>
                                                                <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                            </flux:modal.close>

                                                            <flux:button variant="danger" wire:click="deletePayment({{ $payment->id }})">
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
                                        <td colspan="6" class="px-4 py-4 text-center text-neutral-500 dark:text-neutral-400">
                                            {{ __('No sponsorship payments registered') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($pet->sponsorships->isNotEmpty())
        <flux:modal name="sponsorship-payment-form" class="max-w-lg">
            <form wire:submit="savePayment" class="flex flex-col gap-6">
                <flux:heading size="lg">
                    {{ $editingPaymentId ? __('Edit') : __('Create') }} &mdash; {{ __('Sponsorship Payments') }}
                </flux:heading>

                {{-- Safari renders an empty native date input showing today's date instead of a
                     blank placeholder, so the field starts as plain text and only switches to the
                     native date picker on focus (reverting to text on blur if still empty). --}}
                <flux:input
                    type="text"
                    wire:model="paymentStartDate"
                    :label="__('Start Date')"
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
                    wire:model="paymentEndDate"
                    :label="__('End Date')"
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
                    wire:model="paymentDate"
                    :label="__('Payment Date')"
                    :placeholder="__('Select a date')"
                    autocomplete="off"
                    clearable
                    x-data="{ dateFieldType: 'text' }"
                    x-bind:type="dateFieldType"
                    x-on:focus="dateFieldType = 'date'"
                    x-on:blur="if (! $el.value) dateFieldType = 'text'"
                />

                <flux:input wire:model="paymentValue" type="number" step="0.01" min="0" :label="__('Payment Value')" />

                <flux:textarea wire:model="paymentNotes" :label="__('Notes')" />

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="primary">
                        {{ $editingPaymentId ? __('Save') : __('Create') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
        @endif
    </div>
</div>

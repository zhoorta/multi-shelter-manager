<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Sponsorships') }}</flux:heading>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :placeholder="__('Search by sponsor, contact, pet or notes')"
            class="sm:max-w-xs"
        />
    </div>

    <div class="rounded-xl bg-white shadow-sm dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Sponsor name and contacts') }}</th>
                            <th scope="col" class="px-6 py-3 text-center font-medium">{{ __('Pet') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->sponsorships as $sponsorship)
                            @php
                                $mainImage = $sponsorship->pet->images->firstWhere('is_main', true) ?? $sponsorship->pet->images->first();
                                $validityDate = $sponsorship->payments->max('end_date');
                            @endphp
                            <tr wire:key="sponsorship-{{ $sponsorship->id }}">
                                <td class="px-6 py-3">
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('pets.sponsor.show', [$sponsorship->pet, $sponsorship]) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">
                                            {{ $sponsorship->name }}
                                        </a>
                                        @if ($sponsorship->email)
                                            <span class="text-neutral-500 dark:text-neutral-400">{{ $sponsorship->email }}</span>
                                        @endif
                                        @if ($sponsorship->phone)
                                            <span class="text-neutral-500 dark:text-neutral-400">{{ $sponsorship->phone }}</span>
                                        @endif
                                        <span class="text-neutral-500 dark:text-neutral-400">
                                            @if (! $validityDate)
                                                {{ __('(No payments done)') }}
                                            @elseif ($validityDate->greaterThanOrEqualTo(today()))
                                                {!! __('Valid until :date', ['date' => '<strong>'.$validityDate->format('d/m/Y').'</strong>']) !!}
                                            @else
                                                {!! __('Expired at :date', ['date' => '<strong>'.$validityDate->format('d/m/Y').'</strong>']) !!}
                                            @endif
                                        </span>
                                        @if ($sponsorship->notes)
                                            <div class="h-3"></div>
                                            <span class="text-neutral-500 dark:text-neutral-400">{{ $sponsorship->notes }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ $sponsorship->pet->species->name }} - {{ $sponsorship->pet->ref }}
                                        </span>

                                        @if ($mainImage)
                                            <flux:avatar
                                                size="lg"
                                                class="size-16"
                                                :src="\Illuminate\Support\Facades\Storage::url($mainImage->image_path)"
                                                :name="$sponsorship->pet->name"
                                            />
                                        @endif

                                        <a href="{{ route('pets.show', $sponsorship->pet) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">
                                            {{ $sponsorship->pet->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            :href="route('pets.sponsor.show', [$sponsorship->pet, $sponsorship])"
                                            size="sm"
                                            variant="subtle"
                                            icon="eye"
                                            :aria-label="__('View')"
                                            wire:navigate
                                        />

                                        <flux:modal.trigger name="confirm-sponsorship-deletion-{{ $sponsorship->id }}">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="trash"
                                                :aria-label="__('Delete')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal name="confirm-sponsorship-deletion-{{ $sponsorship->id }}" class="max-w-lg">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                    <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                </div>

                                                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                    <flux:modal.close>
                                                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                    </flux:modal.close>

                                                    <flux:button variant="danger" wire:click="deleteSponsorship({{ $sponsorship->id }})">
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
                                <td colspan="3" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No sponsorships registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="px-6 py-3">
        <flux:pagination :paginator="$this->sponsorships" class="!border-t-0 !pt-0" />
    </div>
</div>

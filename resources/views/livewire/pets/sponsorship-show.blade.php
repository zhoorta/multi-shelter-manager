<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $sponsorship->name }}</flux:heading>
            <flux:subheading>{{ __('Sponsorships') }}</flux:subheading>
        </div>

        <flux:button :href="route('pets.sponsorships.index')" variant="filled" icon="arrow-left" wire:navigate>
            {{ __('Sponsorships') }}
        </flux:button>
    </div>

    <div class="flex flex-col gap-8">
        @include('livewire.pets.partials.sponsorship-box', ['pet' => $pet, 'sponsorship' => $sponsorship])
    </div>

    @include('livewire.pets.partials.sponsorship-payment-modal')
</div>

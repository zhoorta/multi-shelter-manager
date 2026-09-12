<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $adoption->name }}</flux:heading>
            <flux:subheading>{{ __('Adoptions') }}</flux:subheading>
        </div>

        <flux:button :href="route('pets.adoptions.index')" variant="filled" icon="arrow-left" wire:navigate>
            {{ __('Adoptions') }}
        </flux:button>
    </div>

    <div class="flex flex-col gap-8">
        @include('livewire.pets.partials.adoption-box', ['pet' => $pet, 'adoption' => $adoption, 'backToAdoptionsList' => true])
    </div>
</div>

<div class="flex flex-col gap-4">
    <div class="flex flex-col gap-1">
        <flux:heading size="lg">{{ __('Species') }}</flux:heading>
        <flux:subheading>
            {{ __('Controls which species appear on the sidebar for this shelter\'s manager and staff users.') }}
        </flux:subheading>
    </div>

    @foreach ($this->species as $item)
        <flux:switch
            :checked="in_array($item->id, $shelterSpeciesIds, true)"
            wire:click="toggleSpecies({{ $item->id }})"
            :label="$item->name"
            align="left"
            wire:key="species-{{ $item->id }}"
        />
    @endforeach
</div>

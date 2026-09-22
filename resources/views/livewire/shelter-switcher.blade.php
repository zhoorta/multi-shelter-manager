<div>
    @if ($this->memberships->count() > 1)
        <flux:dropdown position="bottom" align="start">
            <flux:button variant="ghost" icon-trailing="chevron-down" class="hidden truncate sm:flex">
                {{ auth()->user()->currentShelter?->name }}
            </flux:button>

            <flux:menu>
                @foreach ($this->memberships as $shelter)
                    <flux:menu.item
                        wire:click="switchTo({{ $shelter->id }})"
                        :disabled="$shelter->id === auth()->user()->current_shelter_id"
                    >
                        {{ $shelter->name }}
                    </flux:menu.item>
                @endforeach
            </flux:menu>
        </flux:dropdown>
    @elseif (auth()->user()->currentShelter)
        <flux:heading class="hidden truncate sm:block">{{ auth()->user()->currentShelter->name }}</flux:heading>
    @endif
</div>

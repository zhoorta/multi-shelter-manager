<div class="flex flex-col gap-4">
    <div class="flex flex-col gap-1">
        <flux:heading size="lg">{{ __('Modules') }}</flux:heading>
        <flux:subheading>
            {{ __('Turn off the parts of the app this shelter does not use. Nothing is deleted: turning a module back on restores its data.') }}
        </flux:subheading>
    </div>

    @foreach ($this->moduleLabels() as $module => $label)
        <flux:switch wire:model="shelterModules.{{ $module }}" :label="$label" align="left" wire:key="module-{{ $module }}" />
    @endforeach
</div>

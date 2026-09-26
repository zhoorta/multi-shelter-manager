<div class="grid grid-cols-2 gap-4">
    <flux:input wire:model="shelterEmail" type="email" :label="__('Email')" />
    <flux:input wire:model="shelterPhone" :label="__('Phone')" />
</div>

<flux:input wire:model="shelterWebsite" :label="__('Website')" />

<flux:input wire:model="shelterAddress" :label="__('Address')" />

<div class="grid grid-cols-3 gap-4">
    <flux:input wire:model="shelterPostalCode" :label="__('Postal Code')" />
    <flux:input wire:model="shelterCity" :label="__('City')" field:class="col-span-2" />
</div>

<flux:select wire:model="shelterRegionId" :label="__('Region')">
    <flux:select.option value="">{{ __('No region assigned') }}</flux:select.option>
    @foreach ($this->regions as $region)
        <flux:select.option :value="$region->id">{{ $region->name }}</flux:select.option>
    @endforeach
</flux:select>

<flux:textarea wire:model="shelterDescription" :label="__('Description')" />

<flux:field>
    <flux:label>{{ __('Logo') }}</flux:label>

    @if ($shelterLogo)
        <img
            src="{{ $shelterLogo->temporaryUrl() }}"
            alt="{{ __('Logo') }}"
            class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
        >
    @elseif ($existingLogoPath)
        <img
            src="{{ \Illuminate\Support\Facades\Storage::url($existingLogoPath) }}"
            alt="{{ __('Logo') }}"
            class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
        >
    @endif

    <input type="file" wire:model="shelterLogo" accept="image/*">

    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">
        {{ __('PNG or JPG up to 2MB') }}
    </flux:text>

    <div wire:loading wire:target="shelterLogo">
        <flux:text size="sm">{{ __('Uploading') }}&hellip;</flux:text>
    </div>

    <flux:error name="shelterLogo" />
</flux:field>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
        </flux:radio.group>

        <flux:select wire:model.live="locale" :label="__('Language')" class="mt-6" data-test="locale-select">
            @foreach (config('app.available_locales') as $code => $language)
                <flux:select.option value="{{ $code }}" lang="{{ $code }}">{{ $language }}</flux:select.option>
            @endforeach
        </flux:select>
    </x-settings.layout>
</section>

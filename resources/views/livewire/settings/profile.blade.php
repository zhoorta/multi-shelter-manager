<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('View your name and email address')">
        <div class="my-6 w-full space-y-6">
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:text>{{ $name }}</flux:text>
            </flux:field>

            <div>
                <flux:field>
                    <flux:label>{{ __('Email') }}</flux:label>
                    <flux:text>{{ $email }}</flux:text>
                </flux:field>

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                    </div>
                @endif
            </div>
        </div>
    </x-settings.layout>
</section>

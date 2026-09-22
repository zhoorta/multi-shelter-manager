<div class="w-full rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
    <div class="flex flex-col gap-6">
        <div class="flex flex-col items-center gap-1 text-center">
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ __('Welcome! Let\'s set up the application') }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Create the administrator account to get started') }}</p>
        </div>

        <form wire:submit="createAdmin" class="flex flex-col gap-6">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
            />

            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="create-admin-button">
                {{ __('Create administrator') }}
            </flux:button>
        </form>
    </div>
</div>

<x-layouts::auth :title="__('Log in')">
    <div class="w-full rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col items-center gap-1 text-center">
                <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ __('Log in to your account') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Enter your email and password below to log in') }}</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                @csrf

                <!-- Email Address -->
                <div class="flex flex-col gap-2">
                    <label for="email" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Email address') }}</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                    @error('email')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between gap-2">
                        <label for="password" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Password') }}</label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" wire:navigate class="text-sm text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                    @error('password')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        @checked(old('remember'))
                        class="h-4 w-4 rounded border-zinc-300 text-accent focus:ring-accent dark:border-zinc-700 dark:bg-zinc-800"
                    />
                    <label for="remember" class="text-sm text-zinc-700 dark:text-zinc-300">{{ __('Remember me') }}</label>
                </div>

                <button
                    type="submit"
                    data-test="login-button"
                    class="w-full rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-accent-foreground shadow-sm transition hover:opacity-90"
                >
                    {{ __('Log in') }}
                </button>
            </form>
        </div>
    </div>
</x-layouts::auth>

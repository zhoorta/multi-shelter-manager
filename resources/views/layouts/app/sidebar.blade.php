<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    @if (! auth()->user()->is_admin)
                        <flux:sidebar.group icon="heart" :heading="__('Pets')" expandable>
                            @foreach (auth()->user()->currentShelter?->species()->orderBy('name')->get() ?? [] as $sidebarSpecies)
                                <flux:sidebar.item
                                    :href="route('pets.index', ['speciesFilter' => $sidebarSpecies->id])"
                                    :current="request()->routeIs('pets.index') && (string) request()->query('speciesFilter') === (string) $sidebarSpecies->id"
                                    wire:navigate
                                >
                                    {{ $sidebarSpecies->name_plural }}
                                </flux:sidebar.item>
                            @endforeach
                            <flux:sidebar.item
                                :href="route('pets.sponsorships.index')"
                                :current="request()->routeIs('pets.sponsorships.index')"
                                wire:navigate
                            >
                                {{ __('Sponsorships') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item
                                :href="route('pets.adoptions.index')"
                                :current="request()->routeIs('pets.adoptions.index')"
                                wire:navigate
                            >
                                {{ __('Adoptions') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item
                                :href="route('pets.vaccinations.index')"
                                :current="request()->routeIs('pets.vaccinations.index')"
                                wire:navigate
                            >
                                {{ __('Vaccinations') }}
                            </flux:sidebar.item>
                        </flux:sidebar.group>
                        <flux:sidebar.item icon="user-group" :href="route('volunteers.index')" :current="request()->routeIs('volunteers.*')" wire:navigate>
                            {{ __('Volunteers') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="building-office-2" :href="route('facilities.index')" :current="request()->routeIs('facilities.index')" wire:navigate>
                            {{ __('Facilities') }}
                        </flux:sidebar.item>
                    @endif
                    @if (auth()->user()->is_admin || auth()->user()->isManagerOfCurrentShelter())
                        <flux:sidebar.item icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.index')" wire:navigate>
                            {{ __('Users') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>

                @if (auth()->user()->is_admin)
                    <flux:sidebar.group :heading="__('Administration')" class="grid">
                        <flux:sidebar.item icon="building-office" :href="route('admin.shelters.index')" :current="request()->routeIs('admin.shelters.index')" wire:navigate>
                            {{ __('Shelters') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="map" :href="route('admin.regions.index')" :current="request()->routeIs('admin.regions.index')" wire:navigate>
                            {{ __('Regions') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="tag" :href="route('admin.species.index')" :current="request()->routeIs('admin.species.index')" wire:navigate>
                            {{ __('Species') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="rectangle-stack" :href="route('admin.breeds.index')" :current="request()->routeIs('admin.breeds.index')" wire:navigate>
                            {{ __('Breeds') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="arrows-pointing-out" :href="route('admin.sizes.index')" :current="request()->routeIs('admin.sizes.index')" wire:navigate>
                            {{ __('Sizes') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="swatch" :href="route('admin.fur-types.index')" :current="request()->routeIs('admin.fur-types.index')" wire:navigate>
                            {{ __('Fur Types') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="beaker" :href="route('admin.vaccines.index')" :current="request()->routeIs('admin.vaccines.index')" wire:navigate>
                            {{ __('Vaccines') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="bug-ant" :href="route('admin.sicknesses.index')" :current="request()->routeIs('admin.sicknesses.index')" wire:navigate>
                            {{ __('Sicknesses') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.activities.index')" :current="request()->routeIs('admin.activities.index')" wire:navigate>
                            {{ __('Activities') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="book-open-text" :href="route('documentation')" :current="request()->routeIs('documentation')" wire:navigate>
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Global Header -->
        <flux:header class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            @if (! auth()->user()->is_admin)
                <livewire:shelter-switcher />
            @endif

            <flux:spacer />

            <flux:text class="hidden sm:block">{{ auth()->user()->name }}</flux:text>

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>

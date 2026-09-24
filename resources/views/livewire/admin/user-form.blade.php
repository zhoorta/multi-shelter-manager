<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            {{ $user ? __('Edit User') : __('Invite User') }}
        </flux:heading>

        <flux:button :href="route('admin.users.index')" variant="filled" icon="arrow-left" wire:navigate>
            {{ __('Users') }}
        </flux:button>
    </div>

    <form wire:submit="saveUser" class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            @if (! $user && $existingUserId)
                <flux:callout icon="information-circle" :heading="__('This email already has an account')">
                    {{ __('This person will be added to the selected shelter(s) and notified — they keep their existing password.') }}
                </flux:callout>
            @endif

            @if ($user)
                <flux:input :value="$userEmail" :label="__('Email')" disabled />
            @else
                <flux:input wire:model.blur="userEmail" :label="__('Email')" type="email" />
            @endif

            @if ($this->isManagerEditingOwnAccount)
                <flux:input :value="$userName" :label="__('Name')" disabled />
            @elseif (! ($existingUserId && ! $user))
                <flux:input wire:model="userName" :label="__('Name')" />
            @endif

            @if (auth()->user()->is_admin)
                <flux:switch wire:model.live="userIsAdmin" :label="__('Global Admin')" align="left" />
            @endif
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900" x-show="! $wire.userIsAdmin">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ __('Shelters') }}</flux:heading>
                @unless ($this->isManagerEditingOwnAccount)
                    <flux:button size="sm" variant="ghost" icon="plus" type="button" wire:click="addMembership">
                        {{ __('Add shelter') }}
                    </flux:button>
                @endunless
            </div>

            @foreach ($userMemberships as $i => $membership)
                <div class="flex flex-col gap-3 rounded-lg border border-neutral-200 p-3 dark:border-neutral-700" wire:key="membership-{{ $i }}">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <flux:select wire:model="userMemberships.{{ $i }}.shelter_id" :label="__('Shelter')" :disabled="$this->isManagerEditingOwnAccount">
                                <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                                @foreach ((auth()->user()->is_admin ? $this->shelters : $this->managedShelters) as $shelter)
                                    <flux:select.option value="{{ $shelter->id }}">{{ $shelter->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div class="flex-1">
                            <flux:select wire:model="userMemberships.{{ $i }}.role" :label="__('Role')" :disabled="$this->isManagerEditingOwnAccount">
                                <flux:select.option value="staff">{{ __('Staff') }}</flux:select.option>
                                <flux:select.option value="manager">{{ __('Manager') }}</flux:select.option>
                                <flux:select.option value="viewer">{{ __('Viewer') }}</flux:select.option>
                            </flux:select>
                        </div>

                        @unless ($this->isManagerEditingOwnAccount)
                            <flux:button size="sm" variant="subtle" icon="trash" type="button" wire:click="removeMembership({{ $i }})" :aria-label="__('Remove')" />
                        @endunless
                    </div>

                    <flux:switch wire:model="userMemberships.{{ $i }}.vaccination_notifications" :label="__('Vaccination Notifications')" align="left" />
                </div>
            @endforeach

            @error('userMemberships')
                <flux:text class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
            @enderror
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="route('admin.users.index')" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ $user ? __('Save') : __('Invite User') }}
            </flux:button>
        </div>
    </form>
</div>

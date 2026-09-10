<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Users') }}</flux:heading>

        <flux:modal.trigger name="user-form">
            <flux:button variant="primary" icon="plus" wire:click="createUser">
                {{ __('Invite User') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Email') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Role') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Shelter') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->users as $item)
                            <tr wire:key="user-{{ $item->id }}">
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">{{ $item->name }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->email }}</td>
                                <td class="px-6 py-3">
                                    <flux:badge size="sm">{{ __(\Illuminate\Support\Str::title($item->role)) }}</flux:badge>
                                </td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->shelter?->name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="user-form">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="pencil"
                                                wire:click="editUser({{ $item->id }})"
                                                :aria-label="__('Edit')"
                                            />
                                        </flux:modal.trigger>

                                        @unless ($item->id === auth()->id())
                                            <flux:modal.trigger name="confirm-user-deletion-{{ $item->id }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="subtle"
                                                    icon="trash"
                                                    :aria-label="__('Delete')"
                                                />
                                            </flux:modal.trigger>

                                            <flux:modal name="confirm-user-deletion-{{ $item->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <div>
                                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                    </div>

                                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                        </flux:modal.close>

                                                        <flux:button variant="danger" wire:click="deleteUser({{ $item->id }})">
                                                            {{ __('Delete') }}
                                                        </flux:button>
                                                    </div>
                                                </div>
                                            </flux:modal>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No users registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <flux:modal name="user-form" class="max-w-lg">
        <form wire:submit="saveUser" class="flex flex-col gap-6">
            <flux:heading size="lg">
                {{ $editingUserId ? __('Edit User') : __('Invite User') }}
            </flux:heading>

            <flux:input wire:model="userName" :label="__('Name')" />

            @if ($editingUserId)
                <flux:input :value="$userEmail" :label="__('Email')" disabled />
            @else
                <flux:input wire:model="userEmail" :label="__('Email')" type="email" />
            @endif

            <flux:select wire:model="userRole" :label="__('Role')">
                @foreach (['staff', 'manager', 'admin'] as $role)
                    <flux:select.option value="{{ $role }}">{{ __(\Illuminate\Support\Str::title($role)) }}</flux:select.option>
                @endforeach
            </flux:select>

            <div x-show="$wire.userRole !== 'admin'">
                <flux:select wire:model="userShelterId" :label="__('Shelter')">
                    <flux:select.option value="">{{ __('Select an option') }}</flux:select.option>
                    @foreach ($this->shelters as $shelter)
                        <flux:select.option value="{{ $shelter->id }}">{{ $shelter->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editingUserId ? __('Save') : __('Invite User') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

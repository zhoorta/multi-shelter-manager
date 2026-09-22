<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Users') }}</flux:heading>

        <flux:button variant="primary" icon="plus" :href="route('admin.users.create')" wire:navigate>
            {{ __('Invite User') }}
        </flux:button>
    </div>

    @if (auth()->user()->is_admin)
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <flux:select wire:model.live="filterShelterId" :label="__('Shelter')" class="sm:max-w-xs">
                <flux:select.option value="">{{ __('All') }}</flux:select.option>
                @foreach ($this->shelters as $shelter)
                    <flux:select.option value="{{ $shelter->id }}">{{ $shelter->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Email') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Notifications') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Shelters') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Last Login') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->users as $item)
                            <tr wire:key="user-{{ $item->id }}">
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">{{ $item->name }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->email }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">
                                    {{ $item->shelters->filter(fn ($shelter) => $shelter->pivot->vaccination_notifications)->pluck('name')->implode(', ') ?: '—' }}
                                </td>
                                <td class="px-6 py-3">
                                    @if ($item->is_admin)
                                        <flux:badge size="sm">{{ __('Admin') }}</flux:badge>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($item->shelters as $shelter)
                                                <flux:badge size="sm">{{ $shelter->name }} ({{ __(\Illuminate\Support\Str::title($shelter->pivot->role)) }})</flux:badge>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->last_login?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="pencil"
                                            :href="route('admin.users.edit', $item)"
                                            wire:navigate
                                            :aria-label="__('Edit')"
                                        />

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
                                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
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
                                <td colspan="6" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No users registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="px-6 py-3">
        <flux:pagination :paginator="$this->users" class="!border-t-0 !pt-0" />
    </div>
</div>

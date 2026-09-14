<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Volunteers') }}</flux:heading>

        @if (auth()->user()->role === 'manager')
            <flux:button variant="primary" icon="plus" :href="route('volunteers.create')" wire:navigate>
                {{ __('Create') }}
            </flux:button>
        @endif
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Gender') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Email') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Phone') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('City') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->volunteers as $item)
                            <tr wire:key="volunteer-{{ $item->id }}">
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">
                                    <a href="{{ route('volunteers.show', $item) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $item->name }}</a>
                                </td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->gender === 'male' ? __('Male') : __('Female') }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->email }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->phone }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->city }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="eye"
                                            :href="route('volunteers.show', $item)"
                                            :aria-label="__('View')"
                                            wire:navigate
                                        />

                                        @if (auth()->user()->role === 'manager')
                                            <flux:modal.trigger name="confirm-volunteer-deletion-{{ $item->id }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="subtle"
                                                    icon="trash"
                                                    :aria-label="__('Delete')"
                                                />
                                            </flux:modal.trigger>

                                            <flux:modal name="confirm-volunteer-deletion-{{ $item->id }}" class="max-w-lg">
                                                <div class="space-y-6">
                                                    <div>
                                                        <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                        <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                    </div>

                                                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                        <flux:modal.close>
                                                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                        </flux:modal.close>

                                                        <flux:button variant="danger" wire:click="deleteVolunteer({{ $item->id }})">
                                                            {{ __('Delete') }}
                                                        </flux:button>
                                                    </div>
                                                </div>
                                            </flux:modal>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No volunteers registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

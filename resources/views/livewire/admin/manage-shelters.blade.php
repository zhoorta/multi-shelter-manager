<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Shelters') }}</flux:heading>

        <flux:button variant="primary" icon="plus" :href="route('admin.shelters.create')" wire:navigate>
            {{ __('Create') }}
        </flux:button>
    </div>

    <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Logo') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('City') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Region') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Users') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Pets') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->shelters as $item)
                            <tr wire:key="shelter-{{ $item->id }}">
                                <td class="px-6 py-3">
                                    @if ($item->logo_path)
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::url($item->logo_path) }}"
                                            alt="{{ $item->name }}"
                                            class="h-10 w-10 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                                        >
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-400 dark:bg-neutral-800">
                                            <flux:icon name="building-office" class="size-5" />
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">
                                    {{ $item->name }}
                                    @if ($item->short_name)
                                        <div class="text-xs font-normal text-neutral-500 dark:text-neutral-400">{{ $item->short_name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->city }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->region?->name }}</td>
                                <td class="px-6 py-3">
                                    <flux:badge size="sm">{{ $item->users_count }}</flux:badge>
                                </td>
                                <td class="px-6 py-3">
                                    <flux:badge size="sm">{{ $item->pets_count }}</flux:badge>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="pencil"
                                            :href="route('admin.shelters.edit', $item)"
                                            :aria-label="__('Edit')"
                                            wire:navigate
                                        />

                                        <flux:modal.trigger name="confirm-shelter-deletion-{{ $item->id }}">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="trash"
                                                :aria-label="__('Delete')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal name="confirm-shelter-deletion-{{ $item->id }}" class="max-w-lg">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                    <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                </div>

                                                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                    <flux:modal.close>
                                                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                    </flux:modal.close>

                                                    <flux:button variant="danger" wire:click="deleteShelter({{ $item->id }})">
                                                        {{ __('Delete') }}
                                                    </flux:button>
                                                </div>
                                            </div>
                                        </flux:modal>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No shelters registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

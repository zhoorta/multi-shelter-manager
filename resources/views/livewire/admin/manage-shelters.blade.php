<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Shelters') }}</flux:heading>

        <flux:modal.trigger name="shelter-form">
            <flux:button variant="primary" icon="plus" wire:click="createShelter">
                {{ __('Create') }}
            </flux:button>
        </flux:modal.trigger>
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
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">{{ $item->name }}</td>
                                <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">{{ $item->city }}</td>
                                <td class="px-6 py-3">
                                    <flux:badge size="sm">{{ $item->users_count }}</flux:badge>
                                </td>
                                <td class="px-6 py-3">
                                    <flux:badge size="sm">{{ $item->pets_count }}</flux:badge>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="shelter-form">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="pencil"
                                                wire:click="editShelter({{ $item->id }})"
                                                :aria-label="__('Edit')"
                                            />
                                        </flux:modal.trigger>

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
                                <td colspan="6" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No shelters registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <flux:modal name="shelter-form" class="max-w-lg">
        <form wire:submit="saveShelter" class="flex flex-col gap-6">
            <flux:heading size="lg">
                {{ $editingShelterId ? __('Edit') : __('Create') }} &mdash; {{ __('Shelters') }}
            </flux:heading>

            <flux:input wire:model="shelterName" :label="__('Name')" />

            <flux:input wire:model="shelterCity" :label="__('City')" />

            <flux:input wire:model="shelterAddress" :label="__('Address')" />

            <flux:input wire:model="shelterPostalCode" :label="__('Postal Code')" />

            <flux:input wire:model="shelterPhone" :label="__('Phone')" />

            <flux:input wire:model="shelterEmail" type="email" :label="__('Email')" />

            <flux:input wire:model="shelterWebsite" :label="__('Website')" />

            <flux:textarea wire:model="shelterDescription" :label="__('Description')" />

            <flux:field>
                <flux:label>{{ __('Logo') }}</flux:label>

                @if ($shelterLogo)
                    <img
                        src="{{ $shelterLogo->temporaryUrl() }}"
                        alt="{{ __('Logo') }}"
                        class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                    >
                @elseif ($existingLogoPath)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url($existingLogoPath) }}"
                        alt="{{ __('Logo') }}"
                        class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                    >
                @endif

                <input type="file" wire:model="shelterLogo" accept="image/*">

                <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">
                    {{ __('PNG or JPG up to 2MB') }}
                </flux:text>

                <div wire:loading wire:target="shelterLogo">
                    <flux:text size="sm">{{ __('Uploading') }}&hellip;</flux:text>
                </div>

                <flux:error name="shelterLogo" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editingShelterId ? __('Save') : __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

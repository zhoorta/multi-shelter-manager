<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Treatments') }}</flux:heading>

        <flux:modal.trigger name="treatment-form">
            <flux:button variant="primary" icon="plus" wire:click="createTreatment">
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
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Species') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Frequency') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($this->treatments as $treatment)
                            <tr wire:key="treatment-{{ $treatment->id }}">
                                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">{{ $treatment->name }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($treatment->species as $treatmentSpecies)
                                            <flux:badge size="sm">{{ $treatmentSpecies->name }}</flux:badge>
                                        @empty
                                            <span class="text-neutral-400">&mdash;</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    @if ($treatment->frequency_months !== null)
                                        {{ __('Every :months months', ['months' => $treatment->frequency_months]) }}
                                    @else
                                        <span class="text-neutral-400">&mdash;</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:modal.trigger name="treatment-form">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="pencil"
                                                wire:click="editTreatment({{ $treatment->id }})"
                                                :aria-label="__('Edit')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal.trigger name="confirm-treatment-deletion-{{ $treatment->id }}">
                                            <flux:button
                                                size="sm"
                                                variant="subtle"
                                                icon="trash"
                                                :aria-label="__('Delete')"
                                            />
                                        </flux:modal.trigger>

                                        <flux:modal name="confirm-treatment-deletion-{{ $treatment->id }}" class="max-w-lg">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">{{ __('Are you sure you want to delete this record?') }}</flux:heading>
                                                    <flux:subheading>{{ __('This record can be restored later by an administrator') }}</flux:subheading>
                                                </div>

                                                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                                    <flux:modal.close>
                                                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                                                    </flux:modal.close>

                                                    <flux:button variant="danger" wire:click="deleteTreatment({{ $treatment->id }})">
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
                                <td colspan="4" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                    {{ __('No treatments registered') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <flux:modal name="treatment-form" class="max-w-lg">
        <form wire:submit="saveTreatment" class="flex flex-col gap-6">
            <flux:heading size="lg">
                {{ $editingTreatmentId ? __('Edit') : __('Create') }} &mdash; {{ __('Treatments') }}
            </flux:heading>

            <flux:input wire:model="treatmentName" :label="__('Name')" />

            <flux:input
                type="number"
                min="1"
                max="120"
                wire:model="treatmentFrequencyMonths"
                :label="__('Frequency (months)')"
                :description:trailing="__('Leave empty when the next date depends on the veterinarian\'s protocol.')"
            />

            <flux:checkbox.group wire:model="treatmentSpeciesIds" :label="__('Species')">
                @foreach ($this->species as $item)
                    <flux:checkbox value="{{ $item->id }}" label="{{ $item->name }}" />
                @endforeach
            </flux:checkbox.group>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editingTreatmentId ? __('Save') : __('Create') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

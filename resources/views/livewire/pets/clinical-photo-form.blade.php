<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $clinicalPhoto !== null ? __('Edit Clinical Photo') : __('New Clinical Photo') }}</flux:heading>
            <flux:subheading>{{ $pet->name }}</flux:subheading>
        </div>

        <flux:button :href="route('pets.show', ['pet' => $pet, 'tab' => 'health'])" variant="filled" icon="arrow-left" wire:navigate>
            {{ $pet->name }}
        </flux:button>
    </div>

    <form wire:submit="savePhoto" autocomplete="off" class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model="label"
                    list="known-labels"
                    :label="__('What is being followed')"
                    :placeholder="__('e.g. Lump on the left mammary gland')"
                />

                <datalist id="known-labels">
                    @foreach ($this->knownLabels as $knownLabel)
                        <option value="{{ $knownLabel }}"></option>
                    @endforeach
                </datalist>

                <flux:input type="date" wire:model="takenAt" :label="__('Date taken')" :max="today()->toDateString()" />
            </div>

            <flux:textarea wire:model="photoNotes" :label="__('Notes')" rows="3" />
        </div>

        @if ($clinicalPhoto === null)
            <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <flux:label>{{ __('Photos') }}</flux:label>

                @if ($photos)
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                        @foreach ($photos as $photo)
                            <img
                                src="{{ $photo->temporaryUrl() }}"
                                alt="{{ __('Photo') }}"
                                class="h-24 w-full rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                            >
                        @endforeach
                    </div>
                @endif

                <flux:field>
                    <input
                        type="file"
                        wire:model="photos"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-neutral-700 file:mr-4 file:rounded-lg file:border-0 file:bg-neutral-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-neutral-700 dark:text-neutral-300 dark:file:bg-white dark:file:text-neutral-900 dark:hover:file:bg-neutral-200"
                    >

                    <flux:text size="sm" class="text-neutral-500 dark:text-neutral-400">
                        {{ __('JPG, PNG or WEBP, up to 10 MB each. Kept at full resolution and never shown publicly.') }}
                    </flux:text>

                    <div wire:loading wire:target="photos">
                        <flux:text size="sm">{{ __('Uploading') }}&hellip;</flux:text>
                    </div>

                    <flux:error name="photos" />
                    <flux:error name="photos.*" />
                </flux:field>
            </div>
        @endif

        <div class="flex justify-end gap-2">
            <flux:button :href="route('pets.show', ['pet' => $pet, 'tab' => 'health'])" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
</div>

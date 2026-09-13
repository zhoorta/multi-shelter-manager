<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl">{{ $sponsorship !== null ? __('Edit Sponsorship') : __('Sponsorship Registration') }}</flux:heading>
            <flux:subheading>{{ $pet->name }}</flux:subheading>
        </div>

        <flux:button :href="route('pets.show', $pet)" variant="filled" icon="arrow-left" wire:navigate>
            {{ $pet->name }}
        </flux:button>
    </div>

    <form wire:submit="saveSponsorship" autocomplete="off" class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:input wire:model="sponsorName" :label="__('Sponsor Name')" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="sponsorEmail" type="email" :label="__('Email')" />
                <flux:input wire:model="sponsorPhone" :label="__('Phone')" />
            </div>

            <flux:input wire:model="sponsorAddress" :label="__('Address')" />

            <div class="grid grid-cols-3 gap-4">
                <flux:input wire:model="sponsorPostalCode" :label="__('Postal Code')" />
                <flux:input wire:model="sponsorCity" :label="__('City')" field:class="col-span-2" />
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="grid grid-cols-2 gap-4">
                <flux:switch wire:model="sendFeedback" :label="__('Send Feedback')" align="left" />

                <flux:switch wire:model="sendNewsletter" :label="__('Send Newsletter')" align="left" />
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <flux:textarea wire:model="sponsorshipNotes" :label="__('Notes')" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button :href="route('pets.show', $pet)" variant="filled" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
</div>

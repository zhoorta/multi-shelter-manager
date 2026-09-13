<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            @if ($volunteer->image_path)
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::url($volunteer->image_path) }}"
                    alt="{{ $volunteer->name }}"
                    class="h-16 w-16 rounded-lg object-cover ring-1 ring-neutral-200 dark:ring-neutral-700"
                >
            @endif

            <div class="flex flex-col gap-1">
                <flux:heading size="xl">{{ $volunteer->name }}</flux:heading>
                <flux:subheading>{{ __('Volunteer') }}</flux:subheading>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if (auth()->user()->role === 'manager')
                <flux:button :href="route('volunteers.edit', $volunteer)" variant="filled" icon="pencil" wire:navigate>
                    {{ __('Edit') }}
                </flux:button>
            @endif

            <flux:button :href="route('volunteers.index')" variant="filled" icon="arrow-left" wire:navigate>
                {{ __('Volunteers') }}
            </flux:button>
        </div>
    </div>

    <div class="flex flex-col gap-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div>
            <flux:heading size="lg">{{ __('Identification') }}</flux:heading>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Name') }}</flux:text>
                    <flux:text>{{ $volunteer->name }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Gender') }}</flux:text>
                    <flux:text>{{ $volunteer->gender === 'male' ? __('Male') : __('Female') }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('ID Card') }}</flux:text>
                    <flux:text>{{ $volunteer->id_card ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('TIN') }}</flux:text>
                    <flux:text>{{ $volunteer->tin ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Birth Date') }}</flux:text>
                    <flux:text>{{ $volunteer->birth_date?->format('d/m/Y') ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Professional Activity') }}</flux:text>
                    <flux:text>{{ $volunteer->professional_activity ?? '—' }}</flux:text>
                </div>
            </div>
        </div>

        <div>
            <flux:heading size="lg">{{ __('Contacts') }}</flux:heading>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</flux:text>
                    <flux:text>{{ $volunteer->email ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Phone') }}</flux:text>
                    <flux:text>{{ $volunteer->phone ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Address') }}</flux:text>
                    <flux:text>{{ $volunteer->address ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Postal Code') }}</flux:text>
                    <flux:text>{{ $volunteer->postal_code ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('City') }}</flux:text>
                    <flux:text>{{ $volunteer->city ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Transport Mode') }}</flux:text>
                    <flux:text>{{ $volunteer->transport_mode ? __($volunteer->transport_mode) : '—' }}</flux:text>
                </div>
            </div>
        </div>

        <div>
            <flux:heading size="lg">{{ __('Volunteering') }}</flux:heading>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Start Date') }}</flux:text>
                    <flux:text>{{ $volunteer->start_date?->format('d/m/Y') ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('End Date') }}</flux:text>
                    <flux:text>{{ $volunteer->end_date?->format('d/m/Y') ?? '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Attendance Evaluation') }}</flux:text>
                    <flux:text>{{ $volunteer->attendance_evaluation ? __($volunteer->attendance_evaluation) : '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Performance Evaluation') }}</flux:text>
                    <flux:text>{{ $volunteer->performance_evaluation ? __($volunteer->performance_evaluation) : '—' }}</flux:text>
                </div>
                <div>
                    <flux:text class="text-neutral-500 dark:text-neutral-400">{{ __('Send Newsletter') }}</flux:text>
                    <flux:text>{{ $volunteer->send_newsletter ? __('Yes') : __('No') }}</flux:text>
                </div>
            </div>
        </div>

        @if ($volunteer->notes)
            <div>
                <flux:heading size="lg">{{ __('Notes') }}</flux:heading>
                <flux:text class="mt-2 whitespace-pre-line">{{ $volunteer->notes }}</flux:text>
            </div>
        @endif
    </div>
</div>

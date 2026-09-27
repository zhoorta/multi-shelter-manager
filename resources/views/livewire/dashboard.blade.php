<div class="flex h-full w-full flex-1 flex-col gap-6">
    @if (! auth()->user()->is_admin && (! $hasCages || ! $speciesConfigured || $this->speciesWithoutBreeds->isNotEmpty()))
        <div class="flex flex-col gap-3">
            @if (! $hasCages && ! auth()->user()->isManagerOfCurrentShelter())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
                    {{ __('No facilities defined. Contact the shelter manager to configure the Facilities, Wings and Cages.') }}
                </div>
            @elseif (! $hasCages)
                <a
                    href="{{ route('facilities.index') }}"
                    wire:navigate
                    class="block rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 hover:bg-amber-100 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200 dark:hover:bg-amber-950/60"
                >
                    {{ __('No facilities defined. Please configure the Facilities, Wings and Cages on the Facilities option.') }}
                </a>
            @endif

            @if (! $speciesConfigured)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
                    {{ __('No pet species defined for the shelter. Please contact the site administrator to configure the shelter species.') }}
                    @if ($administratorEmail)
                        <p class="mt-1">{{ __('Email') }}: <a href="mailto:{{ $administratorEmail }}" class="font-semibold underline underline-offset-2" data-test="administrator-email">{{ $administratorEmail }}</a></p>
                    @endif
                </div>
            @endif

            @foreach ($this->speciesWithoutBreeds as $speciesName)
                <div wire:key="species-without-breeds-{{ $speciesName }}" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
                    {{ __('No breeds of :species exist. Please contact the site administrator to configure the breeds.', ['species' => $speciesName]) }}
                    @if ($administratorEmail)
                        <p class="mt-1">{{ __('Email') }}: <a href="mailto:{{ $administratorEmail }}" class="font-semibold underline underline-offset-2" data-test="administrator-email">{{ $administratorEmail }}</a></p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Pets in Shelter') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $activePetsCount }}</span>
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Available Capacity') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $availableCapacity }}</span>
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Adoptions This Year') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $adoptionsThisYearCount }}</span>
        </div>

        @if ($showsActionCounters)
            @foreach ([
                ['label' => __('Pending Applications'), 'count' => $pendingApplicationsCount, 'href' => route('pets.applications.index')],
                ['label' => __('Overdue Vaccinations'), 'count' => $overdueVaccinationsCount, 'href' => route('pets.vaccinations.index', ['nextDueFilter' => 'overdue'])],
                ['label' => __('Fees overdue'), 'count' => $membersInArrearsCount, 'href' => route('members.index', ['inArrearsOnly' => 1])],
            ] as $counter)
                <a
                    wire:key="action-counter-{{ $loop->index }}"
                    href="{{ $counter['href'] }}"
                    wire:navigate
                    class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:hover:bg-neutral-800"
                >
                    <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $counter['label'] }}</span>
                    <span @class([
                        'text-3xl font-semibold',
                        'text-amber-600 dark:text-amber-400' => $counter['count'] > 0,
                        'text-neutral-900 dark:text-white' => $counter['count'] === 0,
                    ])>{{ $counter['count'] }}</span>
                </a>
            @endforeach
        @endif
    </div>

    @php
        $sectionClass = 'overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900';
        $sectionHeaderClass = 'flex items-center justify-between gap-4 border-b border-neutral-200 px-6 py-4 dark:border-neutral-700';
        $sectionTitleClass = 'text-base font-semibold text-neutral-900 dark:text-white';
        $viewAllClass = 'text-sm text-neutral-500 hover:underline dark:text-neutral-400';
        $gridClass = 'grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5';
        $emptyClass = 'text-center text-sm text-neutral-500 dark:text-neutral-400';
        $showsPersonalData = ! auth()->user()->isViewerOfCurrentShelter();
        $needsAttention = $unknownLocationPets->isNotEmpty()
            || $this->petsWithOpenHealthIssues->isNotEmpty()
            || $this->sponsorshipsToRenew->isNotEmpty()
            || $this->petsWithoutPhoto->isNotEmpty()
            || $this->longestWaitingPets->isNotEmpty();
    @endphp

    @if ($needsAttention)
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Needs attention') }}</h2>
    @endif

    @if ($unknownLocationPets->isNotEmpty())
        <div class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h3 class="{{ $sectionTitleClass }}">{{ __('Pets with Unknown Location') }}</h3>
                <a href="{{ route('pets.index', ['missingDataFilter' => 'no_location']) }}" wire:navigate class="{{ $viewAllClass }}">{{ __('View all') }}</a>
            </div>

            <div class="p-6">
                <div class="{{ $gridClass }}">
                    @foreach ($unknownLocationPets as $pet)
                        @php
                            $daysWithoutLocation = (int) ($pet->checkin_date ?? $pet->created_at)->startOfDay()->diffInDays(today());
                        @endphp
                        @include('livewire.partials.dashboard-pet-card', [
                            'key' => 'unknown-location-'.$pet->id,
                            'href' => route('pets.show', $pet),
                            'details' => [$daysWithoutLocation === 0 ? __('Today') : trans_choice(':count day ago|:count days ago', $daysWithoutLocation, ['count' => $daysWithoutLocation])],
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($this->petsWithOpenHealthIssues->isNotEmpty())
        <div class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h3 class="{{ $sectionTitleClass }}">{{ __('Open health issues') }}</h3>
                <a href="{{ route('pets.index', ['missingDataFilter' => 'open_health_issues']) }}" wire:navigate class="{{ $viewAllClass }}">{{ __('View all') }}</a>
            </div>

            <div class="p-6">
                <div class="{{ $gridClass }}">
                    @foreach ($this->petsWithOpenHealthIssues as $pet)
                        @include('livewire.partials.dashboard-pet-card', [
                            'key' => 'open-health-issue-'.$pet->id,
                            'href' => route('pets.show', $pet),
                            'details' => [$pet->openSicknesses->pluck('name')->implode(', ')],
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($this->sponsorshipsToRenew->isNotEmpty())
        <div class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h3 class="{{ $sectionTitleClass }}">{{ __('Sponsorships to Renew') }}</h3>
                <a href="{{ route('pets.sponsorships.index') }}" wire:navigate class="{{ $viewAllClass }}">{{ __('View all') }}</a>
            </div>

            <div class="p-6">
                <div class="{{ $gridClass }}">
                    @foreach ($this->sponsorshipsToRenew as $sponsorship)
                        @php
                            $validityDate = \Illuminate\Support\Carbon::parse($sponsorship->payments_max_end_date);
                        @endphp
                        @include('livewire.partials.dashboard-pet-card', [
                            'pet' => $sponsorship->pet,
                            'key' => 'sponsorship-to-renew-'.$sponsorship->id,
                            'href' => route('pets.sponsor.show', [$sponsorship->pet, $sponsorship]),
                            'details' => [
                                $sponsorship->name,
                                $validityDate->greaterThanOrEqualTo(today())
                                    ? __('Valid until :date', ['date' => $validityDate->format('d/m/Y')])
                                    : __('Expired at :date', ['date' => $validityDate->format('d/m/Y')]),
                            ],
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($this->petsWithoutPhoto->isNotEmpty())
        <div class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h3 class="{{ $sectionTitleClass }}">{{ __('Pets without a Photo') }}</h3>
                <a href="{{ route('pets.index', ['missingDataFilter' => 'no_photo']) }}" wire:navigate class="{{ $viewAllClass }}">{{ __('View all') }}</a>
            </div>

            <div class="p-6">
                <div class="{{ $gridClass }}">
                    @foreach ($this->petsWithoutPhoto as $pet)
                        @include('livewire.partials.dashboard-pet-card', [
                            'key' => 'without-photo-'.$pet->id,
                            'href' => route('pets.show', $pet),
                            'details' => [$pet->checkin_date?->format('d/m/Y')],
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($this->longestWaitingPets->isNotEmpty())
        <div class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h3 class="{{ $sectionTitleClass }}">{{ __('Longest in the Shelter') }}</h3>
            </div>

            <div class="p-6">
                <div class="{{ $gridClass }}">
                    @foreach ($this->longestWaitingPets as $pet)
                        @include('livewire.partials.dashboard-pet-card', [
                            'key' => 'longest-waiting-'.$pet->id,
                            'href' => route('pets.show', $pet),
                            'details' => [$pet->time_in_captivity],
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Recent activity') }}</h2>

    <div class="{{ $sectionClass }}">
        <div class="{{ $sectionHeaderClass }}">
            <h3 class="{{ $sectionTitleClass }}">{{ __('Recent Intakes') }}</h3>
        </div>

        <div class="p-6">
            @if ($recentIntakes->isEmpty())
                <p class="{{ $emptyClass }}">{{ __('No Recent Intakes') }}</p>
            @else
                <div class="{{ $gridClass }}">
                    @foreach ($recentIntakes as $pet)
                        @include('livewire.partials.dashboard-pet-card', [
                            'key' => 'recent-intake-'.$pet->id,
                            'href' => route('pets.show', $pet),
                            'details' => [
                                $pet->checkin_date->format('d/m/Y'),
                                $pet->cage ? $pet->cage->wing->name.' · '.$pet->cage->code : __('No location defined'),
                            ],
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="{{ $sectionClass }}">
        <div class="{{ $sectionHeaderClass }}">
            <h3 class="{{ $sectionTitleClass }}">{{ __('Recent Adoptions') }}</h3>
        </div>

        <div class="p-6">
            @if ($recentAdoptions->isEmpty())
                <p class="{{ $emptyClass }}">{{ __('No Recent Adoptions') }}</p>
            @else
                <div class="{{ $gridClass }}">
                    @foreach ($recentAdoptions as $adoption)
                        @include('livewire.partials.dashboard-pet-card', [
                            'pet' => $adoption->pet,
                            'key' => 'recent-adoption-'.$adoption->id,
                            'href' => route('pets.show', $adoption->pet),
                            'details' => [
                                $adoption->adoption_date->format('d/m/Y'),
                                $showsPersonalData ? \Illuminate\Support\Str::before(trim((string) $adoption->name), ' ') : null,
                            ],
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="{{ $sectionClass }}">
        <div class="{{ $sectionHeaderClass }}">
            <h3 class="{{ $sectionTitleClass }}">{{ __('Recent Passings') }}</h3>
        </div>

        <div class="p-6">
            @if ($recentPassings->isEmpty())
                <p class="{{ $emptyClass }}">{{ __('No Recent Passings') }}</p>
            @else
                <div class="{{ $gridClass }}">
                    @foreach ($recentPassings as $pet)
                        @include('livewire.partials.dashboard-pet-card', [
                            'key' => 'recent-passing-'.$pet->id,
                            'href' => route('pets.show', $pet),
                            'details' => [$pet->date_of_death->format('d/m/Y')],
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

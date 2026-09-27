<div class="flex flex-col gap-6">
    @php
        $cardClass = 'flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900';
        $sectionClass = 'overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900';
        $sectionHeaderClass = 'border-b border-neutral-200 px-6 py-4 dark:border-neutral-700';
        $sectionTitleClass = 'text-base font-semibold text-neutral-900 dark:text-white';
        $mutedClass = 'text-neutral-500 dark:text-neutral-400';
        $inactiveSince = now()->subDays(30);
        $setup = $this->sheltersToSetUp;
        $setupIssues = [
            ['label' => __('No species enabled'), 'shelters' => $setup['withoutSpecies']],
            ['label' => __('No cages defined'), 'shelters' => $setup['withoutCages']],
            ['label' => __('No users'), 'shelters' => $setup['withoutUsers']],
        ];
        $hasSetupIssues = collect($setupIssues)->contains(fn (array $issue) => $issue['shelters']->isNotEmpty())
            || $this->speciesWithoutBreeds->isNotEmpty();
    @endphp

    <div class="grid gap-4 sm:grid-cols-2">
        @foreach ([
            ['label' => __('Shelters'), 'count' => $this->totals['shelters']],
            ['label' => __('Active users (last 30 days)'), 'count' => $this->totals['activeUsers']],
            ['label' => __('Pets in Shelter'), 'count' => $this->totals['petsInCare']],
            ['label' => __('Adoptions This Year'), 'count' => $this->totals['adoptionsThisYear']],
        ] as $counter)
            <div wire:key="platform-counter-{{ $loop->index }}" class="{{ $cardClass }}">
                <span class="text-sm font-medium {{ $mutedClass }}">{{ $counter['label'] }}</span>
                <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $counter['count'] }}</span>
            </div>
        @endforeach
    </div>

    <div class="{{ $sectionClass }}">
        <div class="{{ $sectionHeaderClass }}">
            <h2 class="{{ $sectionTitleClass }}">{{ __('Shelters') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                        <th scope="col" class="px-6 py-3 text-center font-medium">{{ __('Pets in Shelter') }}</th>
                        <th scope="col" class="px-6 py-3 text-center font-medium">{{ __('Adoptions This Year') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Last login') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Last pet update') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($this->shelters as $shelter)
                        @php
                            $lastLogin = $shelter->users_max_last_login ? \Illuminate\Support\Carbon::parse($shelter->users_max_last_login) : null;
                            $lastPetUpdate = $shelter->pets_max_updated_at ? \Illuminate\Support\Carbon::parse($shelter->pets_max_updated_at) : null;
                        @endphp
                        <tr wire:key="platform-shelter-{{ $shelter->id }}">
                            <td class="px-6 py-3">
                                <div class="flex flex-col gap-1">
                                    <a href="{{ route('admin.shelters.edit', $shelter) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $shelter->name }}</a>
                                    @if ($shelter->region)
                                        <span class="{{ $mutedClass }}">{{ $shelter->region->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center">{{ $shelter->pets_in_care_count }}</td>
                            <td class="px-6 py-3 text-center">{{ $shelter->adoptions_this_year_count }}</td>
                            <td @class([
                                'px-6 py-3',
                                'text-amber-600 dark:text-amber-400' => ! $lastLogin || $lastLogin->lessThan($inactiveSince),
                            ])>{{ $lastLogin?->format('d/m/Y') ?? __('Never') }}</td>
                            <td class="px-6 py-3">{{ $lastPetUpdate?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center {{ $mutedClass }}">{{ __('No shelters registered') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($hasSetupIssues)
        <div class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h2 class="{{ $sectionTitleClass }}">{{ __('Setup to complete') }}</h2>
            </div>

            <ul class="divide-y divide-neutral-200 text-sm dark:divide-neutral-700">
                @foreach ($setupIssues as $issue)
                    @foreach ($issue['shelters'] as $shelter)
                        <li wire:key="setup-{{ $loop->parent->index }}-{{ $shelter->id }}" class="flex items-center justify-between gap-4 px-6 py-3">
                            <a href="{{ route('admin.shelters.edit', $shelter) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $shelter->name }}</a>
                            <span class="text-amber-600 dark:text-amber-400">{{ $issue['label'] }}</span>
                        </li>
                    @endforeach
                @endforeach

                @foreach ($this->speciesWithoutBreeds as $species)
                    <li wire:key="setup-species-{{ $species->id }}" class="flex items-center justify-between gap-4 px-6 py-3">
                        <a href="{{ route('admin.breeds.index') }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $species->name }}</a>
                        <span class="text-amber-600 dark:text-amber-400">{{ __('Species without breeds') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($this->pendingInvitations->isNotEmpty())
        <div class="{{ $sectionClass }}">
            <div class="{{ $sectionHeaderClass }}">
                <h2 class="{{ $sectionTitleClass }}">{{ __('Invitations not accepted') }}</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Shelter') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Invited on') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach ($this->pendingInvitations as $invitedUser)
                            <tr wire:key="pending-invitation-{{ $invitedUser->id }}">
                                <td class="px-6 py-3">
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('admin.users.edit', $invitedUser) }}" wire:navigate class="font-medium text-neutral-900 hover:underline dark:text-white">{{ $invitedUser->name }}</a>
                                        <span class="{{ $mutedClass }}">{{ $invitedUser->email }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3">{{ $invitedUser->shelters->pluck('name')->implode(', ') }}</td>
                                <td class="px-6 py-3">{{ $invitedUser->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

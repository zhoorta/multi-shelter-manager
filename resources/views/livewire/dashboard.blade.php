<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Total Pets') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $activePetsCount }}</span>
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Adoptions') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $adoptionsPetsCount }}</span>
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Available Capacity') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $availableCapacity }}</span>
        </div>

        <div class="flex flex-col gap-2 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Total Staff') }}</span>
            <span class="text-3xl font-semibold text-neutral-900 dark:text-white">{{ $staffCount }}</span>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">{{ __('Recent Intakes') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-xs uppercase text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Status') }}</th>
                        <th scope="col" class="px-6 py-3 font-medium">{{ __('Cage') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($recentIntakes as $pet)
                        <tr wire:key="recent-intake-{{ $pet->id }}">
                            <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">{{ $pet->name }}</td>
                            <td class="px-6 py-3">
                                <span @class([
                                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' => $pet->status === 'available',
                                    'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' => $pet->status === 'not_available',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' => $pet->status === 'adopted',
                                    'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' => $pet->status === 'deceased',
                                ])>
                                    {{ __(match ($pet->status) {
                                        'available' => 'Available',
                                        'not_available' => 'Not Available',
                                        'adopted' => 'Adopted',
                                        'deceased' => 'Deceased',
                                    }) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-neutral-500 dark:text-neutral-400">
                                {{ $pet->cage?->code ?? __('No Cage Assigned') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-6 text-center text-neutral-500 dark:text-neutral-400">
                                {{ __('No Recent Intakes') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mx-auto max-w-5xl p-10 print:p-0">
    <div class="mb-6 flex justify-end print:hidden">
        <button
            type="button"
            onclick="window.print()"
            class="inline-flex items-center gap-2 rounded-md bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700"
        >
            {{ __('Print') }}
        </button>
    </div>

    <div class="flex items-start justify-between gap-6 border-b border-neutral-300 pb-6">
        <div class="text-sm leading-relaxed text-neutral-800">
            <p class="text-lg font-semibold">{{ __('Vaccination Plan') }} — {{ $monthLabel }}</p>
            @if ($selectedVaccine)
                <p class="font-medium">{{ $selectedVaccine->name }}</p>
            @endif
            <p>{{ $shelter->name }}</p>
            <p>{{ trans_choice(':count animal|:count animals', $duePets->count(), ['count' => $duePets->count()]) }} · {{ __('Printed on :date', ['date' => today()->format('d/m/Y')]) }}</p>
        </div>

        @if ($shelter->logo_path)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($shelter->logo_path) }}"
                alt="{{ $shelter->name }}"
                class="h-16 w-16 shrink-0 object-contain"
            >
        @endif
    </div>

    <table class="mt-6 w-full text-left text-sm">
        <thead class="border-b border-neutral-300 text-xs uppercase text-neutral-500">
            <tr>
                <th scope="col" class="py-2 pr-4 font-medium">{{ __('Pet') }}</th>
                <th scope="col" class="py-2 pr-4 font-medium">{{ __('Microchip / Chip') }}</th>
                <th scope="col" class="py-2 pr-4 font-medium">{{ __('Accommodation') }}</th>
                <th scope="col" class="py-2 pr-4 font-medium">{{ __('Vaccines due') }}</th>
                <th scope="col" class="w-32 py-2 font-medium">{{ __('Given / Notes') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-200">
            @forelse ($duePets as $row)
                <tr class="print:break-inside-avoid">
                    <td class="py-2 pr-4 align-top">
                        <p class="font-medium">{{ $row['pet']->name }}</p>
                        <p class="text-xs text-neutral-500">{{ $row['pet']->species->name }} - {{ $row['pet']->ref }}</p>
                    </td>
                    <td class="py-2 pr-4 align-top">{{ $row['pet']->chip ?? '—' }}</td>
                    <td class="py-2 pr-4 align-top">{{ collect([$row['pet']->cage?->wing?->facility?->name, $row['pet']->cage?->wing?->name, $row['pet']->cage?->code])->filter()->implode(' · ') ?: '—' }}</td>
                    <td class="py-2 pr-4 align-top">
                        @foreach ($row['vaccinations'] as $vaccination)
                            <p>
                                <span class="mr-1 inline-block size-3 border border-neutral-500 align-middle"></span>
                                {{ $vaccination->vaccine->name }} — {{ $vaccination->due_date->format('d/m/Y') }}
                                @if ($vaccination->administered_date)
                                    <span class="text-xs text-neutral-500">({{ __('last dose :date', ['date' => $vaccination->administered_date->format('d/m/Y')]) }})</span>
                                @endif
                            </p>
                        @endforeach
                    </td>
                    <td class="border-b border-dotted border-neutral-400 py-2"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-neutral-500">{{ __('No animals have vaccinations due in this month.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

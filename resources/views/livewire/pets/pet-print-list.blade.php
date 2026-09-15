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

    <div class="flex items-center justify-center gap-3 border-b border-neutral-300 pb-6">
        <x-app-logo-icon class="size-10 fill-current text-black" />
        <span class="text-2xl font-semibold">{{ config('app.name', 'Laravel') }}</span>
    </div>

    <div class="mt-8 flex items-start justify-between gap-6 border-b border-neutral-300 pb-6">
        <div class="text-sm leading-relaxed text-neutral-800">
            <p class="font-semibold">{{ $shelter->name }}</p>
            <p>{{ $shelter->city }}</p>
            @if ($shelter->website)
                <p>{{ $shelter->website }}</p>
            @endif
            @if ($shelter->email)
                <p>{{ $shelter->email }}</p>
            @endif
        </div>

        @if ($shelter->logo_path)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($shelter->logo_path) }}"
                alt="{{ $shelter->name }}"
                class="h-20 w-20 shrink-0 object-contain"
            >
        @endif
    </div>

    <table class="mt-8 w-full text-left text-sm">
        <thead class="border-b border-neutral-300 text-xs uppercase text-neutral-500">
            <tr>
                <th scope="col" class="py-2 pr-4 font-medium">{{ __('Photo') }}</th>
                <th scope="col" class="py-2 pr-4 font-medium">{{ __('Identification') }}</th>
                <th scope="col" class="py-2 pr-4 font-medium">{{ __('Characteristics') }}</th>
                <th scope="col" class="py-2 font-medium">{{ __('Accommodation') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-200">
            @forelse ($pets as $pet)
                @php
                    $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();

                    $colors = null;
                    if ($pet->primaryColor && $pet->secondaryColor) {
                        $colors = $pet->primaryColor->name.' '.__('and').' '.$pet->secondaryColor->name;
                    } elseif ($pet->primaryColor) {
                        $colors = $pet->primaryColor->name;
                    } elseif ($pet->secondaryColor) {
                        $colors = $pet->secondaryColor->name;
                    }
                @endphp
                <tr class="print:break-inside-avoid">
                    <td class="py-3 pr-4">
                        @if ($mainImage)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($mainImage->image_path) }}"
                                alt="{{ $pet->name }}"
                                class="h-16 w-16 rounded-lg object-cover"
                            >
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs text-neutral-500">{{ $pet->ref }}</span>
                            <span class="font-medium text-neutral-900">{{ $pet->name }}</span>
                            <span class="text-neutral-500">{{ __(ucfirst($pet->gender)) }}</span>
                            <span class="text-neutral-500">{{ $pet->chip }}</span>

                            @if ($pet->date_of_death)
                                <span>&nbsp;</span>
                                <span class="text-neutral-500">
                                    <strong>{{ __('Deceased') }}</strong> {{ __('at').' '.$pet->date_of_death->format('d/m/Y') }}
                                </span>
                            @elseif ($pet->status === 'adopted' && $pet->latestAdoption)
                                <span>&nbsp;</span>
                                <span class="text-neutral-500">
                                    <strong>{{ __('Adopted') }}</strong> {{ __('at').' '.$pet->latestAdoption->adoption_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="flex flex-col gap-1 text-neutral-500">
                            <span>{{ $pet->breed->name }}</span>
                            @if ($pet->species->has_pure_breed_field && $pet->is_pure_breed)
                                <span>{{ __('Pure breed') }}</span>
                            @endif
                            <span>{{ $pet->size?->name }}</span>
                            <span>{{ $pet->furType?->name }}</span>
                            <span>{{ $colors }}</span>
                            <span>{{ $pet->age_in_words !== null ? __('Age').' '.$pet->age_in_words : '' }}</span>
                        </div>
                    </td>
                    <td class="py-3">
                        <div class="flex flex-col gap-1 text-neutral-500">
                            @if ($pet->cage)
                                <span>{{ $pet->cage->wing->facility->name }}</span>
                                <span>{{ $pet->cage->wing->name }}</span>
                                <span>{{ $pet->cage->code }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-neutral-500">
                        {{ __('No pets registered') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@php
    $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();

    $location = collect([
        $pet->cage?->wing->facility->name ?? __('No Facility Assigned'),
        $pet->cage?->wing->name ?? __('No Wing Assigned'),
        $pet->cage->code ?? __('No Cage Assigned'),
    ])->implode(' - ');
@endphp

<div class="mx-auto max-w-3xl p-10 print:p-0">
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

    <div class="mt-8 flex items-start justify-between gap-6">
        <div class="text-sm leading-relaxed text-neutral-800">
            <p class="font-semibold">{{ __('Reference') }} {{ $pet->ref }}</p>
            <p>{{ $location }}</p>
            <p>{{ $pet->shelter->city }}</p>
            <p class="mt-4">{{ $pet->shelter->name }}</p>
            @if ($pet->shelter->website)
                <p>{{ $pet->shelter->website }}</p>
            @endif
            @if ($pet->shelter->email)
                <p>{{ $pet->shelter->email }}</p>
            @endif
        </div>

        @if ($pet->shelter->logo_path)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($pet->shelter->logo_path) }}"
                alt="{{ $pet->shelter->name }}"
                class="h-20 w-20 shrink-0 object-contain"
            >
        @endif
    </div>

    <div class="mt-8 flex items-start justify-between gap-6 border-t border-neutral-300 pt-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-neutral-500">{{ __('Name') }}</p>
                <p class="font-medium text-neutral-900">{{ $pet->name }}</p>
            </div>
            @unless ($pet->date_of_death)
                <div>
                    <p class="text-neutral-500">{{ __('Age') }}</p>
                    <p class="font-medium text-neutral-900">{{ $pet->age_in_words ?? '—' }}</p>
                </div>
            @endunless
            <div>
                <p class="text-neutral-500">{{ __('Breed') }}</p>
                <p class="font-medium text-neutral-900">{{ $pet->breed->name }}</p>
            </div>
            <div>
                <p class="text-neutral-500">{{ __('Gender') }}</p>
                <p class="font-medium text-neutral-900">{{ __(ucfirst($pet->gender)) }}</p>
            </div>
            <div>
                <p class="text-neutral-500">{{ __('Is Adoptable') }}</p>
                <p class="font-medium text-neutral-900">{{ $pet->is_adoptable ? __('Yes') : __('No') }}</p>
            </div>
            <div>
                <p class="text-neutral-500">{{ __('Is Sponsorable') }}</p>
                <p class="font-medium text-neutral-900">{{ $pet->is_sponsorable ? __('Yes') : __('No') }}</p>
            </div>
            <div>
                <p class="text-neutral-500">
                    @if ($pet->date_of_death)
                        {{ __('Deceased at') }}
                    @elseif ($pet->status === 'adopted' && $pet->adoptions->isNotEmpty())
                        {{ __('Adopted at') }}
                    @else
                        {{ __('In captivity') }}
                    @endif
                </p>
                <p class="font-medium text-neutral-900">
                    @if ($pet->date_of_death)
                        {{ $pet->date_of_death->format('d/m/Y') }}
                    @elseif ($pet->status === 'adopted' && $pet->adoptions->isNotEmpty())
                        {{ $pet->adoptions->first()->adoption_date->format('d/m/Y') }}
                    @else
                        {{ $pet->time_in_captivity ?? '—' }}
                    @endif
                </p>
            </div>
        </div>

        @if ($mainImage)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($mainImage->image_path) }}"
                alt="{{ $pet->name }}"
                class="h-40 w-40 shrink-0 rounded-lg object-cover"
            >
        @endif
    </div>

    <div class="mt-8 border-t border-neutral-300 pt-6 text-sm leading-relaxed text-neutral-800 [&_ol]:list-decimal [&_ol]:ps-5 [&_ul]:list-disc [&_ul]:ps-5">
        @if ($pet->description)
            {!! $pet->description !!}
        @else
            —
        @endif
    </div>
</div>

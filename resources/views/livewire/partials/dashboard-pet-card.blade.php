{{-- Dashboard pet card: expects $pet, $href, $key (wire:key) and $details (lines shown under the name; empty ones are skipped). --}}
@php
    $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
@endphp
<a
    wire:key="{{ $key }}"
    href="{{ $href }}"
    wire:navigate
    class="flex flex-col items-center gap-2 rounded-lg p-3 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800"
>
    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $pet->species->name }} - {{ $pet->ref }}</span>

    <flux:avatar
        size="xl"
        class="size-20"
        :src="$mainImage ? \Illuminate\Support\Facades\Storage::url($mainImage->image_path) : null"
        :name="$pet->name"
    />

    <span class="text-sm font-medium text-neutral-900 hover:underline dark:text-white">{{ $pet->name }}</span>

    @foreach (array_filter($details) as $detail)
        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $detail }}</span>
    @endforeach
</a>

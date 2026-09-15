<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        @includeFirst([
            'livewire.documentation.instructions-'.app()->getLocale(),
            'livewire.documentation.instructions-en',
        ])
    </div>
</div>

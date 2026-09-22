<div class="relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute -top-24 -right-24 size-96 rounded-full bg-orange-200/50 blur-3xl dark:bg-orange-900/20"></div>

    <div class="relative mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:py-16">
        <article class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-amber-100 sm:p-10 dark:bg-stone-900 dark:ring-stone-800">
            @includeFirst([
                'livewire.privacy-policy.content-'.app()->getLocale(),
                'livewire.privacy-policy.content-en',
            ], ['contactEmail' => config('app.privacy_contact_email')])
        </article>
    </div>
</div>

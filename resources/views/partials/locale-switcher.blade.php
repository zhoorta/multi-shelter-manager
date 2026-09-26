<flux:dropdown position="bottom" align="end">
    <flux:button variant="ghost" size="sm" icon="language" icon:trailing="chevron-down" :aria-label="__('Language')" data-test="locale-switcher">
        {{ strtoupper(app()->getLocale()) }}
    </flux:button>

    <flux:menu>
        {{-- One form per language: flux:menu.item swallows the button's value attribute. --}}
        @foreach (config('app.available_locales') as $code => $language)
            <form method="POST" action="{{ route('locale.update') }}" class="w-full">
                @csrf
                <input type="hidden" name="locale" value="{{ $code }}">
                <flux:menu.item
                    as="button"
                    type="submit"
                    lang="{{ $code }}"
                    :icon="app()->getLocale() === $code ? 'check' : null"
                    class="w-full cursor-pointer"
                    data-test="locale-option-{{ $code }}"
                >
                    {{ $language }}
                </flux:menu.item>
            </form>
        @endforeach
    </flux:menu>
</flux:dropdown>

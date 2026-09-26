<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Appearance settings')]
class Appearance extends Component
{
    public string $locale = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->locale = app()->getLocale();
    }

    /**
     * Save the chosen interface language and reload the page so the whole
     * layout (sidebar included) is rendered in it.
     */
    public function updatedLocale(): void
    {
        $validated = $this->validate([
            'locale' => ['required', 'string', Rule::in(array_keys(config('app.available_locales')))],
        ]);

        Auth::user()->update(['locale' => $validated['locale']]);
        session()->put('locale', $validated['locale']);

        $this->redirectRoute('appearance.edit');
    }
}

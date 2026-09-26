---
paths:
  - 'app/Http/Middleware/SetLocale.php,app/Http/Controllers/LocaleController.php,app/Livewire/Settings/Appearance.php,config/app.php'
---

# Livewire Settings

## Per-user language: user locale > session > APP_LOCALE, no browser detection
App\Http\Middleware\SetLocale (appended to the web group) applies users.locale, then session('locale'), else config('app.locale'). Only codes in config('app.available_locales') (code => endonym) are accepted; adding a language means adding it there plus lang/{code}.json. Switching: POST route locale.update (partials/locale-switcher in the public and auth.simple layouts) or Settings > Appearance; both write the session and, when logged in, users.locale. User implements HasLocalePreference, so notifications go out in the recipient's language. Accept-Language is deliberately NOT used: it made the login page and backoffice disagree for users with English browsers, and the test client always sends en-us (it broke every test that calls app()->setLocale() before a guest request). The privacy policy lists "preferred language" under platform users in all 10 partials.

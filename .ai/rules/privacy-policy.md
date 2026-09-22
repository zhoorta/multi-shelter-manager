---
paths:
  - 'resources/views/livewire/privacy-policy/*.blade.php'
---

# Privacy Policy

## Privacy policy text must list every personal-data column the app stores
The public privacy policy (route privacy-policy, App\Livewire\PrivacyPolicy, per-locale partials content-pt/content-en, same includeFirst pattern as Documentation) enumerates the personal data held in users, sessions, adoptions, sponsorships/sponsorship_payments and volunteers, plus a cookie/local-storage table (session cookie name and lifetime read from config('session.*')). The policy states no third-party requests are made: the Fredoka display font is self-hosted via laravel-vite-plugin bunny() in vite.config.js (preload: false, used through the `font-display` Tailwind token) — never load fonts or scripts from a third-party CDN on public pages without adding a consent mechanism and updating both partials. When adding/removing personal-data columns, cookies or third-party requests, update BOTH partials and bump their "Last updated" date. Contact e-mail comes from config('app.privacy_contact_email') (env PRIVACY_CONTACT_EMAIL, falls back to MAIL_FROM_ADDRESS).

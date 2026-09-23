---
paths:
  - 'resources/views/livewire/privacy-policy/*.blade.php'
---

# Privacy Policy

## Privacy policy text must list every personal-data column the app stores
The public privacy policy (route privacy-policy, App\Livewire\PrivacyPolicy, per-locale partials content-pt/content-en, same includeFirst pattern as Documentation) enumerates the personal data held in users, sessions, adoptions, sponsorships/sponsorship_payments and volunteers, plus a cookie/local-storage table (session cookie name and lifetime read from config('session.*')). The policy states no third-party requests are made: the Fredoka display font is self-hosted via laravel-vite-plugin bunny() in vite.config.js (preload: false, used through the `font-display` Tailwind token) — never load fonts or scripts from a third-party CDN on public pages without adding a consent mechanism and updating both partials. When adding/removing personal-data columns, cookies or third-party requests, update BOTH partials and bump their "Last updated" date. Contact e-mail comes from config('app.privacy_contact_email') (env PRIVACY_CONTACT_EMAIL, falls back to MAIL_FROM_ADDRESS).

## Each privacy policy language cites its own country's law and authority
Partials exist for en, pt, es, fr, de, nl, pl, it, sv, da — the old "update BOTH partials" now means update ALL of them. Each cites a country's national GDPR law, cookie law and complaint authority: pt=Portugal (Lei 41/2004, CNPD), es=Spain (LOPDGDD, LSSI art. 22.2, AEPD), fr=France (loi Informatique et Libertés art. 82, CNIL), de=Germany (BDSG, TDDDG §25, state authority via BfDI), nl=Netherlands (UAVG, Telecommunicatiewet 11.7a, AP), pl=Poland (ustawa 10.05.2018, Prawo komunikacji elektronicznej art. 399, UODO), it=Italy (D.Lgs. 196/2003 art. 122, Garante), sv=Sweden (dataskyddslagen, LEK 9 kap. 28 §, IMY), da=Denmark (databeskyttelsesloven, cookiebekendtgørelsen, Datatilsynet). en is deliberately generic EU (ePrivacy Directive art. 5(3), Art. 77 complaint, EDPB members list) because it is also the fallback for untranslated locales — user's choice. PrivacyPolicyTest checks each locale's authority URL.

---
paths:
  - 'lang/**'
---

# Lang

## Ten locales: en, pt, es, fr, de, nl, pl, it, sv, da — keep all JSON files in sync
The app ships lang/en.json, lang/pt.json, lang/es.json, lang/fr.json, lang/de.json, lang/nl.json, lang/pl.json, lang/it.json, lang/sv.json and lang/da.json (plus lang/{pt,es,fr,de,nl,pl,it,sv,da}/passwords.php). Every new UI string key must be added to all ten JSON files. Long-form per-locale partials (documentation/instructions-{locale}, privacy-policy/content-{locale}) also exist for en, pt, es, fr, de, nl, pl, it, sv and da — update all ten together. "Region" is "Distrito" in pt but the neutral "Región"/"Région"/"Region"/"Regio"/"Region"/"Regione"/"Region"/"Region" in es/fr/de/nl/pl/it/sv/da. Polish plural keys (trans_choice) need THREE forms "one|few|many" (e.g. ":count rok|:count lata|:count lat") — Laravel's MessageSelector picks index 0/1/2 for pl; any new trans_choice key must follow this in pl.json. PrivacyPolicyTest uses 'fi' as the untranslated fallback locale — pick another if Finnish is ever added.

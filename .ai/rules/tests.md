---
paths:
  - 'tests/**'
---

# Tests

## Tests run in English regardless of .env APP_LOCALE
phpunit.xml pins `<env name="APP_LOCALE" value="en"/>` because many tests assert English UI strings (e.g. "Checkin Date", "1 year and 3 months"); without it, a developer's .env APP_LOCALE=pt breaks ~32 tests. Tests that need Portuguese must call app()->setLocale('pt') explicitly (see tests/Feature/PrivacyPolicyTest.php).

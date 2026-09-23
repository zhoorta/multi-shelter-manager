## What does this pull request do?

<!-- Describe the change and why it is needed. -->

Closes #<!-- issue number -->

## Type of change

- [ ] 🐞 Bug fix
- [ ] ✨ New feature
- [ ] ♻️ Refactoring (no change in behaviour)
- [ ] 🌍 Translation
- [ ] 📖 Documentation
- [ ] 🔧 Other (build, CI, dependencies…)

## How to test it

<!-- Steps a reviewer can follow, ideally starting from the demo data:
     php artisan migrate:fresh --seeder=DocumentationDemoSeeder -->

1.
2.
3.

## Screenshots

<!-- For any visual change, add before/after screenshots. Delete this section otherwise. -->

## Checklist

- [ ] I have read the [contributing guide](https://github.com/zhoorta/multi-shelter-manager/blob/main/CONTRIBUTING.md).
- [ ] `composer run test` passes locally (Pint, PHPStan and Pest).
- [ ] I added or updated **tests** that cover my change.
- [ ] **Shelter isolation:** my change never lets a shelter see or change another shelter's data, and permissions are checked on the server for each role (Admin / Manager / Staff).
- [ ] Every new text shown to users uses `__()` and is added to **all** `lang/*.json` files.
- [ ] Database changes follow `.ai/rules/migrations.md`, and new models use `Blameable` / `MultiShelterTrait` where needed.
- [ ] I updated the **documentation** (`docs/`) if the behaviour or screens changed.
- [ ] My pull request contains no real personal data, secrets or `.env` values.

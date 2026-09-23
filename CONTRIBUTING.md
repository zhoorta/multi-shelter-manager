# Contributing to Multi Shelter Manager

Thank you for helping! Multi Shelter Manager exists to make life easier for the people who care for abandoned animals, and every contribution helps: bug reports, ideas, translations, documentation or code.

Please read and follow our [Code of Conduct](CODE_OF_CONDUCT.md) in every interaction.

## Ways to contribute

### 🐞 Report a bug

Before opening an issue, search the [existing issues](https://github.com/zhoorta/multi-shelter-manager/issues) to check it hasn't been reported yet. When you open one, include:

- what you did, step by step;
- what you expected to happen;
- what happened instead (copy the error message, or check `storage/logs/laravel.log`);
- screenshots, if they help;
- your environment: PHP version, database (MySQL, MariaDB, SQLite…), browser, and the app language (`APP_LOCALE`).

> Never paste real personal data (adopters, volunteers, sponsors) or your `.env` file into an issue.

### 💡 Suggest a feature

Open an issue describing the problem you want to solve for your shelter, not only the solution you have in mind. Knowing how shelters really work helps us design features that fit everyone.

### 🌍 Improve a translation

The interface is available in 10 languages. Translations live in the `lang/` folder:

- `lang/<code>.json` — the interface texts, e.g. `lang/fr.json`;
- `lang/<code>/` — validation, authentication and password messages.

The in-app documentation and privacy policy have one view per language in `resources/views/livewire/documentation/` and `resources/views/livewire/privacy-policy/`.

To add a **new** language, copy the English files, translate them, and open a pull request.

### 📖 Improve the documentation

The user guide is in the [`docs`](docs/README.md) folder. Fixes, clarifications and new screenshots are all welcome.

### 🔒 Report a security issue

**Do not open a public issue for security problems.** Report them privately as explained in the [security policy](SECURITY.md): use the repository's **Security → Report a vulnerability** button, or e-mail **zhoorta@hotmail.com**.

## Development setup

Requirements: PHP 8.3+, Composer, Node.js 20+, and MySQL/MariaDB or SQLite.

1. **Fork** the repository on GitHub and clone your fork:

   ```bash
   git clone https://github.com/<your-username>/multi-shelter-manager.git
   cd multi-shelter-manager
   ```

2. **Install** everything (dependencies, `.env`, application key, migrations, assets):

   ```bash
   composer run setup
   php artisan storage:link
   ```

3. **Load the demo data** (optional, but it makes testing much easier):

   ```bash
   php artisan migrate:fresh --seeder=DocumentationDemoSeeder
   ```

   Log in as `admin@example.com`, `manager@example.com` or `staff@example.com`, password `password`.

4. **Start** the development server, with hot reload of the front-end:

   ```bash
   composer run dev
   ```

   The app runs on http://localhost:8000.

See [Installation](docs/01-installation.md) for all configuration options.

## Making a change

1. Create a branch from `main` with a descriptive name:

   ```bash
   git checkout -b fix/vaccination-due-date-filter
   git checkout -b feature/pet-weight-history
   ```

2. Make your change and **add or update tests** that cover it.
3. Run the quality checks (see below) and make sure they all pass.
4. Commit with a clear message that says *what* changed and *why*.
5. Push your branch and open a **pull request** against `main`. In the description:
   - explain the problem and how you solved it;
   - link the related issue (e.g. `Closes #42`);
   - add screenshots for any visual change.

Keep pull requests small and focused on one thing: they are reviewed and merged much faster.

## Quality checks

Every pull request runs the checks below automatically (GitHub Actions). Run them locally first:

```bash
composer run test
```

This runs:

| Check | Tool | Run it alone |
|-------|------|--------------|
| Code style | [Laravel Pint](https://laravel.com/docs/pint) | `vendor/bin/pint --test` (fix with `composer run lint`) |
| Static analysis (level 7) | [PHPStan](https://phpstan.org) / Larastan | `composer run types:check` |
| Tests | [Pest](https://pestphp.com) | `php artisan test` |

While working, run only the tests you need:

```bash
php artisan test --compact tests/Feature/ManagePetsTest.php
php artisan test --compact --filter="creates a pet"
```

Tests use an in-memory SQLite database and always run in English, whatever your `.env` says, so they never touch your local data.

## Coding guidelines

### The golden rule: shelters never see each other's data

This is a multi-shelter application. **Data must be isolated by `shelter_id`.**

- Models with a direct `shelter_id` column (e.g. `Pet`, `Facility`, `Volunteer`) use the `App\Traits\MultiShelterTrait`. It filters every query to the logged-in user's current shelter and sets `shelter_id` automatically on create.
- Models that belong to a shelter indirectly (`Wing`, `Cage`, …) must always be queried through their parent.
- Global lookup tables (species, breeds, vaccines, …) are shared by all shelters and have no `shelter_id`.
- When you add a feature, add a test proving that a user of shelter A **cannot** see or change shelter B's data.

### Roles

Always check permissions on the server, never only by hiding a button:

- **Admin** — the platform: shelters, lookup tables, users. Does not manage pets or facilities.
- **Manager** — one or more shelters, including their users.
- **Staff** — the daily work of a shelter.

### Language

- Code, database tables, columns and variables are written in **English**.
- Every text shown to users goes through Laravel's translation helper, e.g. `__('Save')`. Add the new key to **all** the `lang/*.json` files. If you can't translate it, copy the English text; a native speaker can improve it later.

### Stack and structure

- Laravel 13, Livewire 4 and Flux UI, styled with Tailwind CSS.
- Livewire components are split into a class in `app/Livewire/` and a Blade view in `resources/views/livewire/`.
- Create files with `php artisan make:...` (models, migrations, Livewire components, tests) and follow the structure of the files next to yours.
- Models that need an audit trail use `App\Traits\Blameable`, which fills in `created_by`, `updated_by` and `deleted_by` automatically.
- Don't add new dependencies without discussing them in an issue first.

### PHP style

- Always declare parameter and return types.
- Use constructor property promotion.
- Always use curly braces, even for one-line `if` statements.
- Prefer descriptive names (`isAdoptable`, not `check()`) and PHPDoc blocks over inline comments.

Pint fixes most formatting for you: run `composer run lint` before committing.

### Project rules

The `.ai/rules/` folder records decisions and pitfalls specific to this project, grouped by area (pets, facilities, migrations, …). Read the rules for the files you are changing: they explain *why* things are done the way they are.

## Questions?

Open an issue with the **question** label. We're happy to help. 🐾

# Security Policy

Multi Shelter Manager stores personal data about adopters, sponsors, volunteers and shelter staff, so we take security seriously. Thank you for helping keep the project and its users safe.

## Supported versions

The project has no numbered releases yet. Security fixes are made on the latest code of the `main` branch. Please make sure you can reproduce the problem there before reporting it.

| Version | Supported |
|---------|-----------|
| `main` (latest) | ✅ |
| Older commits | ❌ |

## Reporting a vulnerability

**Please do not report security problems in public GitHub issues, discussions or pull requests.**

Report them privately instead, using either:

- **GitHub** — the **Security** tab of this repository → **Report a vulnerability**; or
- **E-mail** — **zhoorta@hotmail.com**, with *"Security"* in the subject.

Please include as much of the following as you can:

- the kind of problem (e.g. data from one shelter visible to another, access beyond a user's role, SQL injection, XSS, authentication bypass…);
- the affected page, route or file;
- step-by-step instructions to reproduce it, ideally starting from the demo data (`php artisan migrate:fresh --seeder=DocumentationDemoSeeder`);
- the impact: what an attacker could see or do;
- a suggested fix, if you have one.

Never include real personal data in your report.

## What happens next

1. You get an acknowledgement within **7 days**.
2. We confirm the problem and assess how serious it is, and keep you informed of progress.
3. We prepare a fix and publish it on `main`.
4. Once the fix is available, the vulnerability is disclosed publicly. You are credited, unless you prefer to stay anonymous.

Please give us reasonable time to fix the problem before disclosing it anywhere else.

## Especially important for this project

Reports in these areas are particularly valuable:

- **Shelter data isolation** — a user of one shelter seeing or changing another shelter's pets, volunteers, facilities, adoptions or users.
- **Role checks** — Staff doing Manager-only actions, or Managers doing Admin-only actions.
- **The public portal** — personal data (adopters, sponsors, volunteers, internal or clinical notes) leaking to visitors who are not logged in.
- **Invitations and password resets** — links that can be reused, guessed or used for someone else's account.
- **File uploads** — photos and logos that could be used to upload or run malicious files.

## Keeping your installation secure

If you run Multi Shelter Manager yourself:

- set `APP_ENV=production` and **`APP_DEBUG=false`**;
- serve the application over **HTTPS** only;
- keep `.env` private and never commit it;
- use a dedicated database user with a strong password;
- keep PHP, the database server and the project's dependencies up to date (`composer update`, `npm update`);
- back up the database and the `storage/app/public` folder regularly.

See the [environment variables reference](docs/01-installation.md#110-environment-variables-reference) for all settings.

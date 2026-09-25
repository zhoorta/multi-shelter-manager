# 1. Installation

[← Documentation index](README.md) · [Next: First run, login and roles →](02-first-run-and-roles.md)

This chapter explains how to install Multi Shelter Manager on a computer or server, from nothing to a running application.

## 1.1 Requirements

| Software | Version |
|----------|---------|
| PHP | 8.3 or newer, with the `gd`, `pdo_mysql` (or `pdo_sqlite`), `mbstring`, `intl` and `fileinfo` extensions |
| Composer | 2.x |
| Node.js and npm | Node 20 or newer |
| Database | MySQL 8 / MariaDB 10.6+, or SQLite |
| A mail server (SMTP) | Needed to send invitations and vaccination reminders |

The application is built with Laravel 13, Livewire 4, Flux UI and Tailwind CSS.

## 1.2 Get the code

```bash
git clone https://github.com/zhoorta/multi-shelter-manager.git
cd multi-shelter-manager
```

## 1.3 Install and build

A single Composer script installs the PHP and JavaScript dependencies, creates the `.env` file, generates the application key, runs the database migrations and builds the front-end assets:

```bash
composer run setup
```

If you prefer to run the steps one by one:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

## 1.4 Configure the `.env` file

Open `.env` in a text editor and review these settings. The most important ones are explained below. For the complete list, see the [environment variables reference](#110-environment-variables-reference) at the end of this chapter.

### Application name, URL and language

```dotenv
APP_NAME="Multi Shelter Manager"
APP_URL=http://localhost:8000
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

`APP_NAME` is shown in the sidebar, on the login page, on printed sheets and in e-mails.

`APP_LOCALE` sets the interface language. Supported languages:

| Code | Language | Code | Language |
|------|----------|------|----------|
| `en` | English | `it` | Italian |
| `pt` | Portuguese | `nl` | Dutch |
| `es` | Spanish | `pl` | Polish |
| `fr` | French | `sv` | Swedish |
| `de` | German | `da` | Danish |

### Database

MySQL example:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multishelter_manager
DB_USERNAME=root
DB_PASSWORD=
```

Create the empty database first (for example `CREATE DATABASE multishelter_manager;`). For SQLite, keep `DB_CONNECTION=sqlite` and the file `database/database.sqlite` is used.

### E-mail

Invitations and vaccination reminders are sent by e-mail, so configure a real mail server in production:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS="no-reply@your-domain.org"
MAIL_FROM_NAME="${APP_NAME}"
```

While testing, `MAIL_MAILER=log` writes the e-mails to `storage/logs/laravel.log` instead of sending them.

### Public adoption portal

```dotenv
PUBLIC_PORTAL_ENABLED=true
```

- `true` — the home page is a public website where visitors can browse the animals available for adoption in every shelter (see [chapter 13](13-public-portal.md)).
- `false` — the application is a back-office only; visiting the home page goes straight to the login page.

## 1.5 Create the database tables

```bash
php artisan migrate
```

## 1.6 Make uploaded photos visible

Pet photos, volunteer photos and shelter logos are stored in `storage/app/public`. Link that folder to the public web folder once:

```bash
php artisan storage:link
```

## 1.7 Start the application

For local use or development:

```bash
composer run dev
```

Then open **http://localhost:8000** in your browser.

In production, point your web server (Nginx, Apache, Laravel Cloud, Forge, …) to the `public/` folder.

## 1.8 Schedule the daily tasks

Vaccination reminder e-mails are sent by a scheduled task that runs every day. On the server, add this line to the crontab (`crontab -e`) so Laravel's scheduler runs every minute:

```cron
* * * * * cd /path/to/multi-shelter-manager && php artisan schedule:run >> /dev/null 2>&1
```

You can also send the reminders manually at any time:

```bash
php artisan app:send-vaccination-due-notifications
```

## 1.9 (Optional) Load the demo data

To explore the application with realistic data — the same data used for the screenshots in this guide — load the demo seeder into an **empty** database:

```bash
php artisan migrate:fresh --seeder=DocumentationDemoSeeder
```

> ⚠️ `migrate:fresh` deletes every table first. Never run it on a database with real data.

It creates 4 shelters, dogs and cats with real photos, facilities with wings and cages, vaccinations, sicknesses, adoptions, sponsorships and volunteers, plus these accounts (password `password` for all):

| Role | E-mail |
|------|--------|
| Admin | `admin@example.com` |
| Manager of *Happy Paws Animal Shelter* | `manager@example.com` |
| Staff of *Happy Paws Animal Shelter* | `staff@example.com` |
| Viewer (read-only) of *Happy Paws Animal Shelter* | `viewer@example.com` |

If you load the demo data you can skip the setup wizard described in the next chapter and log in directly.

## 1.10 Environment variables reference

All settings live in the `.env` file at the root of the project. After changing it in production, run `php artisan config:cache` again so the new values are used.

> 🔒 Never commit `.env` to Git or share it: it holds your application key and passwords.

### Application

| Variable | What it does | Default | Production |
|----------|--------------|---------|------------|
| `APP_NAME` | Name shown in the sidebar, login page, printed sheets, e-mails and public portal | `Laravel` | Your platform's name, e.g. `"Multi Shelter Manager"` |
| `APP_ENV` | Environment the app runs in | `local` | `production` |
| `APP_KEY` | Secret key used to encrypt sessions and data. Generated by `php artisan key:generate` | *(empty)* | Generate once and **never change or lose it**: existing sessions and encrypted data become unreadable |
| `APP_DEBUG` | Show detailed error pages | `true` | **`false`**. Debug pages can reveal passwords and other secrets |
| `APP_URL` | Public address of the application. Used in e-mail links (invitations, password resets) and image URLs | `http://localhost:8000` | Your real address, e.g. `https://shelters.example.org` |
| `APP_LOCALE` | Interface language: `en`, `pt`, `es`, `fr`, `de`, `it`, `nl`, `pl`, `sv` or `da` | `en` | Your language |
| `APP_FALLBACK_LOCALE` | Language used when a text has no translation in `APP_LOCALE` | `en` | `en` |
| `APP_FAKER_LOCALE` | Language of the fake data generated by tests and seeders | `en_US` | Leave as is |
| `APP_MAINTENANCE_DRIVER` | Where the maintenance-mode flag (`php artisan down`) is stored | `file` | `file`. Use `cache` if you run several servers |
| `BCRYPT_ROUNDS` | Password hashing strength | `12` | Leave as is |

### Multi Shelter Manager settings

These variables are specific to this application.

| Variable | What it does | Default | Production |
|----------|--------------|---------|------------|
| `PUBLIC_PORTAL_ENABLED` | `true` shows the public adoption website on the home page ([chapter 13](13-public-portal.md)). `false` makes the app back-office only: the home page redirects to the login page | `false` | Your choice |
| `PRIVACY_CONTACT_EMAIL` | Contact e-mail shown on the **Privacy Policy** page for data-protection requests | Falls back to `MAIL_FROM_ADDRESS` | The e-mail of the person responsible for data protection |

### Database

| Variable | What it does | Default | Production |
|----------|--------------|---------|------------|
| `DB_CONNECTION` | Database type: `mysql`, `mariadb`, `pgsql` or `sqlite` | `sqlite` | `mysql` or `mariadb` recommended |
| `DB_HOST` | Database server address | `127.0.0.1` | Your database server |
| `DB_PORT` | Database server port | `3306` | Your database port |
| `DB_DATABASE` | Database name (for SQLite: path to the file, defaults to `database/database.sqlite`) | `laravel` | e.g. `multishelter_manager` |
| `DB_USERNAME` | Database user | `root` | A dedicated user, **not** `root` |
| `DB_PASSWORD` | Database password | *(empty)* | A strong password |

### E-mail

E-mail is used for user invitations, password resets and the daily vaccination reminders.

| Variable | What it does | Default | Production |
|----------|--------------|---------|------------|
| `MAIL_MAILER` | How e-mails are sent: `smtp`, `ses`, `postmark`, `resend`, `sendmail`, or `log` (write to `storage/logs/laravel.log` instead of sending) | `log` | `smtp` (or your provider's driver) |
| `MAIL_SCHEME` | `smtps` for implicit TLS (usually port 465). Leave `null` for STARTTLS (port 587) | `null` | Depends on your provider |
| `MAIL_HOST` | SMTP server | `127.0.0.1` | Your provider's SMTP server |
| `MAIL_PORT` | SMTP port | `2525` | Usually `587` or `465` |
| `MAIL_USERNAME` | SMTP user | `null` | From your provider |
| `MAIL_PASSWORD` | SMTP password | `null` | From your provider |
| `MAIL_FROM_ADDRESS` | Sender address of every e-mail | `hello@example.com` | e.g. `no-reply@your-domain.org` |
| `MAIL_FROM_NAME` | Sender name | `${APP_NAME}` | Leave as is |

### File storage (photos and logos)

Pet photos, volunteer photos and shelter logos are stored on the **default disk**.

| Variable | What it does | Default | Production |
|----------|--------------|---------|------------|
| `FILESYSTEM_DISK` | `public` stores files on the server in `storage/app/public` (requires `php artisan storage:link`). `s3` stores them in an Amazon S3 (or S3-compatible) bucket | `public` | `public` for a single server, `s3` for cloud hosting |
| `AWS_ACCESS_KEY_ID` | S3 access key (only when `FILESYSTEM_DISK=s3`) | *(empty)* | From your cloud provider |
| `AWS_SECRET_ACCESS_KEY` | S3 secret key | *(empty)* | From your cloud provider |
| `AWS_DEFAULT_REGION` | S3 region | `us-east-1` | Your bucket's region |
| `AWS_BUCKET` | Bucket name. Its files must be publicly readable so photos can be displayed | *(empty)* | Your bucket |
| `AWS_URL` | Public base URL of the bucket or CDN (optional) | *(empty)* | Only if you use a CDN or custom domain |
| `AWS_ENDPOINT` | Endpoint of an S3-compatible service such as DigitalOcean Spaces, Cloudflare R2 or MinIO (optional) | *(empty)* | Only for non-Amazon services |
| `AWS_USE_PATH_STYLE_ENDPOINT` | Use path-style URLs, required by some S3-compatible services such as MinIO | `false` | Usually `false` |

> The demo seeder always saves its photos to the `public` disk.

### Sessions, cache and queue

The defaults use the database, so no extra services are needed. You don't need to change these unless you have a specific reason.

| Variable | What it does | Default |
|----------|--------------|---------|
| `SESSION_DRIVER` | Where login sessions are stored (`database`, `file`, `redis`, …) | `database` |
| `SESSION_LIFETIME` | Minutes of inactivity before a user is logged out | `120` |
| `SESSION_ENCRYPT` | Encrypt session data | `false` |
| `SESSION_PATH` / `SESSION_DOMAIN` | Cookie path and domain | `/` / `null` |
| `CACHE_STORE` | Where cached data is stored (`database`, `file`, `redis`, …) | `database` |
| `CACHE_PREFIX` | Prefix for cache keys, useful when several apps share a cache server | *(empty)* |
| `QUEUE_CONNECTION` | Background job queue. E-mails are currently sent immediately, so no queue worker is needed | `database` |
| `BROADCAST_CONNECTION` | Real-time broadcasting (not used by the app) | `log` |
| `REDIS_CLIENT`, `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT` | Redis connection, only if you set a driver above to `redis` | `phpredis`, `127.0.0.1`, `null`, `6379` |
| `MEMCACHED_HOST` | Memcached server, only if `CACHE_STORE=memcached` | `127.0.0.1` |

### Logging

| Variable | What it does | Default | Production |
|----------|--------------|---------|------------|
| `LOG_CHANNEL` | Where errors are logged | `stack` | `stack` |
| `LOG_STACK` | Channels used by the stack: `single` writes one file, `daily` writes one file per day and deletes old ones | `single` | `daily` |
| `LOG_LEVEL` | Minimum level logged: `debug`, `info`, `warning`, `error`, … | `debug` | `warning` or `error` |
| `LOG_DEPRECATIONS_CHANNEL` | Where PHP deprecation warnings go | `null` | `null` |

### Front-end build

| Variable | What it does | Default |
|----------|--------------|---------|
| `VITE_APP_NAME` | App name available to the JavaScript build | `${APP_NAME}` |

### Example: production `.env`

```dotenv
APP_NAME="Multi Shelter Manager"
APP_ENV=production
APP_KEY=base64:...generated-by-key:generate...
APP_DEBUG=false
APP_URL=https://shelters.example.org

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

PUBLIC_PORTAL_ENABLED=true
PRIVACY_CONTACT_EMAIL=privacy@example.org

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multishelter_manager
DB_USERNAME=multishelter
DB_PASSWORD=a-long-random-password

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS="no-reply@example.org"
MAIL_FROM_NAME="${APP_NAME}"

FILESYSTEM_DISK=public

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning
```

After deploying, run these commands for best performance:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

[← Documentation index](README.md) · [Next: First run, login and roles →](02-first-run-and-roles.md)

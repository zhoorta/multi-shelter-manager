#!/bin/sh
# Prepares /data and, when the web server is starting, migrates the database, seeds the template
# shelter on the first run and starts the scheduler (it deletes expired sandboxes every ten
# minutes). Other commands (`docker run ... php artisan ...`) only get the directories.
set -eu

mkdir -p /data/storage/app/public /data/storage/framework/cache/data /data/storage/framework/sessions \
         /data/storage/framework/views /data/storage/logs /data/caddy/config /data/caddy/data
touch "${DB_DATABASE}"

if [ "${1:-}" = "frankenphp" ]; then
    if [ -z "${APP_KEY:-}" ]; then
        echo "APP_KEY is not set. Generate one with:" >&2
        echo "  docker compose -f docker-compose.demo.yml run --rm demo php artisan key:generate --show" >&2
        echo "and put it in demo.env." >&2
        exit 1
    fi

    php artisan migrate --force --no-interaction
    php artisan demo:prepare --no-interaction
    php artisan optimize --no-interaction

    php artisan schedule:work >/proc/1/fd/1 2>&1 &
fi

exec "$@"

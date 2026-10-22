# syntax=docker/dockerfile:1

# Public demo image (DEMO_MODE=true): FrankenPHP with a SQLite database in /data. On first start
# it seeds the template shelter, and every visitor gets a throwaway copy of it.

FROM dunglas/frankenphp:1-php8.5-bookworm AS base

RUN install-php-extensions gd exif zip intl bcmath pcntl opcache pdo_sqlite \
    && apt-get update \
    && apt-get install -y --no-install-recommends curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

FROM base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
# Dev dependencies stay: the demo seeder uses Faker (fake()).
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-interaction

# The CSS build scans views from vendor/ (Flux) for classes, so it needs the PHP dependencies.
FROM node:22-bookworm-slim AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

FROM base AS app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs bootstrap/cache \
    && composer dump-autoload --optimize --no-interaction \
    && rm -rf /usr/bin/composer \
    && mkdir -p /data /app/bootstrap/cache \
    && ln -s /data/storage/app/public /app/public/storage \
    && chown -R www-data:www-data /data /app/bootstrap/cache

COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-demo.ini
COPY docker/entrypoint.sh /usr/local/bin/demo-entrypoint
RUN chmod +x /usr/local/bin/demo-entrypoint

ENV APP_ENV=production \
    APP_DEBUG=false \
    DEMO_MODE=true \
    PUBLIC_PORTAL_ENABLED=false \
    MAIL_MAILER=log \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=warning \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/data/database.sqlite \
    LARAVEL_STORAGE_PATH=/data/storage \
    SESSION_DRIVER=database \
    CACHE_STORE=database \
    QUEUE_CONNECTION=sync \
    SERVER_NAME=:8080 \
    XDG_CONFIG_HOME=/data/caddy/config \
    XDG_DATA_HOME=/data/caddy/data

VOLUME /data
EXPOSE 8080

USER www-data

HEALTHCHECK --interval=30s --timeout=5s --start-period=120s --retries=3 \
    CMD curl -fsS http://127.0.0.1:8080/up >/dev/null || exit 1

ENTRYPOINT ["demo-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

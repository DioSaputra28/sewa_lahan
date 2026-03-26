#!/usr/bin/env sh

set -eu

cd /var/www/html

mkdir -p \
    bootstrap/cache \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public

chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    php artisan storage:link --no-interaction >/dev/null 2>&1 || true
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction || true
fi

php artisan package:discover --ansi >/dev/null 2>&1 || true
php artisan config:cache --ansi >/dev/null 2>&1 || true
php artisan route:cache --ansi >/dev/null 2>&1 || true
php artisan view:cache --ansi >/dev/null 2>&1 || true

exec "$@"

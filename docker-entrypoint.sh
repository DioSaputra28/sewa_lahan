#!/usr/bin/env sh

set -eu

cd /var/www/html

mkdir -p \
    bootstrap/cache \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    php artisan storage:link >/dev/null 2>&1 || true
fi

php artisan package:discover --ansi >/dev/null 2>&1 || true
php artisan config:cache --ansi >/dev/null 2>&1 || true
php artisan route:cache --ansi >/dev/null 2>&1 || true
php artisan view:cache --ansi >/dev/null 2>&1 || true

exec "$@"

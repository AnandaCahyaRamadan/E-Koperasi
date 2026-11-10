#!/bin/sh
set -e

# Ensure storage and bootstrap/cache permissions
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/app/public \
         /var/www/bootstrap/cache

chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true

exec "$@"


#!/bin/sh
set -e

# ==============================================================================
# STEPUP - Production Entrypoint Script
# Prepares storage directories, connects symlink, waits for database,
# runs database migrations, generates swagger documentation, and caches routes/config.
# ==============================================================================

echo "=== [STEPUP] Initializing production container runtime ==="

# 1. Pastikan folder penyimpanan dan cache Laravel tersedia
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/bootstrap/cache

# 2. Hubungkan symlink storage jika belum ada
if [ ! -L /var/www/html/public/storage ] && [ ! -d /var/www/html/public/storage ]; then
    echo "=> Connecting storage symlink..."
    php artisan storage:link || true
fi

# 3. Tunggu koneksi database MySQL siap menerima request
if [ "$DB_CONNECTION" = "mysql" ] && [ -n "$DB_HOST" ]; then
    echo "=> Waiting for MySQL ($DB_HOST:${DB_PORT:-3306}) to be ready..."
    until nc -z -v -w3 "$DB_HOST" "${DB_PORT:-3306}" 2>/dev/null; do
        sleep 1
    done
    echo "=> MySQL is ready!"
fi

# 4. Jalankan migrasi dan optimasi hanya jika proses utama adalah php-fpm
if [ "$1" = "php-fpm" ]; then
    echo "=> Checking and running database migrations..."
    php artisan migrate --force || echo "=> Database migrations already applied or failed."

    # Generate Swagger / OpenAPI documentation
    echo "=> Generating OpenAPI / Swagger documentation..."
    php artisan l5-swagger:generate || true

    # Cache config, route, view jika APP_KEY tersedia
    if [ -n "$APP_KEY" ]; then
        echo "=> Caching routes, configs, and views for production..."
        php artisan config:cache || true
        php artisan route:cache || true
        php artisan view:cache || true
    fi
fi

echo "=> Executing application command: $@"
exec "$@"

#!/bin/sh
set -e

# ==============================================================================
# STEPUP - Production Entrypoint Script
# Prepares storage directories, connects symlink, waits for database,
# runs database migrations, auto-seeds empty database, generates swagger, and caches config.
# ==============================================================================

echo "=== [STEPUP] Initializing production container runtime ==="

# 1. Pastikan folder penyimpanan dan cache Laravel tersedia
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public/products \
         /var/www/html/bootstrap/cache

# Salin gambar produk bawaan dari data_shoes ke folder storage jika belum ada
if [ -d /var/www/html/data_shoes/images ]; then
    cp -n /var/www/html/data_shoes/images/* /var/www/html/storage/app/public/products/ 2>/dev/null || true
fi

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

# 4. Jalankan migrasi, seeder otomatis, dan optimasi cache
if [ "$1" = "php-fpm" ]; then
    echo "=> Checking and running database migrations..."
    php artisan migrate --force || echo "=> Database migrations already applied or failed."

    # Cek apakah database masih kosong, jika ya jalankan seeder otomatis
    echo "=> Checking footwear catalog count..."
    PRODUCT_COUNT=$(php artisan tinker --execute="echo App\Models\Product::count();" 2>/dev/null | tr -cd '0-9' || echo "0")
    if [ "$PRODUCT_COUNT" = "0" ] || [ -z "$PRODUCT_COUNT" ]; then
        echo "=> Fresh database detected (0 products). Automatically running seeders..."
        php artisan db:seed --force || echo "=> Database seeding failed or skipped."
    else
        echo "=> Database contains $PRODUCT_COUNT products. Synchronizing default footwear details..."
        php artisan db:seed --class=ProductSeeder --force || echo "=> Footwear detail sync skipped."
    fi

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

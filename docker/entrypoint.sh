#!/bin/sh
set -e

echo "Waiting for MySQL database connection..."
until php -r "try { new PDO('mysql:host=' . (getenv('DB_HOST') ?: 'mysql') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . (getenv('DB_DATABASE') ?: 'stepup'), getenv('DB_USERNAME') ?: 'stepup', getenv('DB_PASSWORD') ?: 'stepup'); exit(0); } catch (Exception \$e) { exit(1); }"; do
    echo "MySQL not ready yet, retrying in 2 seconds..."
    sleep 2
done
echo "MySQL is ready and connected!"

# Ensure storage link exists with relative target
if [ ! -d public/storage ]; then
    rm -f public/storage 2>/dev/null || true
    ln -s ../storage/app/public public/storage 2>/dev/null || php artisan storage:link || true
fi

# Run migrations and seeders
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction || true

echo "Starting STEPUP Laravel server on http://0.0.0.0:8000..."
exec php artisan serve --host=0.0.0.0 --port=8000 --no-reload

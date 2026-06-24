#!/usr/bin/env bash
echo "Menjalankan Composer..."
composer install --no-dev --working-dir=/var/www/html

echo "Mengoptimalkan Cache Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Menjalankan Migrasi Database ke Supabase..."
php artisan migrate --force
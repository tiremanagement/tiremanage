#!/bin/bash
set -e

APP_DIR=/var/www/tiremanage
cd $APP_DIR

git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force || true

php artisan optimize:clear
php artisan optimize

chown -R dpd:nginx storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

systemctl restart php-fpm
systemctl restart nginx

echo "Deploy completed at $(date) ✅"

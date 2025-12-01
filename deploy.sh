#!/bin/bash
set -e

cd /var/www/tiremanage

composer install --no-dev --optimize-autoloader

# migrations (if database migation force don't the configuration stop
php artisan migrate --force || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

# web servers need foldeer permissions
chown -R nginx:nginx storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

systemctl restart php-fpm
systemctl reload nginx

echo "Deploy completed ✅"

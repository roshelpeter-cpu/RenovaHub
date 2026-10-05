#!/bin/sh
set -eu

cd /var/www/html

export PORT="${PORT:-10000}"

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/laravel.conf

chown -R www-data:www-data storage bootstrap/cache

php artisan package:discover --ansi
php artisan storage:link

# Closure routes in routes/web.php and routes/api.php cannot be serialized.
# Optimize still caches config, events, and views. Route cache is cleared
# when route:cache refuses those closures, so the existing routes keep working.
if ! php artisan optimize; then
    php artisan route:clear
    php artisan config:cache
    php artisan event:cache
    php artisan view:cache
fi

php artisan migrate --force

php-fpm -D
exec nginx -g 'daemon off;'

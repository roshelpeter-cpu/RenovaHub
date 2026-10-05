# RenovaHub on Render. PHP 8.3-FPM and Nginx listen on port 10000.
# composer.json declares PHP ^8.4; locked packages accept 8.3, so the
# platform check is ignored and no application code is changed.

FROM node:22-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM php:8.3-fpm-bookworm

ENV PORT=10000 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        ca-certificates \
        curl \
        unzip \
        git \
        gettext-base \
        libicu-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        intl \
        zip \
        exif \
        opcache \
    && rm -rf /var/lib/apt/lists/* \
    && php -m | grep -qi '^pdo_mysql$' \
    && php -m | grep -qi '^mbstring$' \
    && php -m | grep -qi '^bcmath$' \
    && php -m | grep -qi '^intl$' \
    && php -m | grep -qi '^xml$' \
    && php -m | grep -qi '^zip$' \
    && php -m | grep -qi '^exif$' \
    && php -m | grep -qi '^fileinfo$' \
    && php -m | grep -qi 'Zend OPcache'

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --no-scripts \
        --ignore-platform-req=php

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && rm -f /etc/nginx/sites-enabled/default

COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]

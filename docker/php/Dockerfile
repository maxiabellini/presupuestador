FROM php:8.4-fpm-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends git libicu-dev libpq-dev libzip-dev unzip \
    && docker-php-ext-install -j$(nproc) intl pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY php.ini /usr/local/etc/php/conf.d/app.ini
WORKDIR /application

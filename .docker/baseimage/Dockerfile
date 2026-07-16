FROM dunglas/frankenphp:php8.4-alpine

RUN apk add --no-cache supervisor \
    && install-php-extensions \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        opcache \
        gd \
        zip \
        bcmath \
        intl \
        pcntl \
        exif

WORKDIR /app

RUN mkdir -p bootstrap/cache \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs

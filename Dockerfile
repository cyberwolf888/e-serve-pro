# ─────────────────────────────────────────────────────────────────────────────
# Stage 1: Build frontend assets
FROM node:20-alpine AS build-assets

WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
COPY resources/ resources/

RUN npm ci && npm run build

# Stage 2: Install production PHP dependencies
FROM composer:2 AS build-deps

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY app/        app/
COPY bootstrap/  bootstrap/
COPY database/   database/

RUN composer dump-autoload --optimize --no-interaction --no-scripts

# Stage 3: Production image
FROM xyndr0me/pro-bi-smart-base:0.1.0 AS production

LABEL maintainer="Hendra Wijaya <info@imadehendrawijaya.com>"

# Copy application source.
COPY . .

# Copy production dependencies and Vite assets.
COPY --from=build-deps /app/vendor vendor/
COPY --from=build-assets /app/public/build public/build

# Precompress static Metronic/app assets.
RUN find /app/public/assets -type f \( -name "*.js" -o -name "*.css" \) -print0 \
    | xargs -0 -I{} gzip -k -f -9 {}

COPY .docker/Caddyfile         /etc/caddy/Caddyfile
COPY .docker/supervisord.conf  /etc/supervisord.conf
COPY .docker/php/php.ini       /usr/local/etc/php/conf.d/app-php.ini

COPY .docker/start.sh /start.sh
RUN chmod +x /start.sh

ENV SERVER_NAME=":80"

CMD ["/start.sh"]

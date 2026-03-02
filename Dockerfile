# 1) Build frontend assets (Vite)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY . .
RUN npm run build

# 2) PHP runtime
FROM php:8.3-cli
WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev zlib1g-dev libicu-dev \
  && docker-php-ext-configure intl \
  && docker-php-ext-install intl zip pdo pdo_mysql \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
COPY --from=assets /app/public/build /app/public/build

# VAŽNO: bez --no-scripts (da bi package discovery radio)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Sigurno “zakucaj” discovery (ako ikad bude build cache čudan)
RUN php artisan package:discover --ansi || true

RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# IMPORTANT: koristi public/index.php kao router za php -S (radi i bez server.php)
CMD sh -lc "\
  php artisan optimize:clear || true && \
  php artisan migrate --force || true && \
  php artisan storage:link || true && \
  php -S 0.0.0.0:${PORT:-8080} -t public public/index.php \
"
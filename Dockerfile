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

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App
COPY . .
COPY --from=assets /app/public/build /app/public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Writable dirs
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# IMPORTANT: slušaj Railway port
CMD sh -lc 'php -S 0.0.0.0:${PORT:-8080} -t public'
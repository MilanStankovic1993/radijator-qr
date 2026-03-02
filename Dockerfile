# 1) Build frontend assets (Vite)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# 2) PHP + Composer deps
FROM php:8.3-cli AS app
WORKDIR /app

# System deps for intl + zip
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev zlib1g-dev \
    libicu-dev \
  && docker-php-ext-configure intl \
  && docker-php-ext-install intl zip pdo pdo_mysql \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App files
COPY . .

# Copy built assets from node stage
COPY --from=assets /app/public/build /app/public/build

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Laravel cache (neće srušiti build ako nema env-a još)
RUN php artisan config:cache || true && \
    php artisan route:cache || true && \
    php artisan view:cache || true

# Ensure writable dirs
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache && \
    chmod -R 777 storage bootstrap/cache

# Railway provides PORT
CMD sh -lc "php artisan migrate --force || true && php artisan storage:link || true && php -S 0.0.0.0:${PORT} -t public"
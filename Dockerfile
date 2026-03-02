# 1) Build frontend assets (Vite)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY . .
RUN npm run build

# 2) PHP + Composer deps
FROM php:8.3-cli AS app
WORKDIR /app

# System deps for intl + zip (+ sqlite file usage)
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
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Writable dirs + sqlite file (ako koristiš sqlite)
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache && \
    touch storage/database.sqlite && \
    chmod -R 777 storage bootstrap/cache && \
    chmod 666 storage/database.sqlite || true

# IMPORTANT: slušaj UVEK na 8080 (isti kao Railway Public Networking port)
CMD sh -lc "php artisan migrate --force || true; php artisan storage:link || true; php -S 0.0.0.0:8080 -t public"
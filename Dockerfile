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

# System deps + PHP extensions (intl, zip, pdo_mysql)
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

# PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN touch storage/database.sqlite && chmod 666 storage/database.sqlite || true
# Writable dirs
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Start
# - očisti cache (da se Filament rute sigurno registruju)
# - migracije
# - storage link
# - start PHP built-in server na Railway portu
CMD sh -lc "\
  echo PORT=\${PORT:-8080}; \
  php artisan optimize:clear || true; \
  php artisan migrate --force || true; \
  php artisan storage:link || true; \
  php -S 0.0.0.0:\${PORT:-8080} -t public \
"
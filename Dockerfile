# 1) PHP + Apache (stabilno za Laravel demo)
FROM php:8.2-apache

# 2) System deps + PHP extensions (intl + zip + pdo_mysql)
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql zip intl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# 3) Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4) App code
WORKDIR /var/www/html
COPY . .

# 5) Apache docroot -> /public
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# 6) Laravel perms
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# 7) Install PHP deps (prod) + build assets (ako ti treba Vite)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ako ti Vite nije bitan za demo, možeš ovo preskočiti.
# Ali pošto imaš package.json, odradićemo:
RUN if [ -f package.json ]; then \
      curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
      apt-get update && apt-get install -y nodejs && \
      npm ci && npm run build && \
      rm -rf node_modules; \
    fi

# 8) Expose & start
EXPOSE 80
CMD bash -lc "php artisan config:clear || true && php artisan migrate --force || true && apache2-foreground"
# syntax=docker/dockerfile:1

############################
# 1) Frontend assets build #
############################
FROM node:22-alpine AS assets

WORKDIR /app

COPY package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

COPY . .
RUN npm run build


########################
# 2) PHP vendor build  #
########################
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize --no-dev


#####################
# 3) Runtime image  #
#####################
FROM php:8.3-fpm-alpine

WORKDIR /app

# System packages + PHP extensions
RUN apk add --no-cache \
    nginx \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    git \
    unzip \
    zip \
    curl \
    freetype \
    libjpeg-turbo \
    libpng \
    su-exec \
 && apk add --no-cache --virtual .build-deps \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
 && docker-php-ext-configure intl \
 && docker-php-ext-install intl pdo pdo_mysql zip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd \
 && apk del .build-deps \
 && rm -rf /var/cache/apk/*

# Optional but useful in production
RUN docker-php-ext-enable opcache

# Copy application
COPY . .

# Copy vendor and built assets from build stages
COPY --from=vendor /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build

# Remove default nginx site
RUN rm -f /etc/nginx/http.d/default.conf

# Create runtime dirs and set permissions
RUN mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/logs \
    /app/bootstrap/cache \
 && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
 && chmod -R 775 /app/storage /app/bootstrap/cache

# Start script
RUN cat > /start.sh <<'SH'
#!/usr/bin/env sh
set -eu

PORT="${PORT:-8080}"

cat > /etc/nginx/http.d/app.conf <<EOF
server {
    listen 0.0.0.0:${PORT};
    server_name _;
    root /app/public;
    index index.php;

    client_max_body_size 50M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ^~ /livewire/ {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_read_timeout 300;
    }

    location ~* \.(?:css|js|mjs|jpg|jpeg|gif|png|svg|ico|webp|woff|woff2|ttf)$ {
        try_files \$uri =404;
        expires 7d;
        add_header Cache-Control "public, max-age=604800";
        access_log off;
    }
}
EOF

# Only safe runtime prep
mkdir -p /app/storage/framework/cache \
         /app/storage/framework/sessions \
         /app/storage/framework/views \
         /app/storage/logs \
         /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Safe commands only
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

php-fpm -D
exec nginx -g 'daemon off;'
SH

RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
# 1) Build frontend assets (Vite)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY . .
RUN npm run build

# 2) PHP deps (composer)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

# 3) Runtime: php-fpm + nginx
FROM php:8.3-fpm-alpine
WORKDIR /app

# system deps + php extensions
RUN apk add --no-cache nginx bash icu-dev libzip-dev oniguruma-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql zip \
    && rm -rf /var/cache/apk/*

# copy app
COPY . .

# copy vendor + built assets
COPY --from=vendor /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build

# permissions
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

# nginx config
RUN rm -f /etc/nginx/http.d/default.conf
RUN printf '%s\n' \
'server {' \
'  listen 0.0.0.0:${PORT};' \
'  server_name _;' \
'  root /app/public;' \
'  index index.php;' \
'' \
'  location / {' \
'    try_files $uri $uri/ /index.php?$query_string;' \
'  }' \
'' \
'  location ~ \.php$ {' \
'    include fastcgi_params;' \
'    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' \
'    fastcgi_pass 127.0.0.1:9000;' \
'  }' \
'' \
'  location ~* \.(?:css|js|jpg|jpeg|gif|png|svg|ico|woff2?)$ {' \
'    expires 7d;' \
'    add_header Cache-Control "public, max-age=604800";' \
'    try_files $uri =404;' \
'  }' \
'}' \
> /etc/nginx/http.d/app.conf

# start script
RUN printf '%s\n' \
'#!/usr/bin/env sh' \
'set -e' \
'' \
'php artisan optimize:clear || true' \
'php artisan migrate --force || true' \
'php artisan storage:link || true' \
'' \
'# start php-fpm (background)' \
'php-fpm -D' \
'' \
'# start nginx (foreground)' \
"nginx -g 'daemon off;'" \
> /start.sh \
&& chmod +x /start.sh

CMD ["/start.sh"]
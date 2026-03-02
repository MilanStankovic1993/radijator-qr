# 1) Build frontend assets (Vite)
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY . .
RUN npm run build

# 2) Runtime (php-fpm + nginx)
FROM php:8.3-fpm-alpine
WORKDIR /app

RUN apk add --no-cache \
    nginx \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    git \
    unzip \
    zip \
  && docker-php-ext-configure intl \
  && docker-php-ext-install intl pdo pdo_mysql zip \
  && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY --from=assets /app/public/build /app/public/build

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache

RUN rm -f /etc/nginx/http.d/default.conf

# start script (OVDE JE FIX: \$PORT ostaje literalno u fajlu)
RUN printf '%s\n' \
'#!/usr/bin/env sh' \
'set -e' \
'' \
'PORT="${PORT:-8080}"' \
'' \
'cat > /etc/nginx/http.d/app.conf <<EOF' \
'server {' \
'  listen 0.0.0.0:'"\$PORT"';' \
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
'    try_files $uri =404;' \
'    expires 7d;' \
'    add_header Cache-Control "public, max-age=604800";' \
'  }' \
'}' \
'EOF' \
'' \
'php artisan optimize:clear || true' \
'php artisan migrate --force || true' \
'php artisan storage:link || true' \
'' \
'php-fpm -D' \
"nginx -g 'daemon off;'" \
> /start.sh \
&& chmod +x /start.sh

CMD ["/start.sh"]
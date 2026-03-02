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

# system deps + php extensions (UKLJUČUJE INTL)
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

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App
COPY . .

# Vendor
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Built assets
COPY --from=assets /app/public/build /app/public/build

# Remove default nginx config
RUN rm -f /etc/nginx/http.d/default.conf

# Start script
RUN cat > /start.sh <<'SH'
#!/usr/bin/env sh
set -e

PORT="${PORT:-8080}"

# --- PERMS: SQLite mora biti upisiv za www-data ---
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache

# ako koristiš sqlite u storage/
touch storage/database.sqlite

# dodeli prava www-data korisniku (php-fpm radi kao www-data)
chown -R www-data:www-data storage bootstrap/cache

# obavezno da folder + sqlite budu upisivi (SQLite pravi i -journal/-wal fajlove)
chmod -R 775 storage bootstrap/cache
chmod 664 storage/database.sqlite || true

# Nginx config:
# - /livewire/ mora kroz Laravel (inače 404 za livewire.js)
cat > /etc/nginx/http.d/app.conf <<EOF
server {
  listen 0.0.0.0:${PORT};
  server_name _;
  root /app/public;
  index index.php;

  location ^~ /livewire/ {
    try_files \$uri \$uri/ /index.php?\$query_string;
  }

  location / {
    try_files \$uri \$uri/ /index.php?\$query_string;
  }

  location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    fastcgi_pass 127.0.0.1:9000;
  }

  location ~* \.(?:css|js|jpg|jpeg|gif|png|svg|ico|woff2?)$ {
    try_files \$uri =404;
    expires 7d;
    add_header Cache-Control "public, max-age=604800";
  }
}
EOF

php artisan optimize:clear || true
php artisan migrate --force || true
php artisan db:seed --force || true
php artisan storage:link || true

php-fpm -D
exec nginx -g 'daemon off;'
SH

RUN chmod +x /start.sh

CMD ["/start.sh"]
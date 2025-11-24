# Dockerfile
FROM php:8.2-fpm

# system deps
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# cache composer install
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-progress

# copy app
COPY . .

# generate key & storage link (best effort)
RUN php artisan key:generate || true
RUN php artisan storage:link || true

# permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000

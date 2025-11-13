FROM php:8.2-fpm

# Install system dependencies & ekstensi PHP
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install gd pdo pdo_mysql zip intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy semua source code Laravel (termasuk artisan)
COPY . .

# Copy dan set permission untuk entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Jalankan composer install setelah semua file tersedia
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Set permissions untuk storage dan cache
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["docker-entrypoint.sh"]

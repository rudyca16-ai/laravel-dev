FROM php:8.3-fpm

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

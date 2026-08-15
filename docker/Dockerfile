FROM php:8.2-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

RUN apt-get update && apt-get install -y \
    make \
    gcc \
    g++ \
    curl \
    unzip \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    postgresql-client \ 
    bash \
    && docker-php-ext-install intl zip pdo pdo_pgsql pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer files first
COPY ./api/composer.json ./api/composer.lock ./

# Install dependencies
RUN composer install --no-scripts

# Copy application files
COPY ./api /app
COPY ./sync-db.sh /app

RUN mkdir -p /app/var /app/var/cache /app/var/log /app/var/cache/prod
RUN chown -R www-data:www-data /app/var

EXPOSE 9000

CMD ["/bin/bash","-c", "php-fpm"]
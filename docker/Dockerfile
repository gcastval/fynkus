FROM php:8.2-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

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
COPY ./api .
COPY ./sync-db.sh .

RUN mkdir -p /var /var/cache /var/log /var/cache/prod
RUN chmod -R 777 /var

EXPOSE 9000

CMD ["/bin/bash","-c", "./sync-db.sh && php-fpm"]
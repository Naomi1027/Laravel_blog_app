FROM php:8.2-fpm

ENV TZ Asia/Tokyo

RUN apt-get update && \
    apt-get install -y git unzip libzip-dev libicu-dev libonig-dev libpq-dev && \
    docker-php-ext-install intl pdo_pgsql pgsql zip bcmath

COPY --from=node:20.11.0 /usr/local/bin /usr/local/bin
COPY --from=node:20.11.0 /usr/local/lib /usr/local/lib
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

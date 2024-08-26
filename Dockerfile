FROM php:8.0-cli

RUN apt update && apt -y install \
        libzip-dev

RUN docker-php-ext-install  \
    zip \
    pcntl \
    sockets

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
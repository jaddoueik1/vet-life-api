# syntax=docker/dockerfile:1

FROM composer:2 AS vendor
WORKDIR /app
ARG APP_KEY=base64:placeholderplaceholderplaceholder==
ENV APP_KEY=${APP_KEY}
COPY . ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction

FROM php:8.2-cli
WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y \
        libzip-dev \
        unzip \
        git \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

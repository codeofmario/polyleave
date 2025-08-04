FROM composer:2.8 AS build
WORKDIR /app
COPY . .
RUN composer install --no-interaction --prefer-dist && \
    composer require firebase/php-jwt:^6

FROM php:8.3-cli
WORKDIR /app
RUN docker-php-ext-install pdo_mysql
COPY --from=build /app .
EXPOSE 8000
CMD ["php","artisan","serve","--host=0.0.0.0","--port=8000"]

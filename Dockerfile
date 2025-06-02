FROM php:8.2-fpm
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

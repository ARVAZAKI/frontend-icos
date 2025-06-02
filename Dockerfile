FROM php:8.2-cli

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies without dev packages
RUN composer install --no-dev --no-scripts --no-autoloader --no-progress

# Copy application code
COPY . .

# Complete the composer installation
RUN composer install --no-dev --optimize-autoloader --no-progress

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create startup script
RUN echo '#!/bin/bash\n\
# Clear all caches\n\
php artisan config:clear 2>/dev/null || true\n\
php artisan cache:clear 2>/dev/null || true\n\
php artisan route:clear 2>/dev/null || true\n\
php artisan view:clear 2>/dev/null || true\n\
\n\
# Create .env if not exists\n\
if [ ! -f .env ]; then\n\
    cp .env.example .env\n\
    php artisan key:generate --no-interaction\n\
fi\n\
\n\
# Ensure proper permissions\n\
chown -R www-data:www-data storage bootstrap/cache\n\
chmod -R 755 storage bootstrap/cache\n\
\n\
# Start Laravel server\n\
echo "Starting Laravel development server..."\n\
php artisan serve --host=0.0.0.0 --port=8000\n\
' > /usr/local/bin/start-laravel.sh \
    && chmod +x /usr/local/bin/start-laravel.sh

# Expose port
EXPOSE 8000

# Use the startup script
CMD ["/usr/local/bin/start-laravel.sh"]
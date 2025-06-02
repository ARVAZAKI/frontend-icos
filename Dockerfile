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

# Copy all files
COPY . .

# Install dependencies WITH dev packages (untuk dapat semua providers)
RUN composer install --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create startup script that handles the Pail issue
RUN echo '#!/bin/bash\n\
echo "Starting Laravel setup..."\n\
\n\
# Create .env if not exists\n\
if [ ! -f .env ]; then\n\
    echo "Creating .env file..."\n\
    cp .env.example .env\n\
fi\n\
\n\
# Clear all caches\n\
echo "Clearing caches..."\n\
php artisan config:clear 2>/dev/null || true\n\
php artisan cache:clear 2>/dev/null || true\n\
php artisan route:clear 2>/dev/null || true\n\
php artisan view:clear 2>/dev/null || true\n\
\n\
# Generate key if not exists\n\
if ! grep -q "APP_KEY=base64:" .env; then\n\
    echo "Generating application key..."\n\
    php artisan key:generate --no-interaction\n\
fi\n\
\n\
# Fix Pail issue by checking if package exists\n\
echo "Checking Laravel Pail..."\n\
if ! php -r "class_exists(\"Laravel\\\\Pail\\\\PailServiceProvider\");" 2>/dev/null; then\n\
    echo "Laravel Pail not found, installing..."\n\
    composer require laravel/pail --no-interaction 2>/dev/null || {\n\
        echo "Failed to install Pail, removing from config..."\n\
        sed -i "/Laravel\\\\\\\\Pail\\\\\\\\PailServiceProvider/d" config/app.php 2>/dev/null || true\n\
    }\n\
fi\n\
\n\
# Clear config cache again after potential changes\n\
php artisan config:clear 2>/dev/null || true\n\
\n\
# Test artisan command\n\
echo "Testing artisan..."\n\
php artisan --version\n\
\n\
# Ensure proper permissions\n\
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true\n\
chmod -R 755 storage bootstrap/cache 2>/dev/null || true\n\
\n\
# Start Laravel server\n\
echo "Starting Laravel development server on 0.0.0.0:8000..."\n\
php artisan serve --host=0.0.0.0 --port=8000\n\
' > /usr/local/bin/start-laravel.sh \
    && chmod +x /usr/local/bin/start-laravel.sh

# Expose port
EXPOSE 8000

# Use the startup script
CMD ["/usr/local/bin/start-laravel.sh"]
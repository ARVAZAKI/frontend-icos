# Gunakan image PHP resmi dengan PHP 8.2, versi CLI untuk pengembangan
FROM php:8.2-cli

# Set direktori kerja
WORKDIR /var/www/html

# Instal dependensi sistem dalam satu lapisan untuk mengurangi ukuran image
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        nodejs \
        npm; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Instal ekstensi PHP yang dibutuhkan Laravel
RUN docker-php-ext-install -j$(nproc) mbstring exif pcntl bcmath gd

# Instal Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Salin file Composer terlebih dahulu untuk caching
COPY composer.json composer.lock ./

# Instal dependensi PHP dengan opsi optimal
RUN set -eux; \
    composer install --no-dev --optimize-autoloader --no-interaction --verbose; \
    rm -rf ~/.composer/cache

# Salin semua file aplikasi
COPY . .

# Validasi file artisan dan set izin
RUN set -eux; \
    [ -f artisan ] || { echo "Artisan file not found!"; exit 1; }; \
    chmod +x artisan; \
    [ -f vendor/autoload.php ] || { echo "vendor/autoload.php not found!"; exit 1; }

# Instal dependensi Node.js dan bersihkan cache
RUN set -eux; \
    npm install --no-audit --no-fund; \
    npm cache clean --force

# Set izin untuk direktori Laravel
RUN set -eux; \
    chown -R www-data:www-data /var/www/html; \
    chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Ekspos port 8000 untuk php artisan serve
EXPOSE 8000

# Gunakan user non-root untuk keamanan
USER www-data

# Perintah untuk menjalankan server pengembangan Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
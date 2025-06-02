# Gunakan image PHP resmi dengan PHP 8.2
FROM php:8.2-fpm

# Set direktori kerja
WORKDIR /var/www/html

# Instal dependensi sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instal ekstensi PHP yang dibutuhkan Laravel
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

# Instal Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Salin semua file aplikasi
COPY . .

# Pastikan file artisan ada dan memiliki izin yang tepat
RUN if [ ! -f artisan ]; then echo "Artisan file not found!" && exit 1; fi \
    && chmod +x artisan

# Instal dependensi PHP tanpa mengabaikan skrip
RUN composer install --optimize-autoloader --no-dev --verbose

# Instal dependensi Node.js
RUN npm install

# Set izin
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Ekspos port 8000 untuk php artisan serve
EXPOSE 8000

# Perintah untuk menjalankan server pengembangan Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
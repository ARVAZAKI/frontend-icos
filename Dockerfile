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
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instal Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Salin semua file aplikasi
COPY . .

# Instal dependensi PHP dengan mengabaikan post-autoload-dump scripts
RUN composer install --optimize-autoloader --no-dev --no-scripts --verbose

# Jalankan post-autoload-dump scripts setelah semua file tersedia
RUN composer run-script post-autoload-dump

# Instal dependensi Node.js
RUN npm install

# Set izin
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Buat file database SQLite jika belum ada
RUN touch /var/www/html/database/database.sqlite

# Ekspos port 8000 untuk php artisan serve
EXPOSE 8000

# Perintah untuk menjalankan server pengembangan Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
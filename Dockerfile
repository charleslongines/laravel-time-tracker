FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Laravel project
WORKDIR /var/www/html
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Prepare storage & sqlite
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod -R 775 database storage bootstrap/cache

# Expose port (must match Railway’s $PORT)
EXPOSE 8080

# Run migrations on startup, then start Apache
CMD php artisan migrate --force && apache2-foreground

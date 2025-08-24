# Use official PHP with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    zip \
    git \
    curl \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Cache Laravel config/routes/views
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Run migrations and seed database
RUN php artisan migrate:refresh --force && php artisan db:seed --force

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]

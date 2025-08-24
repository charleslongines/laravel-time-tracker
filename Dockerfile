FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev unzip git sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 775 storage bootstrap/cache database || true

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# For local testing; Railway uses $PORT
EXPOSE 8080

CMD ["/entrypoint.sh"]


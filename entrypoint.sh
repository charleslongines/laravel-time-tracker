#!/bin/bash
set -e

# Make Apache listen on Railway's assigned $PORT
sed -i "s/80/${PORT}/g" /etc/apache2/sites-available/000-default.conf
sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf

# Ensure SQLite DB file exists
touch /var/www/html/database/database.sqlite

# Run migrations (don’t crash if they fail)
php artisan migrate --force || true

# Start Apache
exec apache2-foreground

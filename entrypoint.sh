#!/bin/bash
set -e

# Ensure Apache listens on Railway's $PORT
sed -i "s/80/${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Ensure SQLite DB file exists
touch /var/www/html/database/database.sqlite

# Run migrations (don't crash if fail)
php artisan migrate --force || true

# Start Apache
apache2-foreground

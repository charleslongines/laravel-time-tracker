#!/bin/bash
set -e

# Ensure SQLite DB file exists
touch /var/www/html/database/database.sqlite

# Run migrations, but don't crash container if they fail
php artisan migrate --force || true

# Start Apache (uses Railway's $PORT)
apache2-foreground

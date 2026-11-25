#!/bin/sh
set -e

cd /var/www/html

# Lien public vers le stockage (photos locales éventuelles)
php artisan storage:link --force || true

# Migrations de la base (Aiven)
php artisan migrate --force

# Optimisations de production
php artisan config:cache
php artisan view:cache

# Apache écoute sur le port fourni par Render ($PORT)
exec apache2-foreground
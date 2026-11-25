
#!/bin/sh

set -e

# Port fourni par Render
PORT="${PORT:-10000}"

# Adapter Apache au port de Render
sed -i "s/10000/${PORT}/g" \
    /etc/apache2/ports.conf \
    /etc/apache2/sites-available/000-default.conf

# Créer le lien symbolique du stockage public
php artisan storage:link --force || true

# Préparer les configurations Laravel
php artisan config:cache

echo "AutoSur.sn est prêt à démarrer sur le port ${PORT}"

exec "$@"

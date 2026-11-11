#!/bin/sh
set -e

cd /var/www

# Base de datos: aplica migraciones pendientes
php artisan migrate --force

# Cache de configuración con las variables del entorno
php artisan config:cache
php artisan route:cache || true

# Permisos en tiempo de ejecución
chown -R www-data:www-data storage bootstrap/cache database || true

exec apache2-foreground

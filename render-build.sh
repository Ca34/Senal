#!/usr/bin/env bash
# exit on error
set -e

composer install --no-dev --optimize-autoloader

# Crear la base de datos SQLite si no existe
mkdir -p database
touch database/database.sqlite

# Migraciones y Seeders (Limpiar y volver a llenar para asegurar datos frescos)
php artisan migrate:fresh --seed --force

# Dar permisos de escritura a las carpetas de almacenamiento y caché
chmod -R 775 storage bootstrap/cache

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache


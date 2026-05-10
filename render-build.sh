#!/usr/bin/env bash
# exit on error
set -e

composer install --no-dev --optimize-autoloader

# Crear la base de datos SQLite si no existe
mkdir -p database
touch database/database.sqlite

# Migraciones y Seeders (Limpiar y volver a llenar para asegurar datos frescos)
php artisan migrate:fresh --seed --force

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

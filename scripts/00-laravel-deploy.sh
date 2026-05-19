#!/usr/bin/env bash

# Crear la base de datos SQLite si no existe en /var/www/html/database
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chmod -R 777 /var/www/html/database

# Instalar dependencias de Composer para producción
echo "Running Composer..."
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

# Migraciones y Seeders (Limpiar y volver a rellenar)
echo "Running Migrations & Seeders..."
php artisan migrate:fresh --seed --force

# Dar permisos a las carpetas de almacenamiento y caché
echo "Setting permissions..."
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Optimizar caches de Laravel
echo "Optimizing Laravel cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment tasks finished successfully!"

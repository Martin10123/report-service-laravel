#!/bin/bash
set -e

echo "🚀 Iniciando aplicación Laravel..."

# Asegurar permisos correctos
echo "📁 Configurando permisos..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Limpiar caches antiguos
echo "🧹 Limpiando caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Generar APP_KEY si no existe
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force
fi

# Crear directorio de base de datos si no existe (para SQLite)
echo "💾 Preparando base de datos..."
mkdir -p database
touch database/database.sqlite || true
chmod 664 database/database.sqlite || true

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️ Migraciones fallaron o ya están actualizadas"

# Cachear configuración en producción
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizando para producción..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Iniciar servidor
echo "✅ Iniciando servidor en puerto $PORT..."
exec php -S 0.0.0.0:$PORT -t public

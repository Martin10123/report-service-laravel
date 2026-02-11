#!/bin/bash
set -e

echo "🚀 Iniciando aplicación Laravel..."

# Permisos seguros
echo "📁 Configurando permisos..."
chmod -R 777 storage bootstrap/cache

# Limpiar caches
echo "🧹 Limpiando caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Mostrar config útil
echo "📊 Estado del entorno:"
echo "APP_ENV: $APP_ENV"
echo "APP_DEBUG: $APP_DEBUG"
echo "DB_CONNECTION: $DB_CONNECTION"
if [ -n "$DATABASE_URL" ]; then
    echo "DATABASE_URL: ✓ Configurado"
else
    echo "DATABASE_URL: ✗ No configurado"
    echo "DB_HOST: $DB_HOST"
fi

# Verificar conexión a base de datos
echo "🔍 Verificando conexión a base de datos..."
php artisan db:show 2>&1 || echo "⚠️ No se pudo verificar la conexión"

# Migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️ Migraciones fallaron"

# Seeders
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force || echo "⚠️ Seeders fallaron o ya ejecutados"

# Optimización (solo si todo funciona)
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizando..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Iniciar servidor
echo "✅ Servidor en puerto ${PORT:-10000}"
exec php -S 0.0.0.0:${PORT:-10000} -t public

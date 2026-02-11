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
echo "DB_HOST: $DB_HOST"
echo "DATABASE_URL: $([ -n "$DATABASE_URL" ] && echo 'Sí' || echo 'No')"

# Migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️ Migraciones fallaron"

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

FROM php:8.3-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    zip \
    nodejs \
    npm \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install zip gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar proyecto
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Cache Laravel
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 10000

CMD php -S 0.0.0.0:$PORT -t public

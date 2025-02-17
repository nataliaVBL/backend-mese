FROM php:8.4-fpm

# Instalar extensões e dependências
RUN apt-get update && apt-get install -y \
    zip unzip git curl \
    libpq-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do Laravel
COPY . .

# Instalar dependências do Laravel
RUN composer install && chmod -R 777 storage bootstrap/cache

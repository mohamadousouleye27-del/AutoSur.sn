# ==========================================
# ETAPE 1 : Compilation des assets frontend
# ==========================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

COPY . .
RUN npm run build


# ==========================================
# ETAPE 2 : Application Laravel
# ==========================================
FROM php:8.5-apache

# Dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP (mbstring, curl et OPcache sont déjà fournis)
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl

# Modules Apache
RUN a2enmod rewrite headers

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Projet
COPY . .

# Assets compilés par Vite
COPY --from=frontend /app/public/build ./public/build

# Dépendances PHP de production
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Dossiers Laravel
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configuration Apache
# Configuration Apache générée ici (évite les problèmes d'encodage des fichiers Windows)
ENV PORT=10000
RUN printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername \
    && printf 'Listen ${PORT}\n' > /etc/apache2/ports.conf \
    && printf '<VirtualHost *:${PORT}>\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        AllowOverride All\n        Require all granted\n        Options -Indexes +FollowSymLinks\n    </Directory>\n    ErrorLog /proc/self/fd/2\n    CustomLog /proc/self/fd/1 combined\n</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf
    

# Script de démarrage : fins de ligne Windows supprimées, lancé via sh
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/bin/sh", "/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
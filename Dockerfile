# Utiliser une image de base PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application dans le conteneur
COPY . /var/www/html

# Installer les dépendances PHP
RUN composer install

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/logs \
    && chmod -R 777 storage \
    && chmod -R 777 bootstrap/cache

# Exposer le port 80
EXPOSE 80

# Exécuter les commandes supplémentaires
RUN composer dump-autoload \
    && rm -f /var/www/html/.env \
    && cp /var/www/html/.env.example /var/www/html/.env \
    && php artisan key:generate

# Démarrer le serveur
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]